<?php

namespace Tests;

use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Mockery;
use Broadcastt\BroadcasttClient;
use Broadcastt\Laravel\BroadcasttBroadcaster;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class BroadcasttBroadcasterTest extends \PHPUnit\Framework\TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var MockInterface
     */
    private $clientMock;

    /**
     * @var BroadcasttBroadcaster|MockInterface
     */
    private $broadcaster;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clientMock = Mockery::mock(BroadcasttClient::class);

        $this->broadcaster = Mockery::mock(BroadcasttBroadcaster::class, [$this->clientMock])->makePartial();
    }

    public function testAuthCallValidAuthenticationResponseWithPrivateChannelWhenCallbackReturnTrue()
    {
        $this->broadcaster->channel('test', function () {
            return true;
        });

        $this->broadcaster->shouldReceive('validAuthenticationResponse')
            ->once();

        $this->broadcaster->auth(
            $this->createRequestWithUserForChannel('private-test')
        );
    }

    public function testAuthThrowAccessDeniedHttpExceptionWithPrivateChannelWhenCallbackReturnFalse()
    {
        $this->expectException(AccessDeniedHttpException::class);

        $this->broadcaster->channel('test', function () {
            return false;
        });

        $this->broadcaster->auth(
            $this->createRequestWithUserForChannel('private-test')
        );
    }

    public function testAuthThrowAccessDeniedHttpExceptionWithPrivateChannelWhenRequestUserNotFound()
    {
        $this->expectException(AccessDeniedHttpException::class);

        $this->broadcaster->channel('test', function () {
            return true;
        });

        $this->broadcaster->auth(
            $this->createRequestWithoutUserForChannel('private-test')
        );
    }

    public function testAuthCallValidAuthenticationResponseWithPresenceChannelWhenCallbackReturnAnArray()
    {
        $returnData = [1, 2, 3, 4];
        $this->broadcaster->channel('test', function () use ($returnData) {
            return $returnData;
        });

        $this->broadcaster->shouldReceive('validAuthenticationResponse')
            ->once();

        $this->broadcaster->auth(
            $this->createRequestWithUserForChannel('presence-test')
        );
    }

    public function testAuthThrowAccessDeniedHttpExceptionWithPresenceChannelWhenCallbackReturnNull()
    {
        $this->expectException(AccessDeniedHttpException::class);

        $this->broadcaster->channel('test', function () {
        });

        $this->broadcaster->auth(
            $this->createRequestWithUserForChannel('presence-test')
        );
    }

    public function testAuthThrowAccessDeniedHttpExceptionWithPresenceChannelWhenRequestUserNotFound()
    {
        $this->expectException(AccessDeniedHttpException::class);

        $this->broadcaster->channel('test', function () {
            return [1, 2, 3, 4];
        });

        $this->broadcaster->auth(
            $this->createRequestWithoutUserForChannel('presence-test')
        );
    }

    public function testValidAuthenticationResponseCallClientPrivateAuthMethodWithPrivateChannel()
    {
        $request = $this->createRequestWithUserForChannel('private-test');

        $data = [
            'auth' => 'abcd:efgh',
        ];

        $this->clientMock->shouldReceive('privateAuth')
            ->once()
            ->andReturn(json_encode($data));

        $this->assertEquals(
            $data,
            $this->broadcaster->validAuthenticationResponse($request, true)
        );
    }

    public function testValidAuthenticationResponseCallClientPresenceAuthMethodWithPresenceChannel()
    {
        $request = $this->createRequestWithUserForChannel('presence-test');

        $data = [
            'auth' => 'abcd:efgh',
            'channel_data' => [
                'user_id' => 42,
                'user_info' => [1, 2, 3, 4],
            ],
        ];

        $this->clientMock->shouldReceive('presenceAuth')
            ->once()
            ->andReturn(json_encode($data));

        $this->assertEquals(
            $data,
            $this->broadcaster->validAuthenticationResponse($request, true)
        );
    }

    public function testBroadcastCallClientTriggerWithSockedIdWhenPayloadContainsSocketValue()
    {
        $this->clientMock->shouldReceive('trigger')
            ->with(['test-channel'], 'test-event', ['test-key' => 'test-value'], 42)
            ->andReturn(true)
            ->once();

        $this->broadcaster->broadcast(['test-channel'], 'test-event', ['test-key' => 'test-value', 'socket' => 42]);
    }

    public function testBroadcastThrowBroadcastExceptionWhenClientTriggerFail()
    {
        $this->clientMock->shouldReceive('trigger')
            ->andReturn(false)
            ->once();

        $this->expectException(BroadcastException::class);

        $this->broadcaster->broadcast(['test-channel'], 'test-event', ['test-key' => 'test-value']);
    }

    /**
     * @param string $channel
     * @return Request
     */
    protected function createRequestWithUserForChannel($channel)
    {
        $symfonyRequest = SymfonyRequest::create(
            '',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode([
                'channel_name' => $channel,
                'socket_id' => 'abcd.1234',
            ]),
        );

        $request = Request::createFromBase($symfonyRequest);

        $request->setUserResolver(function () {
            $user = new User();
            $user->id = 42;
            return $user;
        });

        return $request;
    }

    /**
     * @param string $channel
     * @return Request
     */
    protected function createRequestWithoutUserForChannel($channel)
    {
        $symfonyRequest = SymfonyRequest::create(
            '',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode([
                'channel_name' => $channel,
                'socket_id' => 'abcd.1234',
            ]),
        );

        $request = Request::createFromBase($symfonyRequest);

        return $request;
    }
}
