<?php
/**
 * This file is part of the Ecommerce-Shipping package
 *
 * @package Ecommerce-Shipping
 */

/**
 * Traits namespace
 */
namespace Heystack\Subsystem\Shipping\Traits;

/**
 * Provides the magic setter/getter functions for a ShippingHandler class.
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Shipping
 */
trait ShippingHandlerTrait
{
    /**
     * Returns an array of field names that need to managed by the shipping subsystem.
     * @return array
     */
    abstract public function getDynamicMethods();

    /**
     * Magic setter function that uses the data array to store a property's data.
     *
     * @param  string     $name
     * @param  type       $value
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        if (in_array($name, $this->getDynamicMethods()) || $name == 'data') {

            $setterMethod = 'set' . $name;

            if (method_exists($this, $setterMethod)) {

                $this->$setterMethod($value);

            } else {

                $this->data[$name] = $value;

            }

        } else {

            if (property_exists($this, 'monologService') && isset($this->monologService)) {
                $this->monologService->err($name . ' is not a valid Shipping property');
            }

            throw new \Exception($name . ' is not a valid Shipping property');

        }
    }

    /**
     * Magic getter function that returns a property from the data array
     * @param  string     $name
     * @return type
     * @throws \Exception
     */
    public function __get($name)
    {

        if (in_array($name, $this->getDynamicMethods()) || $name == 'data') {

            $getterMethod = 'get' . $name;

            if (method_exists($this, $getterMethod)) {

                return $this->$getterMethod();

            } else {

                return isset($this->data[$name]) ? $this->data[$name] : null;

            }

        } else {

            if (property_exists($this, 'monologService') && isset($this->monologService)) {
                $this->monologService->err($name . ' is not a valid Shipping property');
            }

            throw new \Exception($name . ' is not a valid Shipping property');

        }
    }
}
