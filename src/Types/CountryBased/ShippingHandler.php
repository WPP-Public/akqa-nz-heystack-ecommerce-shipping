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

use Heystack\Core\Identifier\Identifier;
use Heystack\Core\Identifier\IdentifierInterface;
use Heystack\Core\Interfaces\HasDataInterface;
use Heystack\Core\Interfaces\HasEventServiceInterface;
use Heystack\Core\Interfaces\HasLoggerServiceInterface;
use Heystack\Core\Interfaces\HasStateServiceInterface;
use Heystack\Core\State\State;
use Heystack\Core\Storage\Backends\SilverStripeOrm\Backend;
use Heystack\Core\Storage\Traits\ParentReferenceTrait;
use Heystack\Core\Traits\HasEventServiceTrait;
use Heystack\Core\Traits\HasLoggerServiceTrait;
use Heystack\Core\Traits\HasStateServiceTrait;
use Heystack\Core\ViewableData\ViewableDataInterface;
use Heystack\Ecommerce\Currency\Interfaces\CurrencyServiceInterface;
use Heystack\Ecommerce\Currency\Traits\HasCurrencyServiceTrait;
use Heystack\Ecommerce\Locale\Interfaces\HasLocaleServiceInterface;
use Heystack\Ecommerce\Locale\Interfaces\LocaleServiceInterface;
use Heystack\Ecommerce\Locale\Traits\HasLocaleServiceTrait;
use Heystack\Ecommerce\Transaction\Traits\TransactionModifierSerializeTrait;
use Heystack\Ecommerce\Transaction\Traits\TransactionModifierStateTrait;
use Heystack\Ecommerce\Transaction\TransactionModifierTypes;
use Heystack\Shipping\Interfaces\ShippingHandlerInterface;
use Heystack\Shipping\Traits\ShippingHandlerTrait;
use Heystack\Shipping\Types\CountryBased\Interfaces\CountryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * An implementation of the ShippingHandlerInterface specific to 'country based' shipping cost calculation
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Shipping
 */
class ShippingHandler
    implements
        ShippingHandlerInterface,
        \Serializable,
        ViewableDataInterface,
        HasDataInterface,
        HasStateServiceInterface,
        HasEventServiceInterface,
        HasLocaleServiceInterface,
        HasLoggerServiceInterface
{
    use ShippingHandlerTrait;
    use TransactionModifierStateTrait;
    use TransactionModifierSerializeTrait;
    use ParentReferenceTrait;
    use HasStateServiceTrait;
    use HasEventServiceTrait;
    use HasLocaleServiceTrait;
    use HasLoggerServiceTrait;
    use HasCurrencyServiceTrait;

    /**
     * Holds the key used for storing state
     */
    const IDENTIFIER = 'shipping';

    /**
     * Creates the ShippingHandler object
     * @param LocaleServiceInterface $localeService
     * @param EventDispatcherInterface $eventService
     * @param State $stateService
     * @param CurrencyServiceInterface $currencyService
     */
    public function __construct(
        LocaleServiceInterface $localeService,
        EventDispatcherInterface $eventService,
        State $stateService,
        CurrencyServiceInterface $currencyService
    ) {
        $this->localeService = $localeService;
        $this->eventService = $eventService;
        $this->stateService = $stateService;
        $this->currencyService = $currencyService;
    }

    /**
     * Returns an array of field names that need to managed by the shipping subsystem.
     * @return array
     */
    public function getDynamicMethods()
    {
        return [
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
        ];
    }

    public function getCastings()
    {
        return [
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
        ];
    }

    /**
     * Overrides the magic setter function for the Country field. Uses the cache for
     * retrieval and storage of the Country object
     * @param \Heystack\Core\Identifier\IdentifierInterface $identifier
     */
    public function setCountry(IdentifierInterface $identifier)
    {
        $this->localeService->setActiveCountry($identifier);
    }

    /**
     * Uses the identifier to retrive the country object from the cache
     * @return \Heystack\Ecommerce\Locale\Interfaces\CountryInterface
     */
    public function getCountry()
    {
        return $this->localeService->getActiveCountry();
    }

    /**
     * Returns a unique identifier for use in the Transaction
     * @return \Heystack\Core\Identifier\Identifier
     */
    public function getIdentifier()
    {
        return new Identifier(self::IDENTIFIER);
    }

    /**
     * Returns the total value of the TransactionModifier for use in the Transaction
     * @return \SebastianBergmann\Money\Money
     */
    public function getTotal()
    {
        $country = $this->getCountry();

        if ($country instanceof CountryInterface) {
            $total = $country->getShippingCost();
        } else {
            $total = $this->currencyService->getZeroMoney();
        }

        return $total;
    }

    /**
     * Indicates the type of amount the modifier will return
     * Must return a constant from TransactionModifierTypes
     * @return string
     */
    public function getType()
    {
        return TransactionModifierTypes::CHARGEABLE;
    }

    /**
     * @return array
     */
    public function getStorableData()
    {
        return [
            'id' => 'ShippingHandler',
            'parent' => true,
            'flat' => [
                'ParentID' => $this->parentReference,
                'AddressLine1' => $this->AddressLine1,
                'AddressLine2' => $this->AddressLine2,
                'City' => $this->City,
                'Postcode' => $this->Postcode,
                'Country' => !is_null($this->getCountry()) ? $this->getCountry()->getName() : null,
                'Title' => $this->Title,
                'FirstName' => $this->FirstName,
                'Surname' => $this->Surname,
                'Email' => $this->Email,
                'Phone' => $this->Phone,
                'Total' => \Heystack\Ecommerce\convertMoneyToString($this->getTotal()),
                
                'BillingAsShipping' => $this->BillingAsShipping,
                'BillingAddressLine1' => $this->BillingAddressLine1,
                'BillingAddressLine2' => $this->BillingAddressLine2,
                'BillingCity' => $this->BillingCity,
                'BillingPostcode' => $this->BillingPostcode,
                'BillingCountry' => $this->BillingCountry,
                'BillingTitle' => $this->BillingTitle,
                'BillingFirstName' => $this->BillingFirstName,
                'BillingSurname' => $this->BillingSurname,
                'BillingEmail' => $this->BillingEmail,
                'BillingPhone' => $this->BillingPhone,
            ]
        ];

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

    /**
     * @return array
     */
    public function getStorableBackendIdentifiers()
    {
        return [
            Backend::IDENTIFIER
        ];
    }

    /**
     * @param array $data
     * @return void
     */
    public function setData($data)
    {
        if ($data) {
            $this->data = $data;
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
