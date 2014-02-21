<?php

use Camspiers\DependencyInjection\SharedContainerFactory;
use Heystack\Subsystem\Shipping\DependencyInjection\ContainerExtension;
use Heystack\Subsystem\Shipping\DependencyInjection\CompilerPass\HasShippingHandler;

SharedContainerFactory::addExtension(new ContainerExtension());
SharedContainerFactory::addCompilerPass(new HasShippingHandler());