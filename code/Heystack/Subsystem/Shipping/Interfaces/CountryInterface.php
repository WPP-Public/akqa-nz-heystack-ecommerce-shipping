<?php
/**
 * This file is part of the Ecommerce-Vouchers package
 *
 * @package Ecommerce-Shipping
 */

/**
 * Interfaces namespace
 */
namespace Heystack\Subsystem\Shipping\Interfaces;

/**
 * Defines what methods/functions a Country class needs to implement
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Shipping
 */
interface CountryInterface extends \Serializable
{
    /**
     * Returns a unique identifier
     */
    public function getIdentifier();

    /**
     * Returns the name of the country object
     */
    public function getName();

    /**
     * Returns the country code of the country object
     */
    public function getCountryCode();

}
