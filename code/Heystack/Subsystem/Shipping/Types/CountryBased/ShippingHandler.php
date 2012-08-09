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

use Heystack\Subsystem\Shipping\Interfaces\ShippingHandlerInterface;
use Heystack\Subsystem\Shipping\Traits\ShippingHandlerTrait;

use Heystack\Subsystem\Ecommerce\Transaction\TransactionModifierTypes;
use Heystack\Subsystem\Ecommerce\Transaction\Traits\TransactionModifierStateTrait;
use Heystack\Subsystem\Ecommerce\Transaction\Traits\TransactionModifierSerializeTrait;
use Heystack\Subsystem\Ecommerce\Locale\LocaleHandler;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Monolog\Logger;

use Heystack\Subsystem\Core\State\StateableInterface;
use Heystack\Subsystem\Core\State\State;
use Heystack\Subsystem\Core\ViewableData\ViewableDataInterface;


/**
 * An implementation of the ShippingHandlerInterface specific to 'country based' shipping cost calculation
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Shipping
 */
class ShippingHandler implements ShippingHandlerInterface, StateableInterface, \Serializable, ViewableDataInterface
{
    use ShippingHandlerTrait;
    use TransactionModifierStateTrait;
    use TransactionModifierSerializeTrait;

    /**
     * Holds the key used for storing state
     */
    const IDENTIFIER = 'shipping';

    /**
     * Holds the data array
     * @var array
     */
    protected $data = array();
    
    /**
     * Holds the locale handler object
     * @var \Heystack\Subsystem\Ecommerce\Locale\LocaleHandler 
     */
    protected $localeHandler;

    /**
     * Holds the event dispatcher service object
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventService;

    /**
     * Holds the state service object
     * @var \Heystack\Subsystem\Core\State\State
     */
    protected $stateService;

    /**
     * Holds the monolog logger service object
     * @var \Monolog\Logger
     */
    protected $monologService;

    /**
     * Creates the ShippingHandler object
     * @param string                                                      $countryClass
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventService
     * @param \Heystack\Subsystem\Core\State\State                        $stateService
     * @param \Monolog\Logger                                             $monologService
     */
    public function __construct(LocaleHandler $localeHandler, EventDispatcherInterface $eventService, State $stateService, Logger $monologService = null)
    {
        $this->localeHandler = $localeHandler;
        $this->eventService = $eventService;
        $this->stateService = $stateService;
        $this->monologService = $monologService;
    }

    /**
     * Returns an array of field names that need to managed by the shipping subsystem.
     * @return array
     */
    public function getDynamicMethods()
    {
        return array(
            'AddressLine1',
            'AddressLine2',
            'City',
            'Postcode',
            'Country',
            'Title',
            'FirstName',
            'Surname',
            'Email',
            'Phone'
        );
    }

    public function getCastings()
    {
        return array(
            'AddressLine1' => 'Text',
            'AddressLine2' => 'Text',
            'City' => 'Text',
            'Postcode' => 'Text',
            'Country' => 'Text',
            'Title' => 'Text',
            'FirstName' => 'Text',
            'Surname' => 'Text',
            'Email' => 'Text',
            'Phone' => 'Text'
        );
    }
    
    /**
     * Overrides the magic setter function for the Country field. Uses the cache for
     * retrieval and storage of the Country object
     * @param string $identifier
     */
    public function setCountry($identifier)
    {
        $this->localeHandler->setActiveCountry($identifier);
    }

    /**
     * Uses the identifier to retrive the country object from the cache
     * @param  type                                                                  $identifier
     * @return \Heystack\Subsystem\Shipping\CountryBased\Interfaces\CountryInterface
     */    
    public function getCountry()
    {
        return $this->localeHandler->getActiveCountry();
    }
    
    /**
     * Returns a unique identifier for use in the Transaction
     */
    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    /**
     * Returns the total value of the TransactionModifier for use in the Transaction
     */
    public function getTotal()
    {
        $total = 0;
        
        $countryClass = $this->localeHandler->getCountryClass();

        if ($this->Country instanceof $countryClass) {
            $total = $this->Country->getShippingCost();
        }

        return $total;
    }

    /**
     * Indicates the type of amount the modifier will return
     * Must return a constant from TransactionModifierTypes
     */
    public function getType()
    {
        return TransactionModifierTypes::CHARGEABLE;
    }
}
