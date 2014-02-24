<?php

namespace Heystack\Subsystem\Shipping\Interfaces;

/**
 * Interface HasShippingHandlerInterface
 * @package Heystack\Subsystem\Shipping\Interfaces
 */
interface HasShippingHandlerInterface
{
    /**
     * @param \Heystack\Subsystem\Shipping\Interfaces\ShippingHandlerInterface $shippingHandler
     * @return mixed
     */
    public function setShippingHandler(ShippingHandlerInterface $shippingHandler);

    /**
     * @return \Heystack\Subsystem\Shipping\Interfaces\ShippingHandlerInterface
     */
    public function getShippingHandler();
}