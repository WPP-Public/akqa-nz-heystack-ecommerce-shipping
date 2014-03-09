<?php
/**
 * This file is part of the Ecommerce-Shipping package
 *
 * @package Ecommerce-Shipping
 */

/**
 * Free namespace
 */
namespace Heystack\Shipping\Types\Free;

use Heystack\Core\Identifier\Identifier;
use Heystack\Core\Identifier\IdentifierInterface;
use Heystack\Core\Interfaces\HasDataInterface;
use Heystack\Core\Interfaces\HasStateServiceInterface;
use Heystack\Core\State\State;
use Heystack\Core\State\StateableInterface;
use Heystack\Core\Storage\Backends\SilverStripeOrm\Backend;
use Heystack\Core\Storage\StorableInterface;
use Heystack\Core\Storage\Traits\ParentReferenceTrait;
use Heystack\Core\Traits\HasEventServiceTrait;
use Heystack\Core\Traits\HasLoggerServiceTrait;
use Heystack\Core\Traits\HasStateServiceTrait;
use Heystack\Core\ViewableData\ViewableDataInterface;
use Heystack\Ecommerce\Currency\Interfaces\CurrencyServiceInterface;
use Heystack\Ecommerce\Currency\Traits\HasCurrencyServiceTrait;
use Heystack\Ecommerce\Locale\LocaleService;
use Heystack\Ecommerce\Locale\Traits\HasLocaleServiceTrait;
use Heystack\Ecommerce\Transaction\Traits\TransactionModifierSerializeTrait;
use Heystack\Ecommerce\Transaction\Traits\TransactionModifierStateTrait;
use Heystack\Ecommerce\Transaction\TransactionModifierTypes;
use Heystack\Shipping\Interfaces\ShippingHandlerInterface;
use Heystack\Shipping\Traits\ShippingHandlerTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * An implementation of the ShippingHandlerInterface specific to 'free' shipping cost calculation
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Shipping
 */
class ShippingHandler
    implements
        ShippingHandlerInterface,
        StateableInterface,
        \Serializable,
        ViewableDataInterface,
        StorableInterface,
        HasDataInterface,
        HasStateServiceInterface
{
    use ShippingHandlerTrait;
    use TransactionModifierStateTrait;
    use TransactionModifierSerializeTrait;
    use ParentReferenceTrait;
    use HasLocaleServiceTrait;
    use HasCurrencyServiceTrait;
    use HasEventServiceTrait;
    use HasStateServiceTrait;
    use HasLoggerServiceTrait;

    /**
     * Holds the key used for storing state
     */
    const IDENTIFIER = 'shipping';

    /**
     * Holds the name of the country class to be used
     * @var string
     */
    protected $countryClass;

    /**
     * @var \SebastianBergmann\Money\Money
     */
    protected $total;

    /**
     * @param LocaleService $localeService
     * @param EventDispatcherInterface $eventService
     * @param State $stateService
     * @param CurrencyServiceInterface $currencyService
     * @param LoggerInterface $loggerService
     * Creates the ShippingHandler object
     */
    public function __construct(
        LocaleService $localeService,
        EventDispatcherInterface $eventService,
        State $stateService,
        CurrencyServiceInterface $currencyService,
        LoggerInterface $loggerService = null
    )
    {
        $this->localeService = $localeService;
        $this->eventService = $eventService;
        $this->stateService = $stateService;
        $this->loggerService = $loggerService;
        $this->currencyService = $currencyService;
        $this->total = $this->currencyService->getZeroMoney();
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
     * @param IdentifierInterface $identifier
     */
    public function setCountry(IdentifierInterface $identifier)
    {
        $this->localeService->setActiveCountry($identifier);
    }

    /**
     * Retrive the country object from the cache
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
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Indicates the type of amount the modifier will return
     * Must return a constant from TransactionModifierTypes
     */
    public function getType()
    {
        return TransactionModifierTypes::NEUTRAL;
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
                'BillingCountry' => $this->BillingCountry,
                'BillingTitle' => $this->BillingTitle,
                'BillingFirstName' => $this->BillingFirstName,
                'BillingSurname' => $this->BillingSurname,
                'BillingEmail' => $this->BillingEmail,
                'BillingPhone' => $this->BillingPhone,
           ]
       ];
    }

    /**
     * @return string
     */
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
     * @param $data
     * @return mixed|void
     */
    public function setData($data)
    {
        if (is_array($data)) {
            list($this->data, $this->total) = $data;
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [$this->data, $this->total];
    }
}
