<?php namespace Swiggles\Memcache;

use Illuminate\Contracts\Cache\Store;
use Illuminate\Cache\TaggableStore;

class MemcacheStore extends TaggableStore implements Store {

	/**
	 * The Memcached instance.
	 *
	 * @var \Memcache
	 */
	protected $memcache;

	/**
	 * A string that should be prepended to keys.
	 *
	 * @var string
	 */
	protected $prefix;

	/**
	 * Create a new Memcache store.
	 *
	 * @param  \Memcache  $memcache
	 * @param  string     $prefix
	 * @return void
	 */
	public function __construct(\Memcache $memcache, $prefix = '')
	{
		$this->memcache = $memcache;
		$this->prefix = strlen($prefix) > 0 ? $prefix.':' : '';
	}

	/**
	 * Retrieve an item from the cache by key.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function get($key)
	{
		if ($value = $this->memcache->get($this->prefix.$key)) {
		    return $value;
		}
	}

	/**
	 * Retrieve multiple items from the cache by key.
	 *
	 * Items not found in the cache will have a null value.
	 *
	 * @param  array $keys
	 * @return array
	 */
	public function many(array $keys)
	{
		$return = [];
		foreach ($keys as $key) {
			$return[$key] = $this->get($key);
		}
		return $return;
	}

	/**
	 * Store an item in the cache for a given number of seconds.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $seconds
	 * @return void
	 */
	public function put($key, $value, $seconds)
	{
		$this->memcache->set($this->prefix.$key, $value, false, $seconds);
	}

	/**
	 * Store multiple items in the cache for a given number of seconds.
	 *
	 * @param  array $values
	 * @param  int $seconds
	 * @return void
	 */
	public function putMany(array $values, $seconds)
	{
		foreach ($values as $key => $value) {
			$this->put($key, $value, $seconds);
		}
	}

	/**
	 * Store an item in the cache if the key doesn't exist.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $seconds
	 * @return bool
	 */
	public function add($key, $value, $seconds)
	{
		return $this->memcache->add($this->prefix.$key, $value, false, $seconds);
	}

	/**
	 * Increment the value of an item in the cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
     	 * @return int|bool
	 */
	public function increment($key, $value = 1)
	{
		return $this->memcache->increment($this->prefix.$key, $value);
	}

	/**
	 * Decrement the value of an item in the cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
     	 * @return int|bool
	 */
	public function decrement($key, $value = 1)
	{
		return $this->memcache->decrement($this->prefix.$key, $value);
	}

	/**
	 * Store an item in the cache indefinitely.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function forever($key, $value)
	{
		return $this->put($key, $value, 0);
	}

	/**
	 * Remove an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		$this->memcache->delete($this->prefix.$key);
	}

	/**
	 * Remove all items from the cache.
	 *
	 * @return void
	 */
	public function flush()
	{
		$this->memcache->flush();
	}

	/**
	 * Get the underlying Memcached connection.
	 *
	 * @return \Memcached
	 */
	public function getMemcache()
	{
		return $this->memcache;
	}

	/**
	 * Get the cache key prefix.
	 *
	 * @return string
	 */
	public function getPrefix()
	{
		return $this->prefix;
	}

}
