<?php
/**
 * This file is part of the Ecommerce-Shipping package
 *
 * @package Ecommerce-Shipping
 */

/**
 * CountryBased namespace
 */
namespace Heystack\Subsystem\Shipping\Types\CountryBased;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Heystack\Subsystem\Ecommerce\Currency\Events as CurrencyEvents;

use Heystack\Subsystem\Ecommerce\Transaction\Events as TransactionEvents;
use Heystack\Subsystem\Ecommerce\Transaction\Event\TransactionStoredEvent;

use Heystack\Subsystem\Shipping\Interfaces\ShippingHandlerInterface;

use Heystack\Subsystem\Core\Storage\Storage;

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
     * @var \Heystack\Subsystem\Shipping\Interfaces\ShippingHandlerInterface
     */
    protected $shippingService;

    /**
     * Holds the Storage Service
     * @var \Heystack\Subsystem\Core\Storage\Storage
     */
    protected $storageService;

    /**
     * Creates the ShippingHandler Subscriber object
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface      $eventService
     * @param \Heystack\Subsystem\Shipping\Interfaces\ShippingHandlerInterface $voucherHolder
     * @param \Heystack\Subsystem\Core\Storage\Storage                         $storageService
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
        return array(
            Events::TOTAL_UPDATED          => array('onTotalUpdated', 0),
            CurrencyEvents::CHANGED        => array('onTotalUpdated', 0),
            TransactionEvents::STORED      => array('onTransactionStored', 0)
        );
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
     * @param \Heystack\Subsystem\Ecommerce\Transaction\Event\TransactionStoredEvent $transaction
     */
    public function onTransactionStored(TransactionStoredEvent $event)
    {
//        $voucherHolderID = $this->storageService->process($this->voucherHolder, false, $event->getTransactionID());
//
//        if ($this->voucherHolder->getVouchers()) {
//
//            foreach ($this->voucherHolder->getVouchers() as $voucher) {
//
//                $this->storageService->process($voucher, false, $voucherHolderID);
//
//            }
//
//        }
//
//        $this->eventService->dispatch(Events::STORED);
    }

}
