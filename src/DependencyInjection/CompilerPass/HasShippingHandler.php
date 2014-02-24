<?php

namespace Heystack\Shipping\DependencyInjection\CompilerPass;

use Heystack\Core\DependencyInjection\CompilerPass\HasService;
use Heystack\Shipping\Services;

/**
 * Class HasShippingHandler
 * @package Heystack\Shipping\DependencyInjection\CompilerPass
 */
class HasShippingHandler extends HasService
{
    /**
     * @return string
     */
    protected function getServiceName()
    {
        return Services::SHIPPING_SERVICE;
    }

    /**
     * @return string
     */
    protected function getServiceSetterName()
    {
        return 'setShippingHandler';
    }
}