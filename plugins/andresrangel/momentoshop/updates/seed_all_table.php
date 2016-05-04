<?php namespace AndresRangel\MomentoShop\Updates;

use AndresRangel\MomentoShop\Models\Currency;
use AndresRangel\MomentoShop\Models\GeoZone;
use AndresRangel\MomentoShop\Models\Shop;
use AndresRangel\MomentoShop\Models\TaxRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use October\Rain\Database\Updates\Seeder;
use RainLab\Location\Models\Country;
use RainLab\Location\Models\State;

class SeedAllTables extends Seeder
{
    public function run()
    {
        $spain = Country::where('code', 'es')->first();
        if(!$spain->is_enabled){
            $spain->is_enabled = true;
            $spain->save();
        }
        $seville = State::where('code', 'SV')->first();
        $uk = Country::where('code', 'GB')->first();

        $euro = new Currency();
        $euro->name = "Euro";
        $euro->code = 'EU';
        $euro->symbol_right = '€';
        $euro->decimal_place = '2';
        $euro->value = 0.9091;
        $euro->published_at = Carbon::now();
        $euro->save();
        $euro = Currency::where('code', 'EU')->first();

        $dolar = new Currency();
        $dolar->name = "US Dollar";
        $dolar->code = 'US Dollar';
        $dolar->symbol_left = '$';
        $dolar->decimal_place = '2';
        $dolar->value = 1;
        $dolar->published_at = Carbon::now();
        $dolar->save();

        $sp_iva = new GeoZone();
        $sp_iva->name = 'Spanish IVA';
        $sp_iva->description = '<p>This is the spanish IVA taxes rates</p>';
        $sp_iva->save();
        $sp_iva = GeoZone::find(1);
        $sp_iva->countries()->attach($spain->id);


        $uk_tax = new GeoZone();
        $uk_tax->name = 'UK VAT Zone';
        $uk_tax->description = '<p>This is the spanish IVA taxes rates</p>';
        $uk_tax->save();
        $uk_tax = GeoZone::find(2);
        $uk_tax->countries()->attach($uk->id);

        $tax_rate = new TaxRate();
        $tax_rate->geo_zone = $sp_iva;
        $tax_rate->name = "IVA General 21%";
        $tax_rate->rate = 21;
        $tax_rate->save();

        Shop::set(
            [
                'name'          => 'Momento Shop',
                'owner'         => 'Momento Eureka',
                'address'       => '<p>Urbanizaci\u00f3n El Jard\u00edn 1, casa 24 (41807) Espartinas - Sevilla\u200b<\/p>',
                'email'         => 'shop@momentoeureka.com',
                'phone'         => '600010203',
                'fax'           => '',
                'country'       => $spain->id,
                'state'         => $seville->id,
                'currency'      => $euro->id,
                'length'        => 'centimeter',
                'weight'        => 'kilogram',
                'tax'           => $tax_rate->id,
            ]
        );
    }
}

?>