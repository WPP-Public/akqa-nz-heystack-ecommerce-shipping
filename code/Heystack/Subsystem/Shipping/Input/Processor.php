<?php
/**
 * This file is part of the Ecommerce-Shipping package
 *
 * @package Ecommerce-Shipping
 */

/**
 * Input namespace
 */
namespace Heystack\Subsystem\Shipping\Input;

use Heystack\Subsystem\Core\Input\ProcessorInterface;
use Heystack\Subsystem\Shipping\Interfaces\ShippingHandlerInterface;

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

    /**
     * Holds the shipping handler/service object
     * @var \Heystack\Subsystem\Shipping\Interfaces\ShippingHandlerInterface
     */
    protected $shippingService;

    /**
     * Creates the Shipping Input Processor
     * @param \Heystack\Subsystem\Shipping\Interfaces\ShippingHandlerInterface $shippingService
     */
    public function __construct(ShippingHandlerInterface $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    /**
     * Returns the Processor's identifier
     * @return \Heystack\Subsystem\Core\Identifier\Identifier
     */
    public function getIdentifier()
    {
        return $this->shippingService->getIdentifier();
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

        $shippingFields = $this->shippingService->getDynamicMethods();


        // populate defaults

        foreach ($data as $key => $value) {

            if (in_array($key, $shippingFields)) {

                $this->shippingService->$key = $value;

            }

        }

        // errors
        $errors = array();

        {

            if (!isset($data['BillingFirstName']) || !$data['BillingFirstName']) {

                $errors['BillingFirstName_Error'] = array('Error' => 'Please enter a first name.');

            }

            if (!isset($data['BillingSurname']) || !$data['BillingSurname']) {

                $errors['BillingSurname_Error'] = array('Error' => 'Please enter a surname.');

            }

            if (!isset($data['BillingEmail']) || !filter_var($data['BillingEmail'], FILTER_VALIDATE_EMAIL)) {

                $errors['BillingEmail_Error'] = array('Error' => 'Please enter an email address.');

            }

            if (!isset($data['BillingAddressLine1']) || !$data['BillingAddressLine1']) {

                $errors['BillingAddressLine1_Error'] = array('Error' => 'Please enter an address.');

            }

            if (!isset($data['BillingCity']) || !$data['BillingAddressLine1']) {

                $errors['BillingCity_Error'] = array('Error' => 'Please enter a city.');

            }

            if (!isset($data['BillingPostcode']) || !$data['BillingPostcode']) {

                $errors['BillingPostcode_Error'] = array('Error' => 'Please enter a postcode.');

            }

            if (!isset($data['BillingCountry']) || !$data['BillingCountry']) {

                $errors['BillingCountry_Error'] = array('Error' => 'Please select a country.');

            }

        }

        // if the delivery is billing, populate those fields that we can
        if ($data['delivery'] == 'billing') {

            $this->shippingService->BillingAsShipping = true;
            $this->shippingService->BillingFirstName = $data['BillingFirstName'];
            $this->shippingService->BillingSurname = $data['BillingSurname'];
            $this->shippingService->BillingEmail = $data['BillingEmail'];

            $this->shippingService->FirstName = $data['BillingFirstName'];
            $this->shippingService->Surname = $data['BillingSurname'];
            $this->shippingService->Email = $data['BillingEmail'];

            $this->shippingService->AddressLine1 = $data['BillingAddressLine1'];
            $this->shippingService->AddressLine2 = $data['BillingAddressLine2'];
            $this->shippingService->City = $data['BillingCity'];
            $this->shippingService->Postcode = $data['BillingPostcode'];
            $this->shippingService->Country = $data['BillingCountry'];


        } else {

            //errors
            {

                if (!isset($data['FirstName']) || !$data['FirstName']) {

                    $errors['FirstName_Error'] = array('Error' => 'Please enter a first name.');

                }

                if (!isset($data['Surname']) || !$data['Surname']) {

                    $errors['Surname_Error'] = array('Error' => 'Please enter a surname.');

                }

                if (!isset($data['Email']) || !filter_var($data['Email'], FILTER_VALIDATE_EMAIL)) {

                    $errors['Email_Error'] = array('Error' => 'Please enter an email address.');

                }

                if (!isset($data['AddressLine1']) || !$data['AddressLine1']) {

                    $errors['AddressLine1_Error'] = array('Error' => 'Please enter an address.');

                }

                if (!isset($data['City']) || !$data['City']) {

                    $errors['City_Error'] = array('Error' => 'Please enter a city.');

                }

                if (!isset($data['Postcode']) || !$data['Postcode']) {

                    $errors['Postcode_Error'] = array('Error' => 'Please enter a postcode.');

                }

                if (!isset($data['Country']) || !$data['Country']) {

                    $errors['Country_Error'] = array('Error' => 'Please select a country.');

                }

            }

            $this->shippingService->BillingAsShipping = false;
            $this->shippingService->FirstName = $data['BillingFirstName'];
            $this->shippingService->Surname = $data['BillingSurname'];
            $this->shippingService->Email = $data['BillingEmail'];

        }

        if (count($errors)) {

            return array_merge(
                array(
                    'success' => false,
                    'Errors'  => $errors
                ),
                $data
            );
        }

        $this->shippingService->saveState();

        return array(
            'success' => true
        );
    }

}
