<?php
/**
 * This file is part of the Ecommerce-Shipping package
 *
 * @package Ecommerce-Shipping
 */

/**
 * Input namespace
 */
namespace Heystack\Subsystem\Shipping\Input;

use Heystack\Subsystem\Core\Input\ProcessorInterface;

use Heystack\Subsystem\Shipping\Interfaces\ShippingHandlerInterface;

/**
 * Input processor for shipping
 *
 * Handles the form submission regarding shipping and controls the shipping
 * service in handling what to do with the data submitted
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Shipping
 */
class Processor implements ProcessorInterface
{

    /**
     * Holds the shipping handler/service object
     * @var \Heystack\Subsystem\Shipping\Interfaces\ShippingHandlerInterface
     */
    protected $shippingService;

    /**
     * Creates the Shipping Input Processor
     * @param \Heystack\Subsystem\Shipping\Interfaces\ShippingHandlerInterface $shippingService
     */
    public function __construct(ShippingHandlerInterface $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    /**
     * Returns the Processor's identifier
     * @return string
     */
    public function getIdentifier()
    {
        return strtolower($this->shippingService->getIdentifier());
    }

    /**
     * Method to determine what to do with the request and returns what will be processed
     * by the output processor
     * @param  \SS_HTTPRequest $request
     * @return array
     */
    public function process(\SS_HTTPRequest $request)
    {
        $data = $request->requestVars();

        $shippingFields = $this->shippingService->getDynamicMethods();

        foreach ($data as $key => $value) {

            if (in_array($key, $shippingFields)) {

                $this->shippingService->$key = $value;

            }

        }

        $this->shippingService->saveState();

        return array('success' => 'true');
    }

}
