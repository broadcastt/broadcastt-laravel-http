<?php

namespace Tests;

use Broadcastt\Laravel\BroadcasttFactory;
use GuzzleHttp\Client as GuzzleClient;

class BroadcasttFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var BroadcasttFactory
     */
    private $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new BroadcasttFactory();
    }

    public function testCanCreate()
    {
        $client = $this->factory->create([
            'id' => 'testid',
            'key' => 'testkey',
            'secret' => 'testsecret',
        ]);

        $this->assertEquals('testid', $client->appId);
        $this->assertEquals('testkey', $client->appKey);
        $this->assertEquals('testsecret', $client->appSecret);
    }

    public function testCanCreateWithCluster()
    {
        $client = $this->factory->create([
            'id' => 'testid',
            'key' => 'testkey',
            'secret' => 'testsecret',
            'cluster' => 'us'
        ]);

        $this->assertStringStartsWith('us.', $client->host);
    }

    public function testCanCreateWithUseTLS()
    {
        $client = $this->factory->create([
            'id' => 'testid',
            'key' => 'testkey',
            'secret' => 'testsecret',
            'useTLS' => true
        ]);

        $this->assertEquals('https', $client->scheme);
        $this->assertEquals('443', $client->port);
    }

    public function testCanCreateWithModifiers()
    {
        $guzzleClient = new GuzzleClient();
        $client = $this->factory->create([
            'id' => 'testid',
            'key' => 'testkey',
            'secret' => 'testsecret',
            'scheme' => 'https',
            'host' => 'test.xyz',
            'port' => '8080',
            'basePath' => '/test/path',
            'timeout' => '999',
            'guzzleClient' => $guzzleClient,
        ]);

        $this->assertEquals('https', $client->scheme);
        $this->assertEquals('test.xyz', $client->host);
        $this->assertEquals('8080', $client->port);
        $this->assertEquals('/test/path', $client->basePath);
        $this->assertEquals('999', $client->timeout);
        $this->assertEquals($guzzleClient, $client->guzzleClient);
    }
}
