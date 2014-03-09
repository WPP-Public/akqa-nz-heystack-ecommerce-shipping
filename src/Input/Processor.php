<?php
/**
 * This file is part of the Ecommerce-Shipping package
 *
 * @package Ecommerce-Shipping
 */

/**
 * Input namespace
 */
namespace Heystack\Shipping\Input;

use Heystack\Core\Identifier\Identifier;
use Heystack\Core\Input\ProcessorInterface;
use Heystack\Shipping\Interfaces\ShippingHandlerInterface;
use Heystack\Shipping\Traits\HasShippingHandlerTrait;

/**
 * Input processor for shipping
 *
 * Handles the form submission regarding shipping and controls the shipping
 * service in handling what to do with the data submitted
 *
 * @copyright  Heyday
 * @author     Glenn Bautista <glenn@heyday.co.nz>
 * @package    Ecommerce-Shipping
 */
class Processor implements ProcessorInterface
{
    use HasShippingHandlerTrait;

    /**
     * Creates the Shipping Input Processor
     * @param \Heystack\Shipping\Interfaces\ShippingHandlerInterface $shippingHandler
     */
    public function __construct(ShippingHandlerInterface $shippingHandler)
    {
        $this->shippingHandler = $shippingHandler;
    }

    /**
     * Returns the Processor's identifier
     * @return \Heystack\Core\Identifier\Identifier
     */
    public function getIdentifier()
    {
        return $this->shippingHandler->getIdentifier();
    }

    /**
     * Method to determine what to do with the request and returns what will be processed
     * by the output processor
     * @param  \SS_HTTPRequest $request
     * @return array
     */
    public function process(\SS_HTTPRequest $request)
    {
        $data = $request->requestVars();

        $shippingFields = $this->shippingHandler->getDynamicMethods();

        // populate defaults
        foreach ($data as $key => $value) {
            if (in_array($key, $shippingFields)) {
                if ($key === 'Country') {
                    $this->shippingHandler->setCountry(new Identifier($value));
                } else {
                    $this->shippingHandler->$key = $value;
                }
            }
        }

        // errors
        $errors = [];

        if (!isset($data['BillingFirstName']) || !$data['BillingFirstName']) {

            $errors['BillingFirstName_Error'] = ['Error' => 'Please enter a first name.'];

        }

        if (!isset($data['BillingSurname']) || !$data['BillingSurname']) {

            $errors['BillingSurname_Error'] = ['Error' => 'Please enter a surname.'];

        }

        if (!isset($data['BillingEmail']) || !filter_var($data['BillingEmail'], FILTER_VALIDATE_EMAIL)) {

            $errors['BillingEmail_Error'] = ['Error' => 'Please enter an email address.'];

        }

        if (!isset($data['BillingAddressLine1']) || !$data['BillingAddressLine1']) {

            $errors['BillingAddressLine1_Error'] = ['Error' => 'Please enter an address.'];

        }

        if (!isset($data['BillingCity']) || !$data['BillingAddressLine1']) {

            $errors['BillingCity_Error'] = ['Error' => 'Please enter a city.'];

        }

        if (!isset($data['BillingPostcode']) || !$data['BillingPostcode']) {

            $errors['BillingPostcode_Error'] = ['Error' => 'Please enter a postcode.'];

        }

        if (!isset($data['BillingCountry']) || !$data['BillingCountry']) {

            $errors['BillingCountry_Error'] = ['Error' => 'Please select a country.'];

        }

        // if the delivery is billing, populate those fields that we can
        if ($data['delivery'] == 'billing') {

            $this->shippingHandler->BillingAsShipping = true;

            $this->shippingHandler->FirstName = $data['BillingFirstName'];
            $this->shippingHandler->Surname = $data['BillingSurname'];
            $this->shippingHandler->Email = $data['BillingEmail'];

            $this->shippingHandler->AddressLine1 = $data['BillingAddressLine1'];
            $this->shippingHandler->AddressLine2 = $data['BillingAddressLine2'];
            $this->shippingHandler->City = $data['BillingCity'];
            $this->shippingHandler->Postcode = $data['BillingPostcode'];
            $this->shippingHandler->Country = $data['BillingCountry'];


        } else {

            //errors
            {

                if (!isset($data['FirstName']) || !$data['FirstName']) {

                    $errors['FirstName_Error'] = ['Error' => 'Please enter a first name.'];

                }

                if (!isset($data['Surname']) || !$data['Surname']) {

                    $errors['Surname_Error'] = ['Error' => 'Please enter a surname.'];

                }

                if (!isset($data['Email']) || !filter_var($data['Email'], FILTER_VALIDATE_EMAIL)) {

                    $errors['Email_Error'] = ['Error' => 'Please enter an email address.'];

                }

                if (!isset($data['AddressLine1']) || !$data['AddressLine1']) {

                    $errors['AddressLine1_Error'] = ['Error' => 'Please enter an address.'];

                }

                if (!isset($data['City']) || !$data['City']) {

                    $errors['City_Error'] = ['Error' => 'Please enter a city.'];

                }

                if (!isset($data['Postcode']) || !$data['Postcode']) {

                    $errors['Postcode_Error'] = ['Error' => 'Please enter a postcode.'];

                }

                if (!isset($data['Country']) || !$data['Country']) {

                    $errors['Country_Error'] = ['Error' => 'Please select a country.'];

                }

            }

            $this->shippingHandler->BillingAsShipping = false;
            $this->shippingHandler->FirstName = $data['BillingFirstName'];
            $this->shippingHandler->Surname = $data['BillingSurname'];
            $this->shippingHandler->Email = $data['BillingEmail'];

        }

        if (count($errors)) {

            return array_merge(
                [
                    'success' => false,
                    'Errors'  => $errors
                ],
                $data
            );
        }

        $this->shippingHandler->saveState();

        return [
            'success' => true
        ];
    }

}
