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
        
        $price = $this->baseCost();
        
        $this->extend('updateCost',$price);

        return $price;
    }

    protected function baseCost()
    {
        $price = $this->record['ShippingCost'];
        
        $this->extend('updateBaseCost',$price);

        return $price;
    }
}
