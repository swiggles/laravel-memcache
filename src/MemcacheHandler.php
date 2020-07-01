<?php

namespace Swiggles\Memcache;

use Illuminate\Contracts\Cache\Repository as CacheContract;
use SessionHandlerInterface;

class MemcacheHandler implements SessionHandlerInterface
{
	protected

		/**
		 * @var CacheContract
		 */
		$cache,

		/**
		 * @var int
		 */
		$seconds;

	/**
	 * MemcacheHandler constructor.
	 *
	 * @param CacheContract $cache
	 * @param int $seconds
	 */
	public function __construct(CacheContract $cache, int $seconds)
	{
		$this->cache = $cache;
		$this->seconds = $seconds;
	}

	/**
	 * @param string $savePath
	 * @param string $sessionName
	 * @return bool
	 */
	public function open($savePath, $sessionName): bool
	{
		return true;
	}

	/**
	 * @return bool
	 */
	public function close(): bool
	{
		return true;
	}

	/**
	 * @param string $sessionId
	 * @return string
	 */
	public function read($sessionId): string
	{
		return $this->cache->get($sessionId) ?: '';
	}

	/**
	 * @param string $sessionId
	 * @param string $data
	 * @return bool
	 */
	public function write($sessionId, $data)
	{
		return $this->cache->put($sessionId, $data, $this->seconds);
	}

	/**
	 * @param string $sessionId
	 * @return bool
	 */
	public function destroy($sessionId): bool
	{
		return $this->cache->forget($sessionId);
	}

	/**
	 * @param int $lifetime
	 * @return bool
	 */
	public function gc($lifetime): bool
	{
		return true;
	}

	/**
	 * Get the underlying cache repository.
	 *
	 * @return CacheContract
	 */
	public function getCache(): CacheContract
	{
		return $this->cache;
	}

}
