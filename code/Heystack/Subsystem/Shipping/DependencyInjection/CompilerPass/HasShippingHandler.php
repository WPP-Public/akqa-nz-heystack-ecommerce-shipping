<?php

namespace Heystack\Subsystem\Shipping\DependencyInjection\CompilerPass;

use Heystack\Subsystem\Core\DependencyInjection\CompilerPass\HasService;
use Heystack\Subsystem\Shipping\Services;

/**
 * Class HasShippingHandler
 * @package Heystack\Subsystem\Shipping\DependencyInjection\CompilerPass
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