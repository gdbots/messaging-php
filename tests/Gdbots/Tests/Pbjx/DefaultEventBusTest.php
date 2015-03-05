<?php

namespace Gdbots\Tests\Pbjx;

use Gdbots\Pbjx\Domain\Event\EventExecutionFailedV1;
use Gdbots\Pbjx\Event\EventBusExceptionEvent;
use Gdbots\Pbjx\Pbjx;
use Gdbots\Pbjx\PbjxEvents;
use Gdbots\Tests\Pbjx\Fixtures\FailingEvent;
use Gdbots\Tests\Pbjx\Fixtures\SimpleEvent;
use Gdbots\Tests\Pbjx\Mock\ServiceLocatorMock;

class DefaultEventBusTest extends \PHPUnit_Framework_TestCase
{
    /** @var ServiceLocatorMock */
    protected $locator;

    /** @var Pbjx */
    protected $pbjx;

    protected function setup()
    {
        $this->locator = new ServiceLocatorMock();
        $this->pbjx = $this->locator->getPbjx();
    }

    public function testPublish()
    {
        $event = SimpleEvent::create()->setName('homer');
        $that = $this;
        $dispatcher = $this->locator->getDispatcher();

        $schemaId = $event::schema()->getId();
        $curie = $schemaId->getCurie();
        $vendor = $curie->getVendor();
        $package = $curie->getPackage();
        $category = $curie->getCategory();
        $called = 0;

        $func = function (SimpleEvent $publishedEvent) use ($that, $event, &$called) {
            $called++;
            $that->assertSame($publishedEvent, $event);
        };

        $dispatcher->addListener($schemaId->getResolverKey(), $func);
        $dispatcher->addListener($curie->toString(), $func);
        $dispatcher->addListener(sprintf('%s:%s:%s:*', $vendor, $package, $category), $func);
        $dispatcher->addListener(sprintf('%s:%s:*', $vendor, $package), $func);
        $dispatcher->addListener(sprintf('%s:*', $vendor), $func);
        $this->pbjx->publish($event);

        $this->assertEquals(5, $called);
    }

    public function testEventExecutionFailedV1()
    {
        $event = FailingEvent::create()->setName('homer');
        $dispatcher = $this->locator->getDispatcher();
        $schemaId = $event::schema()->getId();
        $handled = false;

        $dispatcher->addListener(
            $schemaId->getResolverKey(),
            function () {
                throw new \LogicException('Simulate failure.');
            }
        );

        $dispatcher->addListener(
            EventExecutionFailedV1::schema()->getId()->getResolverKey(),
            function () use (&$handled) {
                $handled = true;
            }
        );

        $this->pbjx->publish($event);
        $this->assertTrue(
            $handled,
            sprintf(
                '%s failed because the event [%s] was never published.',
                __FUNCTION__,
                $schemaId->getResolverKey()
            )
        );
    }

    public function testEventBusExceptionEvent()
    {
        $event = FailingEvent::create()->setName('marge');
        $that = $this;
        $dispatcher = $this->locator->getDispatcher();
        $schemaId = $event::schema()->getId();

        $dispatcher->addListener(
            $schemaId->getResolverKey(),
            function () {
                throw new \LogicException('Simulate failure.');
            }
        );

        $dispatcher->addListener(
            EventExecutionFailedV1::schema()->getId()->getResolverKey(),
            function () {
                throw new \LogicException('Failed to handle EventExecutionFailedV1.');
            }
        );

        $dispatcher->addListener(
            PbjxEvents::EVENT_EXCEPTION,
            function (EventBusExceptionEvent $exceptionEvent) use ($that, $event) {
                /** @var EventExecutionFailedV1 $domainEvent */
                $domainEvent = $exceptionEvent->getDomainEvent();
                $that->assertSame(
                    $domainEvent->getFailedEvent()->get(SimpleEvent::NAME_FIELD_NAME),
                    $event->getName()
                );
            }
        );

        $this->pbjx->publish($event);
    }
}