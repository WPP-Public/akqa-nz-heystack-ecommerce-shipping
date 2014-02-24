<?php

use Camspiers\DependencyInjection\SharedContainerFactory;
use Heystack\Shipping\DependencyInjection\ContainerExtension;
use Heystack\Shipping\DependencyInjection\CompilerPass\HasShippingHandler;

SharedContainerFactory::addExtension(new ContainerExtension());
SharedContainerFactory::addCompilerPass(new HasShippingHandler());