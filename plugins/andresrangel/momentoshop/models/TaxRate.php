<?php namespace AndresRangel\MomentoShop\Models;


/**
 * TaxRate Model
 */
class TaxRate extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'andresrangel_momentoshop_tax_rates';

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'geo_zone' => 'AndresRangel\MomentoShop\Models\GeoZone'
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];


}