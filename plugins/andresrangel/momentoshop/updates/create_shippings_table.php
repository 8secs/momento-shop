<?php namespace AndresRangel\MomentoShop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateShippingsTable extends Migration
{

    public function up()
    {
        Schema::create('andresrangel_momentoshop_shippings', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('geo_zone_id')->unsigned();
            $table->string('name');
            $table->decimal('cost', 15, 2);
            $table->decimal('total', 15, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('andresrangel_momentoshop_shippings');
    }

}
