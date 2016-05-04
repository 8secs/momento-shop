<?php namespace AndresRangel\MomentoShop\Updates;

use Carbon\Carbon;
use Schema;
use October\Rain\Database\Updates\Migration;

class CreateCurrenciesTable extends Migration
{

    public function up()
    {
        Schema::create('andresrangel_momentoshop_currencies', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 100);
            $table->string('code', 2);
            $table->string('symbol_left', 12)->nullable();
            $table->string('symbol_right', 12)->nullable();
            $table->string('decimal_place', 1);
            $table->double('value', 15, 8);
            $table->date('published_at')->default(Carbon::now());
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('andresrangel_momentoshop_currencies');
    }

}
