<?php namespace AndresRangel\MomentoShop\Models;
use Carbon\Carbon;


/**
 * Category Model
 */
class Category extends Model
{

    use \October\Rain\Database\Traits\NestedTree;
    use \October\Rain\Database\Traits\Purgeable;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'andresrangel_momentoshop_categories';

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['is_subcategory', 'parent_id'];

    /**
     * @var array Purgeable fields
     */
    protected $purgeable = ['is_subcategory'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'children' => ['AndresRangel\MomentoShop\Models\Category', 'key' => 'parent_id'],
    ];
    public $belongsTo = [
        'parent' => ['AndresRangel\MomentoShop\Models\Category', 'key' => 'parent_id'],
    ];
    public $belongsToMany = [
        'products' => ['AndresRangel\MomentoShop\Models\Product',
            'table' => 'andresrangel_momentoshop_pivots',
        ],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [
        'picture' => ['System\Models\File'],
    ];
    public $attachMany = [
        'pictures' => ['System\Models\File'],
    ];

    public function afterFetch()
    {
        $this->is_subcategory = !!$this->parent_id;
    }

    public function beforeSave()
    {

        $postCategory = post('Category');
        if(strlen($postCategory['published_at']) == 0) $this->published_at =  Carbon::now();

        if($this->getOriginalPurgeValue('is_subcategory') == 0){
            $this->parent_id = null;
        }

        $this->storeNewParent();
    }

    public function setUrl($pageName, $controller)
    {
        $params = [
            'id' => $this->id,
            'slug' => $this->slug,
        ];

        return $this->url = $controller->pageUrl($pageName, $params);
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