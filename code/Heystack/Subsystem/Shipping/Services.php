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
     * Holds the identfier of the shipping service
     * For use with the ServiceStore::getService($identifier) call
     */
    const SHIPPING_SERVICE = 'shipping_service';

    /**
     * Holds the identifier of the shipping input processor
     * For use with the ServiceStore::getService($identifier) call
     */
    const SHIPPING_INPUT_PROCESSOR = 'shipping_input_processor';

    /**
     * Holds the identifier of the shipping output processor
     * For use with the ServiceStore::getService($identifier) call
     */
    const SHIPPING_OUTPUT_PROCESSOR = 'shipping_output_processor';

    /**
     * Holds the identifier of the shipping service subscriber
     * For use with the ServiceStore::getService($identifier) call
     */
    const SHIPPING_SERVICE_SUBSCRIBER = 'shipping_service_subscriber';
}
