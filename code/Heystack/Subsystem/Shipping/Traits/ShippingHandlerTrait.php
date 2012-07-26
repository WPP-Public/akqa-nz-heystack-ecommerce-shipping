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
    abstract public function getShippingFields();
    
    
    /**
     * Magic setter function that uses the data array to store a property's data.
     *
     * @param string $name
     * @param type $value
     * @throws \Exception
     */
    public function __set(string $name, $value)
    {
        if(in_array($name, $this->getShippingFields())){
            
            $setterMethod = 'set' . $name;
            
            if(method_exists($this, $setterMethod)){
                $this->$setterMethod($value);
            }else{
            
                $this->data[$name] = $value;
                
            }
            
        }else{
            
            if(property_exists($this, 'monologService') && isset($this->monologService)){
                $this->monologService->err($name . ' is not a valid Shipping property');
            }
            
            throw \Exception($name . ' is not a valid Shipping property');
            
        }
    }
    
    /**
     * Magic getter function that returns a property from the data array
     * @param string $name
     * @return type
     * @throws \Exception
     */
    public function __get(string $name)
    {
        if(in_array($name, $this->getShippingFields())){
            
            return isset($this->data[$name]) ? $this->data[$name] : null;
            
        }else{
            
            if(property_exists($this, 'monologService') && isset($this->monologService)){
                $this->monologService->err($name . ' is not a valid Shipping property');
            }
                
            throw \Exception($name . ' is not a valid Shipping property');
            
        }
    }
}

