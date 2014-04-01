<?php

namespace Heystack\Shipping\Types\CountryBased;

use Heystack\Core\State\State;
use Heystack\Core\Storage\Backends\SilverStripeOrm\Backend;
use Heystack\Core\Storage\Event as StorageEvent;
use Heystack\Core\Storage\Storage;
use Heystack\Core\Traits\HasEventServiceTrait;
use Heystack\Core\Traits\HasStateServiceTrait;
use Heystack\Ecommerce\Transaction\Events as TransactionEvents;
use Heystack\Shipping\Events;
use Heystack\Shipping\Interfaces\ShippingHandlerInterface;
use Heystack\Shipping\Traits\HasShippingHandlerTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Handles both subscribing to events and acting on those events needed for ShippingHandler to work properly
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Shipping
 * @see Symfony\Component\EventDispatcher
 */
class Subscriber implements EventSubscriberInterface
{
    use HasEventServiceTrait;
    use HasShippingHandlerTrait;
    use HasStateServiceTrait;

    /**
     * Creates the ShippingHandler Subscriber object
     * @param EventDispatcherInterface $eventService
     * @param ShippingHandlerInterface $shippingHandler
     * @param Storage $storageService
     * @param \Heystack\Core\State\State $stateService
     */
    public function __construct(
        EventDispatcherInterface $eventService,
        ShippingHandlerInterface $shippingHandler,
        Storage $storageService,
        State $stateService
    )
    {
        $this->eventService = $eventService;
        $this->shippingHandler = $shippingHandler;
        $this->storageService = $storageService;
        $this->stateService = $stateService;
    }

    /**
     * Returns an array of events to subscribe to and the methods to call when those events are fired
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            sprintf('%s.%s', Backend::IDENTIFIER, TransactionEvents::STORED) => ['onTransactionStored', 0]
        ];
    }

    /**
     * Called after the Transaction is stored.
     * Tells the storage service to store all the information held in the ShippingHandler
     * @param \Heystack\Core\Storage\Event $event
     */
    public function onTransactionStored(StorageEvent $event)
    {
        $this->shippingHandler->setParentReference($event->getParentReference());
        $this->storageService->process($this->shippingHandler);
        $this->eventService->dispatch(Events::STORED);
        $this->stateService->removeByKey(ShippingHandler::IDENTIFIER);
    }
}
