<?php namespace AndresRangel\MomentoShop\Updates;

use Carbon\Carbon;
use Schema;
use October\Rain\Database\Updates\Migration;

class CreateProductsTable extends Migration
{

    public function up()
    {
        Schema::create('andresrangel_momentoshop_products', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->index();
            $table->string('slug')->index()->unique();
            $table->text('description');
            $table->string('modelo')->nullable();
            $table->decimal('price', 10, 2)->default(0)->nullable();
            $table->boolean('is_stockable')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->integer('stock')->default(0)->nullable();
            $table->date('published_at')->default(Carbon::now());
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('andresrangel_momentoshop_products');
    }

}
