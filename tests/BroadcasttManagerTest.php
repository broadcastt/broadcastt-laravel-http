<?php

namespace Tests;

use Broadcastt\BroadcasttClient;
use Broadcastt\Laravel\BroadcasttFactory;
use Broadcastt\Laravel\BroadcasttManager;
use Illuminate\Config\Repository;
use InvalidArgumentException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class BroadcasttManagerTest extends \PHPUnit\Framework\TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var Repository
     */
    private $config;

    /**
     * @var BroadcasttManager
     */
    private $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $factory = new BroadcasttFactory();
        $this->config = new Repository([
            'broadcastt' => [
                'default' => 'main',

                'connections' => [
                    'main' => [
                        'id' => env('BROADCASTER_APP_ID'),
                        'key' => env('BROADCASTER_APP_KEY'),
                        'secret' => env('BROADCASTER_APP_SECRET'),
                        'cluster' => env('BROADCASTER_APP_CLUSTER'),
                    ],

                    'alternative' => [
                        'id' => 'alternative-app-id',
                        'key' => 'alternative-app-key',
                        'secret' => 'alternative-app-secret',
                        'cluster' => 'alternative-app-cluster',
                    ],

                ],
            ],
        ]);

        $this->manager = new BroadcasttManager($factory, $this->config);
    }

    public function testCanAccessDefaultConnection()
    {
        $client = $this->manager->connection();

        $this->assertEquals('main', $this->manager->getDefaultClient());
        $this->assertNotNull($client);
        $this->assertEquals(env('BROADCASTER_APP_ID'), $client->appId);
    }

    public function testCanAccessConnectionByName()
    {
        $client = $this->manager->connection('alternative');

        $this->assertNotNull($client);
        $this->assertEquals('alternative-app-id', $client->appId);
    }

    public function testCanChangeDefaultConnection()
    {
        $this->manager->setDefaultClient('alternative');
        $client = $this->manager->connection();

        $this->assertEquals('alternative', $this->manager->getDefaultClient());
        $this->assertNotNull($client);
        $this->assertEquals('alternative-app-id', $client->appId);
    }

    public function testAccessingInvalidConnectionByNameThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->manager->connection('invalid-connection-name');
    }

    public function testIsTriggerMethodOfDefaultClientCalled()
    {
        $clientMock = Mockery::mock(BroadcasttClient::class);
        $clientMock->shouldReceive('trigger')
            ->once()
            ->with('test-channel', 'test-event', '');

        $factoryMock = Mockery::mock(BroadcasttFactory::class);
        $factoryMock->shouldReceive('create')
            ->andReturn($clientMock);

        $manager = new BroadcasttManager($factoryMock, $this->config);

        $manager->trigger('test-channel', 'test-event', '');
    }

    public function testIsTriggerBatchMethodOfDefaultClientCalled()
    {
        $clientMock = Mockery::mock(BroadcasttClient::class);
        $clientMock->shouldReceive('triggerBatch')
            ->once()
            ->with([]);

        $factoryMock = Mockery::mock(BroadcasttFactory::class);
        $factoryMock->shouldReceive('create')
            ->andReturn($clientMock);

        $manager = new BroadcasttManager($factoryMock, $this->config);

        $manager->triggerBatch([]);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testIsGetMethodOfDefaultClientCalled()
    {
        $clientMock = Mockery::mock(BroadcasttClient::class);
        $clientMock->shouldReceive('get')
            ->once()
            ->with('/test/path', []);

        $factoryMock = Mockery::mock(BroadcasttFactory::class);
        $factoryMock->shouldReceive('create')
            ->andReturn($clientMock);

        $manager = new BroadcasttManager($factoryMock, $this->config);

        $manager->get('/test/path', []);
    }
}
