<?php namespace AndresRangel\MomentoShop\Models;



/**
 * Coupon Model
 */
class Coupon extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'andresrangel_momentoshop_coupons';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [
        'products' => [
            'AndresRangel\MomentoShop\Models\Product',
            'table' => 'andresrangel_momentoshop_pivots',
        ],
        'categories' => ['AndresRangel\MomentoShop\Models\Category',
            'table' => 'andresrangel_momentoshop_pivots',
        ],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];


    public function beforeSave(){

    }

    public function afterDelete()
    {
        if($this->products()) $this->products()->detach();
        if($this->categories()) $this->categories()->detach();
    }

    public function getProductsOptions(){
        return Product::all()->list('name', 'id');
    }

    public function getCategoriesOptions()
    {
        return Category::all()->lists('name', 'id');

    }

}