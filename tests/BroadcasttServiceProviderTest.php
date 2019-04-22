<?php

namespace Tests;

use Broadcastt\BroadcasttClient;
use Broadcastt\Laravel\BroadcasttBroadcaster;
use Broadcastt\Laravel\BroadcasttManager;
use GrahamCampbell\TestBenchCore\LaravelTrait;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;
use Illuminate\Support\Facades\Broadcast;

class BroadcasttServiceProviderTest extends TestCase
{
    use LaravelTrait;
    use ServiceProviderTrait;

    public function testCanMakeBroadcasttManager()
    {
        $manager = $this->app->make(BroadcasttManager::class);

        $this->assertInstanceOf(BroadcasttManager::class, $manager);
    }

    public function testIsBroadcasttManagerInjectable()
    {
        $this->assertIsInjectable(BroadcasttManager::class);
    }

    public function testCanMakeBroadcasttClient()
    {
        $client = $this->app->make(BroadcasttClient::class);

        $this->assertInstanceOf(BroadcasttClient::class, $client);
    }

    public function testIsBroadcasttClientInjectable()
    {
        $this->assertIsInjectable(BroadcasttClient::class);
    }

    public function testIsDriverRegistered()
    {
        $driver = Broadcast::driver('broadcastt');

        $this->assertInstanceOf(BroadcasttBroadcaster::class, $driver);
    }
}
