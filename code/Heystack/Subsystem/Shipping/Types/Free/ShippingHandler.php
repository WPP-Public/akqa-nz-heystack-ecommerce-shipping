<?php
/**
 * This file is part of the Ecommerce-Shipping package
 *
 * @package Ecommerce-Shipping
 */

/**
 * Free namespace
 */
namespace Heystack\Subsystem\Shipping\Types\Free;

use Heystack\Subsystem\Core\Identifier\Identifier;
use Heystack\Subsystem\Shipping\Interfaces\ShippingHandlerInterface;
use Heystack\Subsystem\Shipping\Traits\ShippingHandlerTrait;

use Heystack\Subsystem\Ecommerce\Transaction\TransactionModifierTypes;
use Heystack\Subsystem\Ecommerce\Transaction\Traits\TransactionModifierStateTrait;
use Heystack\Subsystem\Ecommerce\Transaction\Traits\TransactionModifierSerializeTrait;
use Heystack\Subsystem\Ecommerce\Locale\LocaleService;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Monolog\Logger;

use Heystack\Subsystem\Core\State\StateableInterface;
use Heystack\Subsystem\Core\State\State;
use Heystack\Subsystem\Core\ViewableData\ViewableDataInterface;
use Heystack\Subsystem\Core\Storage\StorableInterface;
use Heystack\Subsystem\Core\Storage\Backends\SilverStripeOrm\Backend;
use Heystack\Subsystem\Core\Storage\Traits\ParentReferenceTrait;

/**
 * An implementation of the ShippingHandlerInterface specific to 'free' shipping cost calculation
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Shipping
 */
class ShippingHandler implements ShippingHandlerInterface, StateableInterface, \Serializable, ViewableDataInterface, StorableInterface
{
    use ShippingHandlerTrait;
    use TransactionModifierStateTrait;
    use TransactionModifierSerializeTrait;
    use ParentReferenceTrait;

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
     * Holds the locale service object
     * @var \Heystack\Subsystem\Ecommerce\Locale\LocaleService
     */
    protected $localeService;

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
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventService
     * @param \Heystack\Subsystem\Core\State\State                        $stateService
     * @param \Monolog\Logger                                             $monologService
     */
    public function __construct(LocaleService $localeService, EventDispatcherInterface $eventService, State $stateService, Logger $monologService = null)
    {
        $this->localeService = $localeService;
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
            'Phone',
            
            'BillingAsShipping',
            'BillingAddressLine1',
            'BillingAddressLine2',
            'BillingCity',
            'BillingPostcode',
            'BillingCountry',
            'BillingTitle',
            'BillingFirstName', 
            'BillingSurname',
            'BillingEmail',
            'BillingPhone'
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
            'Phone' => 'Text',
            
            'BillingAsShipping' => 'Boolean',
            'BillingAddressLine1' => 'Text',
            'BillingAddressLine2' => 'Text',
            'BillingCity' => 'Text',
            'BillingPostcode' => 'Text',
            'BillingCountry' => 'Text',
            'BillingTitle' => 'Text',
            'BillingFirstName' => 'Text',
            'BillingSurname' => 'Text',
            'BillingEmail' => 'Text',
            'BillingPhone' => 'Text'
        );
    }

    /**
     * Overrides the magic setter function for the Country field. Uses the cache for
     * retrieval and storage of the Country object
     * @param string $identifier
     */
    public function setCountry($identifier)
    {
        $this->localeService->setActiveCountry($identifier);
    }

    /**
     * Retrive the country object from the cache
     * @param  type                                                                  $identifier
     * @return \Heystack\Subsystem\Shipping\CountryBased\Interfaces\CountryInterface
     */
    public function getCountry()
    {
        return $this->localeService->getActiveCountry();
    }

    /**
     * Returns a unique identifier for use in the Transaction
     * @return \Heystack\Subsystem\Core\Identifier\Identifier
     */
    public function getIdentifier()
    {
        return new Identifier(self::IDENTIFIER);
    }

    /**
     * Returns the total value of the TransactionModifier for use in the Transaction
     */
    public function getTotal()
    {
        $total = 0;

        return $total;
    }

    /**
     * Indicates the type of amount the modifier will return
     * Must return a constant from TransactionModifierTypes
     */
    public function getType()
    {
        return TransactionModifierTypes::NEUTRAL;
    }

    public function getStorableData()
    {

        return array(
            'id' => 'ShippingHandler',
            'parent' => true,
            'flat' => array(
                'ParentID' => $this->parentReference,
                'AddressLine1' => $this->AddressLine1,
                'AddressLine2' => $this->AddressLine2,
                'City' => $this->City,
                'Postcode' => $this->Postcode,
                'Country' => !is_null($this->Country) ? $this->Country->getName() : null,
                'Title' => $this->Title,
                'FirstName' => $this->FirstName,
                'Surname' => $this->Surname,
                'Email' => $this->Email,
                'Phone' => $this->Phone,
                'Total' => $this->getTotal(),
                
                'BillingAsShipping' => $this->BillingAsShipping,
                'BillingAddressLine1' => $this->BillingAddressLine1,
                'BillingAddressLine2' => $this->BillingAddressLine2,
                'BillingCity' => $this->BillingCity,
                'BillingPostcode' => $this->BillingPostcode,
                'BillingCountry' => !is_null($this->BillingCountry) ? $this->localeService->getCountry($this->BillingCountry)->getName() : null,
                'BillingTitle' => $this->BillingTitle,
                'BillingFirstName' => $this->BillingFirstName,
                'BillingSurname' => $this->BillingSurname,
                'BillingEmail' => $this->BillingEmail,
                'BillingPhone' => $this->BillingPhone,
           )
       );

    }

    public function getStorableIdentifier()
    {

        return self::IDENTIFIER;

    }

    /**
     * Get the name of the schema this system relates to
     * @return string
     */
    public function getSchemaName()
    {

        return 'Shipping';

    }

    public function getStorableBackendIdentifiers()
    {
        return array(
            Backend::IDENTIFIER
        );
    }

}
