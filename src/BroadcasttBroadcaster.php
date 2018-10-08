<?php
/**
 *
 */

namespace Broadcastt\Laravel;

use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class BroadcasttBroadcaster extends Broadcaster
{
    /**
     * The Broadcastt SDK instance.
     *
     * @var \Broadcastt\Broadcastt
     */
    private $broadcastt;

    /**
     * BroadcasttBroadcaster constructor.
     *
     * @param $broadcastt
     */
    public function __construct($broadcastt)
    {
        $this->broadcastt = $broadcastt;
    }

    /**
     * Authenticate the incoming request for a given channel.
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function auth($request)
    {
        if (Str::startsWith($request->channel_name, ['private-', 'presence-']) &&
            ! $request->user()) {
            throw new AccessDeniedHttpException;
        }

        $channelName = Str::startsWith($request->channel_name, 'private-')
            ? Str::replaceFirst('private-', '', $request->channel_name)
            : Str::replaceFirst('presence-', '', $request->channel_name);

        return parent::verifyUserCanAccessChannel(
            $request, $channelName
        );
    }

    /**
     * Return the valid authentication response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  mixed $result
     * @return mixed
     *
     * @throws \Broadcastt\BroadcasttException
     */
    public function validAuthenticationResponse($request, $result)
    {
        if (Str::startsWith($request->channel_name, 'private')) {
            return $this->decodePusherResponse(
                $request,
                $this->broadcastt->privateAuth(
                    $request->channel_name, $request->socket_id
                )
            );
        }

        return $this->decodePusherResponse(
            $request,
            $this->broadcastt->presenceAuth(
                $request->channel_name, $request->socket_id, $request->user()->getAuthIdentifier(), $result
            )
        );
    }

    /**
     * Decode the given Pusher response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $response
     * @return array
     */
    protected function decodePusherResponse($request, $response)
    {
        if (! $request->callback) {
            return json_decode($response, true);
        }

        return response()->json(json_decode($response, true))
            ->withCallback($request->callback);
    }

    /**
     * Broadcast the given event.
     *
     * @param  array $channels
     * @param  string $event
     * @param  array $payload
     * @return void
     *
     * @throws \Broadcastt\BroadcasttException
     */
    public function broadcast(array $channels, $event, array $payload = [])
    {
        $socket = Arr::pull($payload, 'socket');

        $response = $this->broadcastt->event(
            $this->formatChannels($channels), $event, $payload, $socket
        );

        if ((is_array($response) && $response['status'] >= 200 && $response['status'] <= 299)
            || $response === true) {
            return;
        }

        throw new BroadcastException(
            is_bool($response) ? 'Failed to connect to Broadcastt.' : $response['body']
        );
    }
}
