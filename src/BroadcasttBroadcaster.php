<?php
/**
 *
 */

namespace Broadcastt\Laravel;

use Broadcastt\BroadcasttClient;
use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class BroadcasttBroadcaster extends Broadcaster
{
    /**
     * The Broadcastt SDK instance.
     *
     * @var BroadcasttClient
     */
    private $client;

    /**
     * BroadcasttBroadcaster constructor.
     *
     * @param $client
     */
    public function __construct(BroadcasttClient $client)
    {
        $this->client = $client;
    }

    /**
     * Authenticate the incoming request for a given channel.
     *
     * @param  Request $request
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
     * @param  Request $request
     * @param  mixed $result
     * @return mixed
     */
    public function validAuthenticationResponse($request, $result)
    {
        if (Str::startsWith($request->channel_name, 'private-')) {
            return $this->decodeBroadcasttResponse(
                $request,
                $this->client->privateAuth(
                    $request->channel_name, $request->socket_id
                )
            );
        }

        return $this->decodeBroadcasttResponse(
            $request,
            $this->client->presenceAuth(
                $request->channel_name, $request->socket_id, $request->user()->getAuthIdentifier(), $result
            )
        );
    }

    /**
     * Decode the given Broadcastt response.
     *
     * @param  Request  $request
     * @param  mixed  $response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function decodeBroadcasttResponse($request, $response)
    {
        if (! $request->input('callback', false)) {
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
     */
    public function broadcast(array $channels, $event, array $payload = [])
    {
        $socket = Arr::pull($payload, 'socket');

        $success = $this->client->trigger(
            $this->formatChannels($channels), $event, $payload, $socket
        );

        if ($success === true) {
            return;
        }

        throw new BroadcastException(
            is_bool($success) ? 'Failed to connect to Broadcastt.' : $success['body']
        );
    }

    /**
     * @return BroadcasttClient
     */
    public function getClient()
    {
        return $this->client;
    }
}
