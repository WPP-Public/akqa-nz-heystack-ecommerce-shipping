<?php
/**
 * This file is part of the Ecommerce-Shipping package
 *
 * @package Ecommerce-Shipping
 */

/**
 * Interfaces namespace
 */
namespace Heystack\Shipping\Interfaces;

use Heystack\Ecommerce\Transaction\Interfaces\TransactionModifierInterface;

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
     * Overrides the magic setter function for the Country field. Uses the LocaleService for
     * retrieval and storage of the Country object
     * @param string $identifier
     */
    public function setCountry($identifier);

    /**
     * Overrides the magic getter function for the Country field. Uses the Locale Service for
     * retrieval and storage of the Country object
     */
    public function getCountry();
}
