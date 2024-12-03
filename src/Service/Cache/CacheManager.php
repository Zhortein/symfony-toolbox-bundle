<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Cache;

use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CacheManager
{
    public const string CACHE_NAMESPACE = 'ZhorteinCacheManager';

    public AbstractAdapter $adapter;

    public function __construct(string $cacheNamespace = self::CACHE_NAMESPACE, ?string $adapterClass = null, int $ttl = 300)
    {
        if (empty($adapterClass)) {
            $adapterClass = FilesystemAdapter::class;
        }

        if (empty($cacheNamespace)) {
            $cacheNamespace = self::CACHE_NAMESPACE;
        }

        /** @var AbstractAdapter $adapter */
        $adapter = new $adapterClass($cacheNamespace, $ttl);
        $this->adapter = $adapter;
    }

    /**
     * @param string   $key      Cache key
     * @param callable $callback Function that return data to be cached if cache empty
     */
    public function remember(string $key, callable $callback): mixed
    {
        try {
            return $this->adapter->get($key, $callback);
        } catch (\Exception|InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * Returns cached item or false it no cache found for that key.
     *
     * @return bool|mixed
     */
    public function get(string $cacheKey, mixed $callback): mixed
    {
        if (!is_callable($callback)) {
            $callable = static function () use ($callback) {
                return $callback;
            };
        } else {
            $callable = $callback;
        }

        if ($this->adapter->hasItem($cacheKey)) {
            return $this->adapter->getItem($cacheKey)->get();
        }

        return $this->remember($cacheKey, $callable);
    }

    /**
     * Stores value in cache (and delete previous value).
     */
    public function set(string $cacheKey, mixed $callback): mixed
    {
        if (false === $this->delete($cacheKey)) {
            return false;
        }

        if (!is_callable($callback)) {
            $callable = static function () use ($callback) {
                return $callback;
            };
        } else {
            $callable = $callback;
        }

        return $this->remember($cacheKey, $callable);
    }

    public function delete(string $cacheKey): bool
    {
        try {
            // Delete cached value
            $this->adapter->delete($cacheKey);

            return true;
        } catch (\Exception|InvalidArgumentException) {
            return false;
        }
    }
}
