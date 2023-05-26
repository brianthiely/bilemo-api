<?php

namespace App\Service\Cache;

use DateTimeInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class CacheService
{
    private TagAwareCacheInterface $cache;

    /**
     * CacheService constructor.
     *
     * @param TagAwareCacheInterface $cache
     */
    public function __construct(TagAwareCacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Cache the given data with the specified key and expiration time.
     *
     * @param string $key The cache key
     * @param string $data The data to cache
     * @param DateTimeInterface|int $expiresAt The expiration time or TTL in seconds
     * @param array $tags The cache tags
     */
    public function cache(string $key, string $data, DateTimeInterface|int $expiresAt, array $tags = []): void
    {
        $item = $this->cache->getItem($key);

        if (!empty($tags)) {
            $item->tag($tags);
        }

        $item->set($data);
        $item->expiresAt($expiresAt);

        $this->cache->save($item);
    }


    /**
     * Retrieve the cached data associated with the specified key.
     *
     * @param string $key The cache key
     * @return string|null The cached data or null if not found
     */
    public function get(string $key): ?string
    {
        $item = $this->cache->getItem($key);

        if (!$item->isHit()) {
            return null;
        }

        return $item->get();
    }


    /**
     * @throws InvalidArgumentException
     */
    public function invalidateTags(array $array): void
    {
        $this->cache->invalidateTags($array);
    }
}
