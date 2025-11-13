<?php

declare(strict_types=1);

namespace App\Shared\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Rate limiting event subscriber
 * Applies rate limits to different routes and user types
 */
readonly class RateLimitSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RateLimiterFactory $apiLimiter,
        private RateLimiterFactory $adminLimiter,
        private RateLimiterFactory $globalLimiter,
        private RateLimiterFactory $apiBurstLimiter,
        private RateLimiterFactory $apiAnonymousLimiter,
        private ?Security $security = null,
        private ?LoggerInterface $logger = null
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 5],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        // Debug: log toutes les requêtes API
        if (str_starts_with($request->getPathInfo(), '/api/')) {
            $this->logger?->info('Rate limiter: checking path', [
                'path' => $request->getPathInfo(),
                'ip' => $request->getClientIp()
            ]);
        }

        // Skip rate limiting for health checks and internal routes
        if ($this->shouldSkipRateLimit($request)) {
            return;
        }

        // Get client identifier (IP + User ID if authenticated)
        $identifier = $this->getClientIdentifier($request);

        // Debug: log identifier
        if (str_starts_with($request->getPathInfo(), '/api/')) {
            $this->logger?->debug('Rate limiter identifier generated', [
                'identifier' => $identifier,
                'path' => $request->getPathInfo()
            ]);
        }

        // Apply global rate limiting first
        $globalLimit = $this->globalLimiter->create($identifier);
        $globalConsumption = $globalLimit->consume();
        if (!$globalConsumption->isAccepted()) {
            $this->logger?->warning('Global rate limit exceeded', [
                'identifier' => $identifier,
                'remaining_tokens' => $globalConsumption->getRemainingTokens()
            ]);
            $retryAfter = $globalConsumption->getRetryAfter()->getTimestamp() + 3600;
            $event->setResponse($this->createRateLimitResponse('Global rate limit exceeded', [
                'limit' => $globalConsumption->getRemainingTokens(),
                'retry_after' => $retryAfter,
                'type' => 'global_limit'
            ]));
            return;
        }

        // Apply specific rate limiting based on route
        if (str_starts_with($request->getPathInfo(), '/api/')) {
            $this->logger?->debug('Applying API rate limit', [
                'path' => $request->getPathInfo(),
                'identifier' => $identifier
            ]);
            $this->applyApiRateLimit($event, $identifier);
        } elseif (str_starts_with($request->getPathInfo(), '/admin/')) {
            $this->applyAdminRateLimit($event, $identifier);
        }
    }

    /**
     * Apply API-specific rate limiting with different rules for authenticated vs anonymous users
     */
    private function applyApiRateLimit(RequestEvent $event, string $identifier): void
    {
        $request = $event->getRequest();
        $isAuthenticated = $this->security && $this->security->getUser();

        // First, check burst limiting (applies to all)
        $burstLimit = $this->apiBurstLimiter->create($identifier);
        $burstConsumption = $burstLimit->consume();
        if (!$burstConsumption->isAccepted()) {
            $retryAfter = $burstConsumption->getRetryAfter()->getTimestamp() + 60;
            $event->setResponse($this->createRateLimitResponse(
                'API burst limit exceeded (too many requests per minute)',
                [
                    'limit' => $burstConsumption->getRemainingTokens(),
                    'retry_after' => $retryAfter,
                    'type' => 'burst_limit'
                ]
            ));
            return;
        }

        // Apply different hourly limits based on authentication
        if ($isAuthenticated) {
            // Authenticated users get higher limits
            $apiLimit = $this->apiLimiter->create($identifier);
            $consumption = $apiLimit->consume();

            if (!$consumption->isAccepted()) {
                $retryAfter = $consumption->getRetryAfter()->getTimestamp() + 3600;
                $event->setResponse($this->createRateLimitResponse(
                    'API rate limit exceeded for authenticated user',
                    [
                        'limit' => $consumption->getRemainingTokens(),
                        'retry_after' => $retryAfter,
                        'type' => 'authenticated_limit',
                        'user' => $this->security->getUser()->getUserIdentifier()
                    ]
                ));
            }
        } else {
            // Anonymous users get lower limits
            $anonymousLimit = $this->apiAnonymousLimiter->create($identifier);
            $consumption = $anonymousLimit->consume();

            if (!$consumption->isAccepted()) {
                $retryAfter = $consumption->getRetryAfter()->getTimestamp() + 3600;
                $event->setResponse($this->createRateLimitResponse(
                    'API rate limit exceeded for anonymous user. Consider authenticating for higher limits.',
                    [
                        'limit' => $consumption->getRemainingTokens(),
                        'retry_after' => $retryAfter,
                        'type' => 'anonymous_limit',
                        'suggestion' => 'Authenticate to get higher rate limits'
                    ]
                ));
            }
        }
    }

    /**
     * Apply admin-specific rate limiting
     */
    private function applyAdminRateLimit(RequestEvent $event, string $identifier): void
    {
        $adminLimit = $this->adminLimiter->create($identifier);
        $consumption = $adminLimit->consume();

        if (!$consumption->isAccepted()) {
            $retryAfter = $consumption->getRetryAfter()->getTimestamp() + 3600;
            $event->setResponse($this->createRateLimitResponse(
                'Admin rate limit exceeded',
                [
                    'limit' => $consumption->getRemainingTokens(),
                    'retry_after' => $retryAfter,
                    'type' => 'admin_limit'
                ]
            ));
        }
    }

    /**
     * Check if rate limiting should be skipped for this request
     */
    private function shouldSkipRateLimit(Request $request): bool
    {
        $path = $request->getPathInfo();

        // Skip for health checks
        if (str_starts_with($path, '/health')) {
            return true;
        }

        // Skip for Symfony profiler and debug routes
        if (str_starts_with($path, '/_')) {
            return true;
        }

        // Skip for assets
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/', $path)) {
            return true;
        }

        return false;
    }

    /**
     * Get unique client identifier for rate limiting
     */
    private function getClientIdentifier(Request $request): string
    {
        $ip = $request->getClientIp() ?? 'unknown';

        // Include user ID if authenticated for more precise limiting
        if ($this->security && $this->security->getUser()) {
            return $ip . '_user_' . $this->security->getUser()->getUserIdentifier();
        }

        return $ip;
    }

    /**
     * Create standardized rate limit response
     * @param array<mixed> $details
     */
    private function createRateLimitResponse(string $message, array $details = []): JsonResponse
    {
        $response = new JsonResponse([
            'error' => 'rate_limit_exceeded',
            'message' => $message,
            'details' => $details,
            'timestamp' => time()
        ], 429);

        // Add standard rate limiting headers
        if (isset($details['type'])) {
            switch ($details['type']) {
                case 'burst_limit':
                    $response->headers->set('X-RateLimit-Limit', '100');
                    $response->headers->set('X-RateLimit-Window', '1 minute');
                    break;
                case 'authenticated_limit':
                    $response->headers->set('X-RateLimit-Limit', '10000');
                    $response->headers->set('X-RateLimit-Window', '1 hour');
                    break;
                case 'anonymous_limit':
                    $response->headers->set('X-RateLimit-Limit', '1000');
                    $response->headers->set('X-RateLimit-Window', '1 hour');
                    break;
                default:
                    $response->headers->set('X-RateLimit-Limit', '1000');
                    $response->headers->set('X-RateLimit-Window', '1 hour');
            }
        }

        if (isset($details['limit'])) {
            $response->headers->set('X-RateLimit-Remaining', (string) $details['limit']);
        }
        if (isset($details['retry_after'])) {
            $response->headers->set('Retry-After', (string) $details['retry_after']);
        }

        return $response;
    }
}
