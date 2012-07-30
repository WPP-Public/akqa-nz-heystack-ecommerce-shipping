<?php
/**
 * This file is part of the Ecommerce-Shipping package
 *
 * @package Ecommerce-Shipping
 */

/**
 * CountryBased namespace
 */
namespace Heystack\Subsystem\Shipping\Free;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Heystack\Subsystem\Ecommerce\Transaction\Events as TransactionEvents;

use Heystack\Subsystem\Core\Storage\Event as StorageEvent;

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
            TransactionEvents::STORED      => array('onTransactionStored', 0)
        );
    }
    
    /**
     * Called after the Transaction is stored.
     * Tells the storage service to store all the information held in the ShippingHandler
     * @param \Heystack\Subsystem\Core\Storage\Event $event
     */
    public function onTransactionStored(StorageEvent $event)
    {   
        
        $this->shippingService->setParentReference($event->getParentReference());
        
        $this->storageService->process($this->shippingService);
        
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
