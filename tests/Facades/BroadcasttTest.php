<?php

namespace Tests\Facades;

use Broadcastt\Laravel\BroadcasttManager;
use Broadcastt\Laravel\Facades\Broadcastt;
use GrahamCampbell\TestBenchCore\FacadeTrait;
use Tests\TestCase;

class BroadcasttTest extends TestCase
{
    use FacadeTrait;

    /**
     * Get the facade accessor.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return BroadcasttManager::class;
    }

    /**
     * Get the facade class.
     *
     * @return string
     */
    protected static function getFacadeClass(): string
    {
        return Broadcastt::class;
    }

    /**
     * Get the facade root.
     *
     * @return string
     */
    protected static function getFacadeRoot(): string
    {
        return BroadcasttManager::class;
    }
}