<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Shipping
 */

/**
 * Output namespace
 */
namespace Heystack\Subsystem\Shipping\Output;

use Heystack\Subsystem\Core\Identifier\Identifier;
use Heystack\Subsystem\Core\Output\ProcessorInterface;

/**
 * Output Processor for Shipping
 *
 * Handles all output related to Shipping
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Shipping
 *
 */
class Processor implements ProcessorInterface
{
    /**
     * Returns the identifier for this object
     * @return \Heystack\Subsystem\Core\Identifier\Identifier
     */
    public function getIdentifier()
    {
        return new Identifier('shipping');
    }

    /**
     * Method used to determine how to handle the output based on the InputProcessor's result
     * @param  \Controller      $controller
     * @param  type             $result
     * @return \SS_HTTPResponse
     */
    public function process(\Controller $controller, $result = null)
    {
        if ($controller->getRequest()->isAjax()) {

            $response = $controller->getResponse();
            $response->setStatusCode(200);
            $response->addHeader('Content-Type', 'application/json');

            $response->setBody(json_encode($result));

            return $response;
        } else {
            $controller->redirectBack();
        }

        return null;
    }

}
