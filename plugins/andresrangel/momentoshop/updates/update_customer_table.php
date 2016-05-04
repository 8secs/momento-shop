<?php
/**
 * Created by PhpStorm.
 * User: andres
 * Date: 31/03/16
 * Time: 15:15
 */

namespace AndresRangel\MomentoShop\Updates;


use Illuminate\Support\Facades\Schema;
use October\Rain\Database\Updates\Migration;

class UpdateCustomerTable extends Migration
{
    public function up(){
        Schema::table('andresrangel_momentoshop_customers', function($table)
        {
            $table->integer('user_id')->unsigned();
            /*$table->string('username', 255)->nullable();
            $table->string('slug', 255)->nullable();
            $table->string('phone', 9)->nullable();
            $table->string('fax', 9)->nullable();*/
        });
    }

    public function down(){

    }

}