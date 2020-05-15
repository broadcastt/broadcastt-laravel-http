<?php

namespace Tests;

use Broadcastt\Laravel\BroadcasttServiceProvider;
use Illuminate\Config\Repository;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        /** @var Repository $config */
        $config = $app->config;

        $config->set('broadcasting.connections.broadcastt', [
            'driver' => 'broadcastt',
            'id' => env('BROADCASTER_APP_ID'),
            'key' => env('BROADCASTER_APP_KEY'),
            'secret' => env('BROADCASTER_APP_SECRET'),
            'cluster' => env('BROADCASTER_APP_CLUSTER'),
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            BroadcasttServiceProvider::class
        ];
    }

    /**
     * Get the service provider class.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return string
     */
    protected function getServiceProviderClass($app)
    {
        return BroadcasttServiceProvider::class;
    }
}
