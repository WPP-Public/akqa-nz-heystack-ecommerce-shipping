<?php

class CountryAdmin extends ModelAdmin
{

    public static $managed_models = array(
        'Country',
        'Zone',
        'ZonedCountry'
    );

    public static $url_segment = 'countries';
    public static $menu_title = 'Countries';

}
