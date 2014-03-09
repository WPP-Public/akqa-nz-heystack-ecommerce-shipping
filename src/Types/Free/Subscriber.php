<?php
/**
 * This file is part of the Ecommerce-Shipping package
 *
 * @package Ecommerce-Shipping
 */

/**
 * CountryBased namespace
 */
namespace Heystack\Shipping\Types\Free;

use Heystack\Core\State\State;
use Heystack\Core\Traits\HasEventServiceTrait;
use Heystack\Core\Traits\HasStateServiceTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Heystack\Ecommerce\Transaction\Events as TransactionEvents;
use Heystack\Shipping\Interfaces\ShippingHandlerInterface;
use Heystack\Core\Storage\Storage;
use Heystack\Core\Storage\Event as StorageEvent;

use Heystack\Core\Storage\Backends\SilverStripeOrm\Backend;
use Heystack\Shipping\Events;

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
    use HasStateServiceTrait;

    /**
     * Holds the ShippingHandler Service
     * @var \Heystack\Shipping\Interfaces\ShippingHandlerInterface
     */
    protected $shippingService;

    /**
     * Holds the Storage Service
     * @var \Heystack\Core\Storage\Storage
     */
    protected $storageService;

    /**
     * Creates the ShippingHandler Subscriber object
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventService
     * @param \Heystack\Shipping\Interfaces\ShippingHandlerInterface $shippingService
     * @param \Heystack\Core\Storage\Storage $storageService
     * @param \Heystack\Core\State\State $stateService
     */
    public function __construct(
        EventDispatcherInterface $eventService,
        ShippingHandlerInterface $shippingService,
        Storage $storageService,
        State $stateService
    )
    {
        $this->eventService = $eventService;
        $this->shippingService = $shippingService;
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
            Backend::IDENTIFIER . '.' . TransactionEvents::STORED      => ['onTransactionStored', 0]
        ];
    }

    /**
     * Called after the Transaction is stored.
     * Tells the storage service to store all the information held in the ShippingHandler
     * @param \Heystack\Core\Storage\Event $event
     */
    public function onTransactionStored(StorageEvent $event)
    {
        $this->shippingService->setParentReference($event->getParentReference());
        $this->storageService->process($this->shippingService);
        $this->eventService->dispatch(Events::STORED);
        $this->stateService->removeByKey(ShippingHandler::IDENTIFIER);
    }
}
