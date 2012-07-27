<?php
/**
 * This file is part of the Ecommerce-Shipping package
 *
 * @package Ecommerce-Shipping
 */

/**
 * Free namespace
 */
namespace Heystack\Subsystem\Shipping\Free;

use Heystack\Subsystem\Shipping\Events;
use Heystack\Subsystem\Shipping\Interfaces\ShippingHandlerInterface;
use Heystack\Subsystem\Shipping\Traits\ShippingHandlerTrait;

use Heystack\Subsystem\Ecommerce\Transaction\TransactionModifierTypes;
use Heystack\Subsystem\Ecommerce\Transaction\Traits\TransactionModifierStateTrait;
use Heystack\Subsystem\Ecommerce\Transaction\Traits\TransactionModifierSerializeTrait;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Monolog\Logger;

use Heystack\Subsystem\Core\State\StateableInterface;
use Heystack\Subsystem\Core\State\State;

/**
 * An implementation of the ShippingHandlerInterface specific to 'free' shipping cost calculation
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Shipping
 */
class ShippingHandler implements ShippingHandlerInterface, StateableInterface, \Serializable
{
    use ShippingHandlerTrait;
    use TransactionModifierStateTrait;
    use TransactionModifierSerializeTrait;

    /**
     * Holds the key used for storing state
     */
    const IDENTIFIER = 'shipping';

    /**
     * Holds the key for storing all countries in the data array
     */
    const ALL_COUNTRIES_KEY = 'allcountries';

    /**
     * Holds the data array
     * @var array
     */
    protected $data = array();

    /**
     * Holds the name of the country class to be used
     * @var string
     */
    protected $countryClass;

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
    public function __construct($countryClass, EventDispatcherInterface $eventService, State $stateService, Logger $monologService = null)
    {
        $this->countryClass = $countryClass;
        $this->eventService = $eventService;
        $this->stateService = $stateService;
        $this->monologService = $monologService;
    }

    /**
     * Returns an array of field names that need to managed by the shipping subsystem.
     * @return array
     */
    public function getShippingFields()
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

    /**
     * Returns an associative array of the shipping fields and the data that is set up for them
     * @return array
     */
    public function getShippingFieldsData()
    {
        $data = array();

        foreach ($this->getShippingFields() as $shippingField) {
            $data[$shippingField] = $this->$shippingField;
        }

        return $data;
    }

    /**
     * If after restoring state no countries are loaded onto the data array get
     * them from the database and load them to the data array, and save the state.
     * @throws \Exception
     */
    protected function ensureDataExists()
    {
        if (!$this->data || !isset($this->data[self::ALL_COUNTRIES_KEY])) {
            $countries = \DataObject::get($this->countryClass);

            if ($countries instanceof \DataObjectSet && $countries->exists()) {

                foreach ($countries as $country) {
                    $this->data[self::ALL_COUNTRIES_KEY][$country->getIdentifier()] = $country;
                }

                $this->saveState();

            } else {

                if (isset($this->monologService)) {
                    $this->monologService->err('Please create some countries');
                }

                throw new \Exception('Please create some countries');
            }
        }
    }

    /**
     * Overrides the magic setter function for the Country field. Uses the cache for
     * retrieval and storage of the Country object
     * @param string $identifier
     */
    public function setCountry($identifier)
    {
        if ($country = $this->getCountry($identifier)) {

            $this->data['Country'] = $country;
        }
    }

    /**
     * Uses the identifier to retrive the country object from the cache
     * @param  type                                                                  $identifier
     * @return \Heystack\Subsystem\Shipping\CountryBased\Interfaces\CountryInterface
     */
    public function getCountry($identifier)
    {
        $this->ensureDataExists();

        return isset($this->data[self::ALL_COUNTRIES_KEY][$identifier]) ? $this->data[self::ALL_COUNTRIES_KEY][$identifier] : null;
    }

    /**
     * Returns an array of all countries from the cache
     * @return array
     */
    public function getCountries()
    {
        $this->ensureDataExists();

        return isset($this->data[self::ALL_COUNTRIES_KEY]) ? $this->data[self::ALL_COUNTRIES_KEY] : null;
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

        return number_format($total,2,'.','');
    }

    /**
     * Indicates the type of amount the modifier will return
     * Must return a constant from TransactionModifierTypes
     */
    public function getType()
    {
        return TransactionModifierTypes::NEUTRAL;
    }
}
