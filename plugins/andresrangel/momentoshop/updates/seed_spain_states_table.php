<?php namespace AndresRangel\MomentoShop\Updates;

use October\Rain\Database\Updates\Seeder;
use RainLab\Location\Models\Country;
use RainLab\Location\Models\State;

class SeedSpainStatesTable extends Seeder
{
    public function run()
    {
        $sp = Country::whereCode('ES')->first();
        
        $sp->states()->createMany([
            ['name' => 'La Coru&ntilde;a', 'code' => 'CA'],
            ['name' => '&Aacute;lava', 'code' => 'AL'],
            ['name' => 'Albacete', 'code' => 'AB'],
            ['name' => 'Alicante', 'code' => 'AC'],
            ['name' => 'Almeria', 'code' => 'AM'],
            ['name' => 'Asturias', 'code' => 'AS'],
            ['name' => '&Aacute;vila', 'code' => 'AV'],
            ['name' => 'Badajoz', 'code' => 'BJ'],
            ['name' => 'Baleares', 'code' => 'IB'],
            ['name' => 'Barcelona', 'code' => 'BA'],
            ['name' => 'Burgos', 'code' => 'BU'],
            ['name' => 'C&aacute;ceres', 'code' => 'CC'],
            ['name' => 'C&aacute;diz', 'code' => 'CZ'],
            ['name' => 'Cantabria', 'code' => 'CT'],
            ['name' => 'Castell&oacute;n', 'code' => 'CL'],
            ['name' => 'Ceuta', 'code' => 'CE'],
            ['name' => 'Ciudad Real', 'code' => 'CR'],
            ['name' => 'C&oacute;rdoba', 'code' => 'CD'],
            ['name' => 'Cuenca', 'code' => 'CU'],
            ['name' => 'Girona', 'code' => 'GI'],
            ['name' => 'Granada', 'code' => 'GD'],
            ['name' => 'Guadalajara', 'code' => 'GJ'],
            ['name' => 'Guip&uacute;zcoa', 'code' => 'GP'],
            ['name' => 'Huelva', 'code' => 'HL'],
            ['name' => 'Huesca', 'code' => 'HS'],
            ['name' => 'Ja&eacute;n', 'code' => 'JN'],
            ['name' => 'La Rioja', 'code' => 'RJ'],
            ['name' => 'Las Palmas', 'code' => 'PM'],
            ['name' => 'Leon', 'code' => 'LE'],
            ['name' => 'Lleida', 'code' => 'LL'],
            ['name' => 'Lugo', 'code' => 'LG'],
            ['name' => 'Madrid', 'code' => 'MD'],
            ['name' => 'Malaga', 'code' => 'MA'],
            ['name' => 'Melilla', 'code' => 'ML'],
            ['name' => 'Murcia', 'code' => 'MU'],
            ['name' => 'Navarra', 'code' => 'NV'],
            ['name' => 'Ourense', 'code' => 'OU'],
            ['name' => 'Palencia', 'code' => 'PL'],
            ['name' => 'Pontevedra', 'code' => 'PO'],
            ['name' => 'Salamanca', 'code' => 'SL'],
            ['name' => 'Santa Cruz de Tenerife', 'code' => 'SC'],
            ['name' => 'Segovia', 'code' => 'SG'],
            ['name' => 'Sevilla', 'code' => 'SV'],
            ['name' => 'Soria', 'code' => 'SO'],
            ['name' => 'Tarragona', 'code' => 'TA'],
            ['name' => 'Teruel', 'code' => 'TE'],
            ['name' => 'Toledo', 'code' => 'TO'],
            ['name' => 'Valencia', 'code' => 'VC'],
            ['name' => 'Valladolid', 'code' => 'VD'],
            ['name' => 'Vizcaya', 'code' => 'VZ'],
            ['name' => 'Zamora', 'code' => 'ZM'],
            ['name' => 'Zaragoza', 'code' => 'ZR']
        ]);
    }
}

?>

