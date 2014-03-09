<?php
/**
 * This file is part of the Ecommerce-Shipping package
 *
 * @package Ecommerce-Shipping
 */

/**
 * Interfaces namespace
 */
namespace Heystack\Shipping\Interfaces;

use Heystack\Core\Identifier\IdentifierInterface;
use Heystack\Core\State\StateableInterface;
use Heystack\Core\Storage\Interfaces\ParentReferenceInterface;
use Heystack\Core\Storage\StorableInterface;
use Heystack\Ecommerce\Transaction\Interfaces\TransactionModifierInterface;

/**
 * Defines what methods & functions a ShippingHandler Class needs to implement
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Shipping
 */
interface ShippingHandlerInterface
    extends
        TransactionModifierInterface,
        ParentReferenceInterface,
        StorableInterface,
        StateableInterface
{
    /**
     * Defines what methods the implementing class implements dynamically through __get and __set
     */
    public function getDynamicMethods();

    /**
     * Overrides the magic setter function for the Country field. Uses the LocaleService for
     * retrieval and storage of the Country object
     * @param \Heystack\Core\Identifier\IdentifierInterface $identifier
     */
    public function setCountry(IdentifierInterface $identifier);

    /**
     * Overrides the magic getter function for the Country field. Uses the Locale Service for
     * retrieval and storage of the Country object
     */
    public function getCountry();
}
