<?php
/**
 * This file is part of the Ecommerce-Vouchers package
 *
 * @package Ecommerce-Shipping
 */

/**
 * Interfaces namespace
 */
namespace Heystack\Subsystem\Shipping\CountryBased\Interfaces;

interface CountryInterface extends \Serializable
{
    public function getIdentifier();
    public function getName();
    public function getCountryCode();
    public function getShippingCost();
}

