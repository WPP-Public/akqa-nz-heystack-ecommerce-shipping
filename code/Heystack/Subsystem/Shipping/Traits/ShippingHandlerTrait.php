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

trait ShippingHandlerTrait
{   
    abstract public function getShippingFields();
    
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

