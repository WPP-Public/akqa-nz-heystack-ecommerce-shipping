<?php

use Heystack\Subsystem\Shipping\CountryBased\Interfaces\CountryInterface;
use Heystack\Subsystem\Shipping\CountryBased\Traits\CountryTrait;

class Country extends DataObject implements CountryInterface
{
    use CountryTrait;
    
    static $db = array(
        'Name' => 'Varchar(255)',
        'CountryCode' => 'Varchar(255)',
        'ShippingCost' => 'Decimal(10,2)'
    );
    
    static $summary_fields = array(
        'Name',
        'CountryCode',
        'ShippingCost'
    );
}