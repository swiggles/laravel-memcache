<?php namespace Swiggles\Memcache;

use Illuminate\Cache\Repository;
use Illuminate\Cache\CacheManager;
use Illuminate\Session\SessionManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

class MemcacheServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('memcache', function($app)
        {
            $memcache = new MemcacheConnector;

            $servers = $this->app['config']['cache.stores.memcached.servers'];
            return $memcache->connect($servers);
        });

        $this->app->singleton('memcache.store', function($app)
        {
            $prefix = $this->app['config']['cache.prefix'];

            return new Repository(new MemcacheStore($app['memcache'], $prefix));
        });

        $this->app->singleton('memcache.driver', function($app)
        {
            $minutes = $this->app['config']['session.lifetime'];
            return new MemcacheHandler($app['memcache.store'], $minutes * 60);
        });
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $this->app['config'];

        // if the cache driver is set to use the memcache driver
        if ($config['cache.default'] == 'memcache') {
            // set the config to use same servers as memcached
            $config->set('cache.stores.memcache.driver', 'memcache');
            $servers = $config['cache.stores.memcached.servers'];
            $config->set('cache.stores.memcache.servers', $servers);

            // extend the cache manager
            $this->extendCache($this->app);
        }

        // if the session driver is set to memcached
        if ($config['session.driver'] == 'memcache') {
            // extend the session manager
            $this->extendSession($this->app);
        }
    }

    /**
     * Add the memcache driver to the cache manager.
     *
     * @param \Illuminate\Contracts\Foundation\Application  $app
     */
    public function extendCache(Application $app)
    {
        $app->make('cache')->extend('memcache', function ($app) {
            return $app['memcache.store'];
        });
    }

    /**
     * Add the memcache driver to the session manager.
     *
     * @param \Illuminate\Contracts\Foundation\Application  $app
     */
    public function extendSession(Application $app)
    {
        $app->make('session')->extend('memcache', function ($app) {
            return $app['memcache.driver'];
        });
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ['memcache', 'memcache.store', 'memcache.driver'];
    }
}
