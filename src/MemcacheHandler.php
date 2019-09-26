<?php namespace Swiggles\Memcache;

use SessionHandlerInterface;
use Illuminate\Contracts\Cache\Repository as CacheContract;

class MemcacheHandler implements SessionHandlerInterface {

    /**
     * Create a new cache driven handler instance.
     *
     * @param  \Illuminate\Cache\Repository  $cache
     * @param  int  $seconds
     * @return void
     */
    public function __construct(CacheContract $cache, $seconds)
    {
        $this->cache = $cache;
        $this->seconds = $seconds;
    }

    public function open($savePath, $sessionName)
    {
        return true;
    }
    
    public function close()
    {
        return true;
    }
    
    public function read($sessionId)
    {
        return $this->cache->get($sessionId) ?: '';
    }
    
    public function write($sessionId, $data)
    {
        return $this->cache->put($sessionId, $data, $this->seconds);
    }
    
    public function destroy($sessionId)
    {
        return $this->cache->forget($sessionId);
    }
    
    public function gc($lifetime)
    {
        return true;
    }
    
    /**
     * Get the underlying cache repository.
     *
     * @return \Illuminate\Cache\Repository
     */
    public function getCache()
    { 
        return $this->cache;
    }

}
