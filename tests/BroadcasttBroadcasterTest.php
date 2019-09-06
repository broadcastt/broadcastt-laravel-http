<?php

namespace Tests;

use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Http\Request;
use Mockery;
use Broadcastt\BroadcasttClient;
use Broadcastt\Laravel\BroadcasttBroadcaster;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
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
            $this->getMockRequestWithUserForChannel('private-test')
        );
    }

    public function testAuthThrowAccessDeniedHttpExceptionWithPrivateChannelWhenCallbackReturnFalse()
    {
        $this->expectException(AccessDeniedHttpException::class);

        $this->broadcaster->channel('test', function () {
            return false;
        });

        $this->broadcaster->auth(
            $this->getMockRequestWithUserForChannel('private-test')
        );
    }

    public function testAuthThrowAccessDeniedHttpExceptionWithPrivateChannelWhenRequestUserNotFound()
    {
        $this->expectException(AccessDeniedHttpException::class);

        $this->broadcaster->channel('test', function () {
            return true;
        });

        $this->broadcaster->auth(
            $this->getMockRequestWithoutUserForChannel('private-test')
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
            $this->getMockRequestWithUserForChannel('presence-test')
        );
    }

    public function testAuthThrowAccessDeniedHttpExceptionWithPresenceChannelWhenCallbackReturnNull()
    {
        $this->expectException(AccessDeniedHttpException::class);

        $this->broadcaster->channel('test', function () {
        });

        $this->broadcaster->auth(
            $this->getMockRequestWithUserForChannel('presence-test')
        );
    }

    public function testAuthThrowAccessDeniedHttpExceptionWithPresenceChannelWhenRequestUserNotFound()
    {
        $this->expectException(AccessDeniedHttpException::class);

        $this->broadcaster->channel('test', function () {
            return [1, 2, 3, 4];
        });

        $this->broadcaster->auth(
            $this->getMockRequestWithoutUserForChannel('presence-test')
        );
    }

    public function testValidAuthenticationResponseCallClientPrivateAuthMethodWithPrivateChannel()
    {
        $request = $this->getMockRequestWithUserForChannel('private-test');

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
        $request = $this->getMockRequestWithUserForChannel('presence-test');

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
    protected function getMockRequestWithUserForChannel($channel)
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->channel_name = $channel;
        $request->socket_id = 'abcd.1234';

        $request->shouldReceive('input')
            ->with('callback', false)
            ->andReturn(false);

        $user = Mockery::mock('User');
        $user->shouldReceive('getAuthIdentifier')
            ->andReturn(42);

        $request->shouldReceive('user')
            ->andReturn($user);

        return $request;
    }

    /**
     * @param string $channel
     * @return Request
     */
    protected function getMockRequestWithoutUserForChannel($channel)
    {
        $request = Mockery::mock(Request::class);
        $request->channel_name = $channel;

        $request->shouldReceive('user')
            ->andReturn(null);

        return $request;
    }
}
