<?php namespace AndresRangel\MomentoShop\Updates;

use Carbon\Carbon;
use Schema;
use October\Rain\Database\Updates\Migration;

class CreateFiltersTable extends Migration
{

    public function up()
    {
        Schema::create('andresrangel_momentoshop_filters', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('filter_type_id')->unsigned();
            $table->string('name');
            $table->text('description');
            $table->date('published_at')->default(Carbon::now());
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('andresrangel_momentoshop_filters');
    }

}
