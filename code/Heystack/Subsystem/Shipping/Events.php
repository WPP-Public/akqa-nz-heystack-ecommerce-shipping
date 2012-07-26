<?php
/**
 * This file is part of the Ecommerce-Shipping package
 *
 * @package Ecommerce-Shipping
 */

/**
 * Shipping namespace
 */
namespace Heystack\Subsystem\Shipping;

/**
 * Events holds constant references to triggerable dispatch events.
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Shipping
 * @see Symfony\Component\EventDispatcher
 *
 */
final class Events
{    
    /**
     * Indicates that the ShippingHandler's total has been updated
     */
    const TOTAL_UPDATED       = 'shipping.totalupdated';
    
    /**
     * Indicates that the ShippingHandler's information has been stored
     */
    const STORED              = 'shipping.stored';
}
