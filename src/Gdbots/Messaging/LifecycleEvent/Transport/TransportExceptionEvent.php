<?php

namespace Gdbots\Messaging\LifecycleEvent\Transport;

use Gdbots\Messaging\MessageInterface;

class TransportExceptionEvent extends TransportEvent
{
    /* @var \Exception */
    protected $exception;

    /**
     * @param string $transportName
     * @param MessageInterface $message
     * @param \Exception $e
     */
    public function __construct($transportName, MessageInterface $message, \Exception $e)
    {
        parent::__construct($transportName, $message);
        $this->exception = $e;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}