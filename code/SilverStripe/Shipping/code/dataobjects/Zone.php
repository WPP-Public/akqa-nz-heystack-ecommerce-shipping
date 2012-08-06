<?php

class Zone extends DataObject
{
    public static $db = array(
        'Name' => 'Varchar(255)',
        'ShippingCost' => 'Decimal(10,2)',
        'FreeAfter' => 'Decimal(10,2)'
    );

    public static $has_many = array(
        'Countries' => 'ZonedCountry'
    );

    public function cost()
    {
        if ($this->FreeAfter > 0) {
            $productHolder = Heystack\Subsystem\Core\ServiceStore::getService(Heystack\Subsystem\Products\Services::PRODUCTHOLDER);

            if ($productHolder->getTotal() >= $this->FreeAfter) {
                return 0;
            }
        }

        return $this->baseCost();
    }

    protected function baseCost()
    {
        $price = $this->record['ShippingCost'];

        $currencyService = Heystack\Subsystem\Core\ServiceStore::getService(Heystack\Subsystem\Ecommerce\Services::CURRENCY_SERVICE);

        $activeCurrencyCode = $currencyService->getActiveCurrency()->getIdentifier();

        switch ($activeCurrencyCode) {
            case 'NZD':
                $price *= 1;
                break;
            case 'USD':
                $price *= 2;
                break;
            default:
                $price *= 3;
                break;
        }

        return $price;
    }
}
