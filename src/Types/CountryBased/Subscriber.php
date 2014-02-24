<?php
/**
 * This file is part of the Ecommerce-Shipping package
 *
 * @package Ecommerce-Shipping
 */

/**
 * CountryBased namespace
 */
namespace Heystack\Shipping\Types\CountryBased;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Heystack\Ecommerce\Currency\Events as CurrencyEvents;
use Heystack\Ecommerce\Locale\Events as LocaleEvents;
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
    /**
     * Holds the Event Dispatcher Service
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventService;

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
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface      $eventService
     * @param \Heystack\Shipping\Interfaces\ShippingHandlerInterface $shippingService
     * @param \Heystack\Core\Storage\Storage                         $storageService
     */
    public function __construct(EventDispatcherInterface $eventService, ShippingHandlerInterface $shippingService,  Storage $storageService)
    {
        $this->eventService = $eventService;
        $this->shippingService = $shippingService;
        $this->storageService = $storageService;
    }

    /**
     * Returns an array of events to subscribe to and the methods to call when those events are fired
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CurrencyEvents::CHANGED        => ['onTotalUpdated', 0],
            LocaleEvents::CHANGED          => ['onTotalUpdated', 0],
            Backend::IDENTIFIER . '.' . TransactionEvents::STORED      => ['onTransactionStored', 0]
        ];
    }

    /**
     * Called after the ShippingHandler's total is updated.
     * Tells the transaction to update its total.
     */
    public function onTotalUpdated()
    {
        $this->eventService->dispatch(TransactionEvents::UPDATE);
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
    }

}
