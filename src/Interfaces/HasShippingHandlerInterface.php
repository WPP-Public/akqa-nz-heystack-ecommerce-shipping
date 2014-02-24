<?php

namespace Heystack\Shipping\Interfaces;

/**
 * Interface HasShippingHandlerInterface
 * @package Heystack\Shipping\Interfaces
 */
interface HasShippingHandlerInterface
{
    /**
     * @param \Heystack\Shipping\Interfaces\ShippingHandlerInterface $shippingHandler
     * @return mixed
     */
    public function setShippingHandler(ShippingHandlerInterface $shippingHandler);

    /**
     * @return \Heystack\Shipping\Interfaces\ShippingHandlerInterface
     */
    public function getShippingHandler();
}