<?php

namespace App\SharedKernel\Infrastructure\Messenger\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ConsumedByWorkerStamp;
use Symfony\Component\Messenger\Stamp\SentStamp;
use Psr\Cache\CacheItemPoolInterface;

class UniquenessMiddleware implements MiddlewareInterface
{
    private CacheItemPoolInterface $cache;
    private int $ttl;

    public function __construct(CacheItemPoolInterface $cache, int $ttl = 3600)
    {
        $this->cache = $cache;
        $this->ttl = $ttl;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();
        $messageHash = $this->generateMessageHash($message);
        $cacheKey = 'messenger_message_' . $messageHash;

        // Vérifier l'unicité seulement lors du dispatch initial (avant envoi)
        $isBeingSent = empty($envelope->all(SentStamp::class)) && empty($envelope->all(ConsumedByWorkerStamp::class));

        if ($isBeingSent) {
            $cacheItem = $this->cache->getItem($cacheKey);

            if ($cacheItem->isHit()) {
                // Message déjà en queue, on ne l'ajoute pas
                return $envelope;
            }

            // Marquer atomiquement le message comme en cours de traitement
            $cacheItem->set(time());
            $cacheItem->expiresAfter($this->ttl);
            $this->cache->save($cacheItem);
        }

        try {
            // Continuer le traitement
            $result = $stack->next()->handle($envelope, $stack);

            // Nettoyer le cache après traitement réussi
            $this->cleanupCache($cacheKey, $envelope);

            return $result;
        } catch (\Throwable $e) {
            // En cas d'erreur lors de la consommation, garder le cache temporairement
            if (!empty($envelope->all(ConsumedByWorkerStamp::class))) {
                $cacheItem = $this->cache->getItem($cacheKey);
                $cacheItem->set(time());
                $cacheItem->expiresAfter(60); // 1 minute au lieu du TTL normal
                $this->cache->save($cacheItem);
            }

            throw $e;
        }
    }

    private function cleanupCache(string $cacheKey, Envelope $envelope): void
    {
        // Nettoyer si le message a été consommé OU si c'est après l'envoi vers la queue
        if (!empty($envelope->all(ConsumedByWorkerStamp::class)) || !empty($envelope->all(SentStamp::class))) {
            $this->cache->deleteItem($cacheKey);
        }
    }

    private function generateMessageHash($message): string
    {
        // Pour votre cas spécifique : entité + id
        if (method_exists($message, 'getEntityClass') && method_exists($message, 'getEntityId')) {
            return md5($message->getEntityClass() . '_' . $message->getEntityId());
        }

        if (method_exists($message, 'getUniqueIdentifier')) {
            return md5($message->getUniqueIdentifier());
        }

        return md5(serialize($message));
    }
}
