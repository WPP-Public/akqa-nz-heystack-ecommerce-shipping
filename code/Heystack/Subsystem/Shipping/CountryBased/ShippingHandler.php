<?php
/**
 * This file is part of the Ecommerce-Shipping package
 *
 * @package Ecommerce-Shipping
 */

/**
 * Interfaces namespace
 */
namespace Heystack\Subsystem\Shipping\CountryBased;

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

class ShippingHandler implements ShippingHandlerInterface, StateableInterface, \Serializable
{
    use ShippingHandlerTrait;
    use TransactionModifierStateTrait;
    use TransactionModifierSerializeTrait;
    
    /**
     * Holds the key used for storing state
     */
    const STATE_KEY = 'shipping';
    const ALL_COUNTRIES_KEY = 'allcountries';
    
    protected $data = array();    
    
    protected $countryClass;
    protected $eventService;
    protected $stateService;
    protected $monologService;
    
    
    public function __construct($countryClass, EventDispatcherInterface $eventService, State $stateService, Logger $monologService = null)
    {
        $this->countryClass = $countryClass;
        $this->eventService = $eventService;
        $this->stateService = $stateService;
        $this->monologService = $monologService;
    }
    
    public function getShippingFields(){
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
    
    public function getShippingFieldsData()
    {
        $data = array();
        
        foreach($this->getShippingFields() as $shippingField){
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
                
                if(isset($this->monologService)){
                    $this->monologService->err('Please create some countries');
                }
                
                throw new \Exception('Please create some countries');
            }
        }        
    }
    
    public function setCountry($identifier)
    {
        if($country = $this->getCountry($identifier)){
            
            $this->data['Country'] = $country;
            
            $this->eventService->dispatch(Events::TOTAL_UPDATED);
        }
    }
    
    public function getCountry($identifier)
    {
        $this->ensureDataExists();
        
        return isset($this->data[self::ALL_COUNTRIES_KEY][$identifier]) ? $this->data[self::ALL_COUNTRIES_KEY][$identifier] : null;
    }
    
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
        return self::STATE_KEY;
    }
    
    /**
     * Returns the total value of the TransactionModifier for use in the Transaction
     */
    public function getTotal()
    {
        $total = 0;
        
        if( $this->Country instanceof $this->countryClass){
            $total = $this->Country->getShippingCost();
        }
        
        return number_format($total,2,'.','');
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

