<?php namespace AndresRangel\MomentoShop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateCustomersTable extends Migration
{

    public function up()
    {
        Schema::create('andresrangel_momentoshop_customers', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('username', 255)->nullable();
            $table->string('phone', 9)->nullable();
            $table->string('fax', 9)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('andresrangel_momentoshop_customers');
    }

}
