<?php namespace AndresRangel\MomentoShop\Models;
use Carbon\Carbon;


/**
 * Filter Model
 */
class Filter extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'andresrangel_momentoshop_filters';

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [

    ];
    public $hasMany = [];
    public $belongsTo = [
        'filter_type' => 'AndresRangel\MomentoShop\Models\FilterType',
    ];
    public $belongsToMany = [
        'products' => ['AndresRangel\MomentoShop\Models\Product',
            'table' => 'andresrangel_momentoshop_pivots',
        ],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function beforeSave()
    {
        $postFilter = post('Filter');
        if(strlen($postFilter['published_at']) == 0) $this->published_at = Carbon::now();
    }

    public function afterDelete()
    {
        if($this->products()) $this->products()->detach();
    }

    public function getProductsOptions()
    {
        return Product::all()->lists('name', 'id');
    }

}