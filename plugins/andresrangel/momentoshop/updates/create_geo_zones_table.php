<?php namespace AndresRangel\MomentoShop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateGeoZonesTable extends Migration
{

    public function up()
    {
        Schema::create('andresrangel_momentoshop_geo_zones', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('andresrangel_momentoshop_geo_zones_countries', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('geo_zone_id')->unsigned()->nullable()->index();
            $table->integer('country_id')->unsigned()->nullable()->index();
        });

        Schema::create('andresrangel_momentoshop_geo_zones_states', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('geo_zone_id')->unsigned()->nullable()->index();
            $table->integer('state_id')->unsigned()->nullable()->index();

        });
    }

    public function down()
    {
        Schema::dropIfExists('andresrangel_momentoshop_geo_zones');
    }

}
