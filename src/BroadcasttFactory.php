<?php

namespace Broadcastt\Laravel;

use Broadcastt\BroadcasttClient;

class BroadcasttFactory
{

    /**
     * @param $config
     * @return BroadcasttClient
     */
    public function create($config)
    {
        if (array_key_exists('cluster', $config)) {
            $client = new BroadcasttClient($config['id'], $config['key'], $config['secret'], $config['cluster']);
        } else {
            $client = new BroadcasttClient($config['id'], $config['key'], $config['secret']);
        }
        unset($config['id'], $config['key'], $config['secret'], $config['cluster']);

        if (array_key_exists('useTLS', $config) && $config['useTLS']) {
            $client->useTLS();
        }
        unset($config['useTLS']);

        foreach ($config as $param => $value) {
            $client->{$param} = $value;
        }

        return $client;
    }
}