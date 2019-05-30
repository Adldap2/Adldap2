<?php

namespace Adldap\Query;

use Psr\SimpleCache\CacheInterface;

class Cache
{
    /**
     * The cache driver.
     *
     * @var CacheInterface
     */
    protected $store;

    /**
     * Constructor.
     *
     * @param CacheInterface $store
     */
    public function __construct(CacheInterface $store)
    {
        $this->store = $store;
    }

    /**
     * Get an item from the cache.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->store->get($key);
    }

    /**
     * Store an item in the cache.
     *
     * @param string $key
     * @param mixed $value
     * @param \DateTimeInterface|\DateInterval|int|null $ttl
     *
     * @return bool
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function put($key, $value, $ttl = null)
    {
        $this->store->set($key, $value, $ttl);
    }

    /**
     * Get an item from the cache, or execute the given Closure and store the result.
     *
     * @param string $key
     * @param \DateTimeInterface|\DateInterval|int|null $ttl
     * @param \Closure $callback
     *
     * @return mixed
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function remember($key, $ttl, \Closure $callback)
    {
        $value = $this->get($key);

        if (! is_null($value)) {
            return $value;
        }

        $this->put($key, $value = $callback(), $ttl);

        return $value;
    }

    /**
     * Delete an item from the cache.
     *
     * @param string $key
     *
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function delete($key)
    {
        $this->store->delete($key);
    }
}
