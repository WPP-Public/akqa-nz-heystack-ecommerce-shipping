<?php


namespace Heystack\Shipping\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class ContainerConfig
 * @author Cam Spiers <cameron@heyday.co.nz>
 * @package Heystack\Shipping\Config
 */
class ContainerConfig implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('shipping');

        $rootNode
            ->children()
                ->scalarNode('yml_shipping_handler')->end()
            ->end();

        return $treeBuilder;
    }
}