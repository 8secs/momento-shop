<?php namespace AndresRangel\MomentoShop\Models;

use RainLab\Location\Models\State;
use RainLab\Location\Models\Country;

/**
 * GeoZone Model
 */
class GeoZone extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'andresrangel_momentoshop_geo_zones';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'taxRates' => 'AndresRangel\MomentoShop\Models\TaxRate',
        'shippings' => 'AndresRangel\MomentoShop\Models\Shipping',
    ];
    public $belongsTo = [];
    public $belongsToMany = [
        'countries' => [
            'RainLab\Location\Models\Country',
            'table' => 'andresrangel_momentoshop_geo_zones_countries',
        ],
        'states' => [
            'RainLab\Location\Models\State',
            'table' => 'andresrangel_momentoshop_geo_zones_states',
        ],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function afterDelete()
    {
        if($this->countries()) $this->countries()->detach();
        if($this->states()) $this->states()->detach();
    }

    public function getCountriesOptions(){
        return Country::where('is_enabled', 1)->get();
    }

    public function getStatesOptions()
    {
        return State::where($this->countries())->lists('name', 'id', 'code');

    }

}