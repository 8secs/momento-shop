<?php namespace AndresRangel\MomentoShop\Models;

use October\Rain\Database\Model as BaseModel;

class Model extends BaseModel
{
	
    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];
	
    public $translatable = ['name', 'description', 'content', 'information', 'quote', 'story', 'slogan'];
	
    protected $dates = ['published_at'];
	
    protected $guarded = ['*'];

	public function scopePublished($query)
	{
		return $query->where('published_at', '<', date('Y-m-d H:i:s'));
	}
}