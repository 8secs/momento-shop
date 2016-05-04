<?php namespace AndresRangel\MomentoShop\Models;
use Carbon\Carbon;


/**
 * FilterType Model
 */
class FilterType extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'andresrangel_momentoshop_filter_types';

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
    public $hasMany = [
        'filters' => 'AndresRangel\MomentoShop\Models\Filter',
    ];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function beforeSave()
    {
        $postFilter = post('FilterType');
        if(strlen($postFilter['published_at']) == 0) $this->published_at = Carbon::now();
    }

}