<?php

namespace Gdbots\Pbjx;

use Gdbots\Pbj\Extension\DomainEvent;
use Gdbots\Pbjx\Exception\GdbotsPbjxException;

interface EventBus
{
    /**
     * Publishes events to all subscribers.
     *
     * @param DomainEvent $event
     * @throws GdbotsPbjxException
     * @throws \Exception
     */
    public function publish(DomainEvent $event);

    /**
     * Processes an event directly.  DO NOT use this method in the
     * application as this is intended for the transports, consumers
     * and workers of the Pbjx system.
     *
     * @param DomainEvent $event
     * @throws GdbotsPbjxException
     * @throws \Exception
     */
    public function receiveEvent(DomainEvent $event);
}