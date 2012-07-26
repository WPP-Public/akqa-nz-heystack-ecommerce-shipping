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
 * Holds constants corresponding to the services defined in the services.yml file
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Shipping
 */
final class Services
{    
    /**
     * Holds the string representation of the shipping service
     */
    const SHIPPING_SERVICE = 'shipping_service';
    
    /**
     * Holds the string representation of the shipping input processor
     */
    const SHIPPING_INPUT_PROCESSOR = 'shipping_input_processor';
    
    /**
     * Holds the string representation of the shipping output processor
     */
    const SHIPPING_OUTPUT_PROCESSOR = 'shipping_output_processor';
    
    /**
     * Holds the string representation of the shipping service subscriber
     */
    const SHIPPING_SERVICE_SUBSCRIBER = 'shipping_service_subscriber';
}