<?php

declare(strict_types=1);

namespace App\Shared\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

/**
 * Login rate limiter to prevent brute force attacks
 * Fixed version that doesn't throw problematic exceptions
 */
readonly class LoginRateLimiterSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RateLimiterFactory $loginLimiter,
        private ?LoggerInterface $logger = null
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Standard Symfony Security events
            LoginSuccessEvent::class => ['onLoginSuccess', 0],
            LoginFailureEvent::class => ['onLoginFailure', 0],

            // Additional check on request for login forms (safer approach)
            KernelEvents::REQUEST => ['onRequest', 10],
        ];
    }

    /**
     * Check rate limit on login form submissions (works for Sonata)
     */
    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        // Check if this is a login form submission
        if (!$this->isLoginFormSubmission($request)) {
            return;
        }

        $clientIp = $request->getClientIp() ?? 'unknown';

        // Create rate limiter for this IP
        $limiter = $this->loginLimiter->create('form_login_' . $clientIp);
        $consumption = $limiter->consume();

        // Check if limit is exceeded
        if (!$consumption->isAccepted()) {
            $this->logger?->warning('Form login rate limit exceeded', [
                'ip' => $clientIp,
                'path' => $request->getPathInfo()
            ]);

            // Calculate retry after in seconds from now
            $retryAfter = $consumption->getRetryAfter();
            $retryAfterSeconds = $retryAfter->getTimestamp() - time();

            // Handle different types of login endpoints
            if ($this->isApiLoginRequest($request)) {
                $this->handleApiRateLimit($event, $retryAfterSeconds);
            } elseif (str_contains($request->getPathInfo(), '/admin/')) {
                $this->handleSonataRateLimit($event, $retryAfterSeconds);
            } else {
                // For other forms, also redirect instead of throwing exception
                $this->handleGenericRateLimit($event, $request, $retryAfterSeconds);
            }
        } else {
            // Get remaining tokens from the consumption object
            $remainingTokens = $consumption->getRemainingTokens();
            $this->logger?->info('Form login rate limit check passed', [
                'ip' => $clientIp,
                'remaining_tokens' => $remainingTokens
            ]);
        }
    }

    /**
     * Reset rate limiter on successful login
     */
    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $clientIp = $event->getRequest()->getClientIp() ?? 'unknown';

        // Reset the rate limiter for this IP on successful login
        $limiter = $this->loginLimiter->create('form_login_' . $clientIp);
        $limiter->reset();

        $this->logger?->info('Form login rate limit reset after successful login', [
            'ip' => $clientIp
        ]);
    }

    /**
     * Log failed login attempts
     */
    public function onLoginFailure(LoginFailureEvent $event): void
    {
        $clientIp = $event->getRequest()->getClientIp() ?? 'unknown';

        // Rate limiter has already been consumed in onRequest
        // Just log the failure for monitoring purposes
        $this->logger?->notice('Form login failed', [
            'ip' => $clientIp,
            'path' => $event->getRequest()->getPathInfo()
        ]);
    }

    /**
     * Check if this is an API login request (should return JSON)
     */
    private function isApiLoginRequest(Request $request): bool
    {
        $path = $request->getPathInfo();

        // API login endpoints that should return JSON
        if (
            $path === '/api/login_check' ||
            str_starts_with($path, '/api/') ||
            $request->headers->get('Content-Type') === 'application/json' ||
            $request->headers->get('Accept') === 'application/json'
        ) {
            return true;
        }

        return false;
    }

    /**
     * Handle rate limiting for API endpoints (JSON response)
     */
    private function handleApiRateLimit(RequestEvent $event, int $retryAfterSeconds): void
    {
        $response = new JsonResponse([
            'code' => 429,
            'message' => 'Too many login attempts. Please try again later.',
            'retry_after' => $retryAfterSeconds
        ], 429);

        // Add rate limiting headers
        $response->headers->set('X-RateLimit-Limit', '5');
        $response->headers->set('X-RateLimit-Window', '15 minutes');
        $response->headers->set('Retry-After', (string) $retryAfterSeconds);

        $event->setResponse($response);
    }

    /**
     * Check if this is a login form submission
     */
    private function isLoginFormSubmission(Request $request): bool
    {
        // POST request to login endpoints
        if ($request->getMethod() !== 'POST') {
            return false;
        }

        $path = $request->getPathInfo();

        // Standard login paths
        if (str_contains($path, '/login') || str_contains($path, '/admin/login')) {
            return true;
        }

        // Check for login form data
        if (
            $request->request->has('_username') ||
            $request->request->has('email') ||
            $request->request->has('_password') ||
            $request->request->has('password')
        ) {
            return true;
        }

        return false;
    }
    /**
     * Handle rate limiting for Sonata Admin
     */
    private function handleSonataRateLimit(RequestEvent $event, int $retryAfterSeconds): void
    {
        $request = $event->getRequest();

        // Add flash message for Sonata (multiple approaches)
        if ($request->hasSession()) {
            /** @var Session $session */
            $session = $request->getSession();
            $session->getFlashBag()->add('sonata_flash_error', sprintf(
                'Too many login attempts. Please try again in %d seconds.',
                $retryAfterSeconds
            ));
            $session->getFlashBag()->add('error', sprintf(
                'Too many login attempts. Please try again in %d seconds.',
                $retryAfterSeconds
            ));
            $session->getFlashBag()->add('sonata_user_error', sprintf(
                'Too many login attempts. Please try again in %d seconds.',
                $retryAfterSeconds
            ));
        }

        // Log for debugging
        error_log(sprintf(
            'Rate limit exceeded for IP %s. Redirecting to login with %d seconds delay.',
            $request->getClientIp(),
            $retryAfterSeconds
        ));

        // Redirect back to login page with error parameter
        $loginUrl = '/admin/login?error=rate_limit&retry_after=' . $retryAfterSeconds;
        $response = new RedirectResponse($loginUrl);

        // Add custom header for debugging
        $response->headers->set('X-Rate-Limit-Exceeded', 'true');
        $response->headers->set('X-Retry-After', (string) $retryAfterSeconds);

        $event->setResponse($response);
    }

    /**
     * Handle rate limiting for generic login forms
     */
    private function handleGenericRateLimit(RequestEvent $event, Request $request, int $retryAfterSeconds): void
    {
        // Add flash message
        if ($request->hasSession()) {
            /** @var Session $session */
            $session = $request->getSession();
            $session->getFlashBag()->add('error', sprintf(
                'Too many login attempts. Please try again in %d seconds.',
                $retryAfterSeconds
            ));
        }

        // Redirect back to the same page or a generic login page
        $redirectUrl = $request->headers->get('referer') ?? '/login';
        $response = new RedirectResponse($redirectUrl);

        $response->headers->set('X-Rate-Limit-Exceeded', 'true');
        $response->headers->set('X-Retry-After', (string) $retryAfterSeconds);

        $event->setResponse($response);
    }
}
