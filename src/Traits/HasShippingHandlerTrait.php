<?php

namespace Heystack\Shipping\Traits;

use Heystack\Shipping\Interfaces\ShippingHandlerInterface;

/**
 * Class HasShippingHandlerTrait
 * @package Heystack\Shipping\Traits
 */
trait HasShippingHandlerTrait
{
    /**
     * @var \Heystack\Shipping\Interfaces\ShippingHandlerInterface
     */
    protected $shippingHandler;

    /**
     * @param \Heystack\Shipping\Interfaces\ShippingHandlerInterface $shippingHandler
     * @return void
     */
    public function setShippingHandler(ShippingHandlerInterface $shippingHandler)
    {
        $this->shippingHandler = $shippingHandler;
    }

    /**
     * @return \Heystack\Shipping\Interfaces\ShippingHandlerInterface
     */
    public function getShippingHandler()
    {
        return $this->shippingHandler;
    }
}