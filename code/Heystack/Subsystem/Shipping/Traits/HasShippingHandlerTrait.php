<?php

namespace Heystack\Subsystem\Shipping\Traits;

use Heystack\Subsystem\Shipping\Interfaces\ShippingHandlerInterface;

/**
 * Class HasShippingHandlerTrait
 * @package Heystack\Subsystem\Shipping\Traits
 */
trait HasShippingHandlerTrait
{
    /**
     * @var \Heystack\Subsystem\Shipping\Interfaces\ShippingHandlerInterface
     */
    protected $shippingHandler;

    /**
     * @param mixed $shippingHandler
     */
    public function setShippingHandler(ShippingHandlerInterface $shippingHandler)
    {
        $this->shippingHandler = $shippingHandler;
    }

    /**
     * @return \Heystack\Subsystem\Shipping\Interfaces\ShippingHandlerInterface
     */
    public function getShippingHandler()
    {
        return $this->shippingHandler;
    }
}