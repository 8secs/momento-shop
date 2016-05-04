<?php namespace AndresRangel\MomentoShop\Models;

use October\Rain\Database\Model as BaseModel;
use RainLab\Location\Models\Country;
use RainLab\Location\Models\State;
use RainLab\Translate\Models\Locale;

/**
 * Shop Model
 * Name
 * Owner
 * Address
 * Email
 * Phone
 * Fax
 * Logo
 * Opening Times
 * Comments
 *
 * Locations data
 * Country
 * State
 * Locale
 * Currency
 * Length
 * Weight
 */
class Shop extends BaseModel
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'andresrangel_momentoshop_shops';

    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'andresrangel_momentoshop_shop_settings';

    public $settingsFields = 'fields.yaml';

    public $attachOne = [
        'logo' => ['System\Models\File'],
    ];

    public function getCountryOptions(){
        return Country::where('is_enabled', 1)->lists('name', 'id');
    }

    public function getStateOptions(){

        return State::where('country_id', $this->country)->lists('name', 'id');
    }

    public function getLocaleOptions() {
        return Locale::all()->lists('name', 'id');
    }

    public function getCurrencyOptions(){
        return Currency::all()->lists('name', 'id');
    }

    public function getTaxOptions(){
        return TaxRate::all()->lists('name', 'id');
    }

    public function getLengthOptions() {
        return [
            'centimeter'        => 'Centimeter',
            'millimeter'        => 'Millimeter',
            'inch'              => 'Inch',
        ];
    }

    public function getWeightOptions(){
        return [
            'kilogram'          => 'Kilogram',
            'gram'              => 'Gram',
            'pound'             => 'Pound',
            'ounce'             => 'Ounce',
        ];
    }

}