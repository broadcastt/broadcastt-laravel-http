<?php
/**
 *
 */

namespace Broadcastt\Laravel;

use Broadcastt\BroadcasttClient;
use Illuminate\Config\Repository;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Illuminate\Contracts\Broadcasting\Factory as FactoryContract;

/**
 * @mixin BroadcasttClient
 */
class BroadcasttManager implements FactoryContract
{
    /**
     * @var Repository
     */
    private $config;

    /**
     * @var BroadcasttFactory
     */
    private $factory;

    /**
     * @var BroadcasttClient[]
     */
    private $clients = [];

    public function __construct(BroadcasttFactory $factory, Repository $config)
    {
        $this->factory = $factory;
        $this->config = $config;
    }

    /**
     * Get a client instance.
     *
     * @param string|null $name
     * @return BroadcasttClient
     */
    public function connection($name = null)
    {
        return $this->client($name);
    }

    /**
     * Get a client instance.
     *
     * @param string|null $name
     * @return BroadcasttClient
     */
    public function client($name = null)
    {
        if (!array_key_exists($name, $this->clients)) {
            $this->clients[$name] = $this->makeClient($name);
        }

        return $this->clients[$name];
    }

    /**
     * Make the connection instance.
     *
     * @param string $name
     *
     * @return BroadcasttClient
     */
    private function makeClient($name)
    {
        $config = $this->getClientConfig($name);

        return $this->factory->create($config);
    }

    /**
     * Get the configuration for a connection.
     *
     * @param string|null $name
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function getClientConfig(string $name = null)
    {
        $name = $name ?: $this->getDefaultClient();

        $connections = $this->config->get('broadcastt.connections');

        if (!is_array($config = Arr::get($connections, $name)) && !$config) {
            throw new InvalidArgumentException("Connection [$name] not configured.");
        }

        $config['name'] = $name;

        return $config;
    }

    /**
     * Get the default client name.
     *
     * @return string
     */
    public function getDefaultClient()
    {
        return $this->config->get('broadcastt.default');
    }

    /**
     * Set the default client name.
     *
     * @param string $name The client name
     * @return void
     */
    public function setDefaultClient($name)
    {
        $this->config->set('broadcastt.default', $name);
    }

    /**
     * Dynamically call the default client instance.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->connection()->$method(...$parameters);
    }
}
