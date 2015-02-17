<?php

namespace Gdbots\Pbjx;

final class PbjxEvents
{
    /**
     * Private constructor. This class is not meant to be instantiated.
     */
    private function __construct() {}

    /**
     * Occurs prior to command being sent to the transport.
     * @see Gdbots\Pbjx\Event\ValidateCommandEvent
     *
     * @var string
     */
    const COMMAND_VALIDATE = 'gdbots.pbjx.command.validate';

    /**
     * Occurs after validation and prior to command being sent to the transport.
     * @see Gdbots\Pbjx\Event\EnrichCommandEvent
     *
     * @var string
     */
    const COMMAND_ENRICH = 'gdbots.pbjx.command.enrich';

    /**
     * Occurs before command is sent to the handler.
     * @see Gdbots\Pbjx\Event\CommandBusEvent
     *
     * @var string
     */
    const COMMAND_BEFORE_HANDLE = 'gdbots.pbjx.command.before_handle';

    /**
     * Occurs after command has been successfully sent to the handler.
     * @see Gdbots\Pbjx\Event\CommandBusEvent
     *
     * @var string
     */
    const COMMAND_AFTER_HANDLE = 'gdbots.pbjx.command.after_handle';

    /**
     * Occurs prior to an expection being thrown during the handling phase of a command.  This
     * is not announced during validate, enrich or the transport send.
     *
     * @see Gdbots\Pbjx\Event\CommandBusExceptionEvent
     *
     * @var string
     */
    const COMMAND_HANDLE_EXCEPTION = 'gdbots.pbjx.command.exception';
}