<?php
/**
 *
 */

namespace Broadcastt\Laravel;

use Broadcastt\BroadcasttClient;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class BroadcasttServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Broadcast::extend('broadcastt', function ($app, $config) {
            /** @var $app \Illuminate\Contracts\Foundation\Application */

            /** @var BroadcasttFactory $factory */
            $factory = $app->make(BroadcasttFactory::class);
            $client = $factory->create($config);

            return new BroadcasttBroadcaster($client);
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
        $this->app->singleton(BroadcasttFactory::class, function () {
            return new BroadcasttFactory();
        });

        $this->app->singleton(BroadcasttManager::class, function ($app) {
            /** @var BroadcasttFactory $factory */
            $factory = $this->app->make(BroadcasttFactory::class);

            return new BroadcasttManager($factory, $app->config);
        });

        $this->app->singleton(BroadcasttClient::class, function () {
            /** @var BroadcasttManager $manager */
            $manager = $this->app->make(BroadcasttManager::class);

            return $manager->connection();
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
            BroadcasttManager::class,
            BroadcasttFactory::class,
            BroadcasttClient::class,
        ];
    }
}