<?php namespace AndresRangel\MomentoShop\Models;


/**
 * Shipping Model
 */
class Shipping extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'andresrangel_momentoshop_shippings';

    public $belongsTo = [
        'geo_zone' => 'AndresRangel\MomentoShop\Models\GeoZone'
    ];

    public $hasManyThrough = [
        'countries' => [
            'RainLab\Location\Models\Country',
            'through' => 'AndresRangel\MomentoShop\Models\GeoZone'
        ],
        'states' => [
            'RainLab\Location\Models\State',
            'through' => 'AndresRangel\MomentoShop\Models\GeoZone'
        ],
    ];

}