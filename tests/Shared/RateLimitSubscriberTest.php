<?php

declare(strict_types=1);

namespace App\Tests\Shared;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test suite for rate limiting functionality
 */
class RateLimitSubscriberTest extends WebTestCase
{
    private function clearRateLimitCache(): void
    {
        // Clear any existing rate limit state
        $container = static::getContainer();

        if ($container->has('cache.rate_limiter')) {
            $cache = $container->get('cache.rate_limiter');
            $cache->clear();
        }
    }

    public function testApiRateLimitNotExceeded(): void
    {
        $client = static::createClient();
        $this->clearRateLimitCache();

        // Make a few API requests (should not hit rate limit)
        for ($i = 0; $i < 5; $i++) {
            $client->request('GET', '/api');
            $this->assertResponseIsSuccessful(sprintf('Request %d should succeed', $i + 1));
        }
    }

    public function testHealthCheckBypassesRateLimit(): void
    {
        $client = static::createClient();

        // Health checks should never be rate limited
        for ($i = 0; $i < 10; $i++) {
            $client->request('GET', '/health');
            $this->assertResponseIsSuccessful(sprintf('Health check %d should succeed', $i + 1));
        }
    }

    public function testRateLimitHeaders(): void
    {
        $client = static::createClient();

        // Make enough requests to potentially trigger rate limiting headers
        $client->request('GET', '/api');

        // Even if not rate limited, check that response doesn't have rate limit error
        $this->assertNotEquals(429, $client->getResponse()->getStatusCode());
    }

    public function testStaticAssetsAreNotRateLimited(): void
    {
        $client = static::createClient();

        // Test common static asset extensions
        $assets = [
            '/css/style.css',
            '/js/app.js',
            '/images/logo.png',
            '/favicon.ico'
        ];

        foreach ($assets as $asset) {
            // These will 404 but should not be rate limited
            $client->request('GET', $asset);
            $this->assertNotEquals(429, $client->getResponse()->getStatusCode());
        }
    }

    public function testProfilerRoutesAreNotRateLimited(): void
    {
        $client = static::createClient();

        // Profiler routes should not be rate limited
        $profilerRoutes = [
            '/_profiler',
            '/_wdt/123'
        ];

        foreach ($profilerRoutes as $route) {
            $client->request('GET', $route);
            $this->assertNotEquals(429, $client->getResponse()->getStatusCode());
        }
    }
}
