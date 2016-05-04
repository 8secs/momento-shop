<?php namespace AndresRangel\MomentoShop\Models;



/**
 * Address Model
 */
class Address extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'andresrangel_momentoshop_addresses';

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
    public $belongsTo = [
        'customer' => 'AndresRangel\MomentoShop\Models\Customer',
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}