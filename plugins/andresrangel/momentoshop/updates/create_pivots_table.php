<?php namespace AndresRangel\MomentoShop\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class CreatePivotsTable extends Migration
{

	public $models = [
		'category',
		'product',
		'project',
		'filter',
		'coupon',
	];

	public function up()
	{
		Schema::create('andresrangel_momentoshop_pivots', function ($table) {
			$table->engine = 'InnoDB';
			foreach ($this->models as $model) {
				$table->integer($model . '_id')->unsigned()->nullable()->index();
			}
		});
	}

	public function down()
	{
		Schema::dropIfExists('andresrangel_mycompany_pivots');
	}

}