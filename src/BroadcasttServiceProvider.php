<?php
/**
 *
 */

namespace Broadcastt\Laravel;

use Broadcastt\Broadcastt;
use Illuminate\Support\ServiceProvider;

class BroadcasttServiceProvider extends ServiceProvider
{
    public function boot()
    {
        \Broadcast::extend('broadcastt', function ($app, $config) {
            $config = config('broadcastt.connections.default', $config);

            if (array_key_exists('cluster', $config)) {
                $broadcastt = new Broadcastt($config['id'], $config['key'], $config['secret'], $config['cluster']);
            } else {
                $broadcastt = new Broadcastt($config['id'], $config['key'], $config['secret']);
            }

            if (array_key_exists('encrypted', $config) && $config['encrypted']) {
                $broadcastt->setScheme('https');
                $broadcastt->setPort(443);
            }

            if (array_key_exists('debug', $config)) {
                $broadcastt->setDebug($config['debug']);
            }

            if (array_key_exists('scheme', $config)) {
                $broadcastt->setScheme($config['scheme']);
            }

            if (array_key_exists('host', $config)) {
                $broadcastt->setHost($config['host']);
            }

            if (array_key_exists('port', $config)) {
                $broadcastt->setPort($config['port']);
            }

            if (array_key_exists('timeout', $config)) {
                $broadcastt->setTimeout($config['timeout']);
            }

            if (array_key_exists('curl_options', $config)) {
                $broadcastt->setCurlOptions($config['curl_options']);
            }

            return new BroadcasttBroadcaster($broadcastt);
        });

        $this->setupConfig();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/../config/broadcastt.php');
        $this->publishes([$source => config_path('broadcastt.php')]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('broadcastt', function () {
            return new BroadcasttManager(\Broadcast::driver('broadcastt'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'broadcastt',
        ];
    }
}