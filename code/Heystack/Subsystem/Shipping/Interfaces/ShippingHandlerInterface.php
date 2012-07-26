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
     * Returns an array of field names that need to managed by the shipping subsystem.
     * @return array
     */    
    public function getShippingFields();
    
    /**
     * Returns an associative array of the shipping fields and the data that is set up for them
     * @return array
     */    
    public function getShippingFieldsData();
    
    /**
     * Overrides the magic setter function for the Country field. Uses the cache for 
     * retrieval and storage of the Country object
     * @param string $identifier
     */
    public function setCountry($identifier);
    
    /**
     * Uses the identifier to retrive the country object from the cache
     * @param type $identifier
     * @return \Heystack\Subsystem\Shipping\CountryBased\Interfaces\CountryInterface
     */    
    public function getCountry($identifier);
    
    /**
     * Returns an array of all countries from the cache
     * @return array
     */    
    public function getCountries();
    
}