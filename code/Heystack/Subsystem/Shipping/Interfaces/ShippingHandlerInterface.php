<?php
/**
 * This file is part of the Ecommerce-Shipping package
 *
 * @package Ecommerce-Shipping
 */

/**
 * Interfaces namespace
 */
namespace Heystack\Subsystem\Shipping\Interfaces;

use Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionModifierInterface;

/**
 * Defines what methods & functions a ShippingHandler Class needs to implement
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Shipping
 */
interface ShippingHandlerInterface extends TransactionModifierInterface
{
    /**
     * Defines what methods the implementing class implements dynamically through __get and __set
     */
    public function getDynamicMethods();
    
    /**
     * Overrides the magic setter function for the Country field. Uses the cache for
     * retrieval and storage of the Country object
     * @param string $identifier
     */
    public function setCountry($identifier);

    /**
     * Uses the identifier to retrive the country object from the cache
     * @param  type                                                                  $identifier
     * @return \Heystack\Subsystem\Shipping\CountryBased\Interfaces\CountryInterface
     */
    public function getCountry($identifier);

    /**
     * Returns an array of all countries from the cache
     * @return array
     */
    public function getCountries();

}
