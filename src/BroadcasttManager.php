<?php
/**
 *
 */

namespace Broadcastt\Laravel;

use Broadcastt\BroadcasttException;

class BroadcasttManager
{
    /**
     * @var \Broadcastt\Broadcastt[]
     */
    private $connections = [];

    /**
     * @var string The name of the default connection
     */
    protected $default;

    public function __construct($defaultConnection)
    {
        $this->default = 'default';

        $this->connections[$this->default] = $defaultConnection;
    }

    /**
     * Returns a connection instance
     *
     * @param string $connection Name of the connection
     *
     * @return \Broadcastt\Broadcastt A connection instance
     */
    public function on($connection = null)
    {
        if (is_null($connection)) {
            return $this->connections[$this->default];
        }

        return $this->connections[$connection];
    }

    /**
     * Trigger an event by providing event name and payload.
     * Optionally provide a socket ID to exclude a client (most likely the sender).
     *
     * @param array|string $channels A channel name or an array of channel names to publish the event on.
     * @param string $name Name of the event
     * @param mixed $data Event data
     * @param string|null $socketId [optional]
     * @param bool $jsonEncoded [optional]
     *
     * @throws BroadcasttException Throws exception if $channels is an array of size 101 or above or $socketId is invalid
     *
     * @return bool|array
     */
    public function event($channels, $name, $data, $socketId = null, $jsonEncoded = false)
    {
        return $this->on($this->default)->event($channels, $name, $data, $socketId, $jsonEncoded);
    }

    /**
     * Trigger multiple events at the same time.
     *
     * @param array $batch [optional] An array of events to send
     * @param bool $encoded [optional] Defines if the data is already encoded
     *
     * @throws BroadcasttException Throws exception if curl wasn't initialized correctly
     *
     * @return array|bool|string
     */
    public function eventBatch($batch = [], $encoded = false)
    {
        return $this->on($this->default)->eventBatch($batch, $encoded);
    }

    /**
     * GET arbitrary REST API resource using a synchronous http client.
     * All request signing is handled automatically.
     *
     * @param string $path Path excluding /apps/APP_ID
     * @param array $params API params (see https://broadcastt.xyz/docs/References-‐-Rest-API )
     *
     * @throws BroadcasttException Throws exception if curl wasn't initialized correctly
     *
     * @return array|bool See Broadcastt API docs
     */
    public function get($path, $params = [])
    {
        return $this->on($this->default)->get($path, $params);
    }
}
