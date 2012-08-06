<?php

use Heystack\Subsystem\Shipping\Types\CountryBased\Interfaces\CountryInterface;
use Heystack\Subsystem\Shipping\Traits\CountryTrait;

class ZonedCountry extends DataObject implements CountryInterface
{
    use CountryTrait;

    public static $db = array(
        'Name' => 'Varchar(255)',
        'CountryCode' => 'Varchar(255)'
    );

    public static $has_one = array(
        'Zone' => 'Zone'
    );

    public static $summary_fields = array(
        'Name',
        'CountryCode'
    );

    public function getShippingCost()
    {
        if ($this->ZoneID) {

            return $this->Zone()->cost();

        }

        return 0;
    }
}
