<?php
/**
 * This file is part of the Ecommerce-Vouchers package
 *
 * @package Ecommerce-Shipping
 */

/**
 * Traits namespace
 */
namespace Heystack\Subsystem\Shipping\CountryBased\Traits;

trait CountryTrait
{
    use \Heystack\Subsystem\Core\State\Traits\DataObjectSerializableTrait;
    
    public function getIdentifier()
    {
        return $this->CountryCode;
    }
    
    public function getName()
    {
        return $this->record['Name'];
    }
    
    public function getCountryCode()
    {
        return $this->record['CountryCode'];
    }
    
    public function getShippingCost()
    {
        return $this->record['ShippingCost'];
    }
}