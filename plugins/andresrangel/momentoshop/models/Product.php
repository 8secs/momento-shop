<?php namespace AndresRangel\MomentoShop\Models;

use App;
use Carbon\Carbon;
use Str;
use Html;
use Lang;
use Illuminate\Support\Facades\DB;

/**
 * Product Model
 */
class Product extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    /**
     * @var string The database table used by the model.
     */
    public $table = 'andresrangel_momentoshop_products';

    /**
     * @var array Validation rules
     */
    protected $rules = [
        'name' => ['required', 'between:4,255'],
        'slug' => [
            'required',
            'alpha_dash',
            'between:1,255',
            'unique:andresrangel_momentoshop_products'
        ],
        'price' => ['numeric', 'max:99999999.99'],
    ];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = ['published_at'];

    /**
     * The attributes on which the project list can be ordered
     * @var array
     */
    public static $allowedSortingOptions = array(
        'title asc' => 'Title (ascending)',
        'title desc' => 'Title (descending)',
        'created_at asc' => 'Created (ascending)',
        'created_at desc' => 'Created (descending)',
        'updated_at asc' => 'Updated (ascending)',
        'updated_at desc' => 'Updated (descending)',
        'published_at asc' => 'Published (ascending)',
        'published_at desc' => 'Published (descending)',
        'random' => 'Random'
    );

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [
        'categories' => ['AndresRangel\MomentoShop\Models\Category',
            'table' => 'andresrangel_momentoshop_pivots',
        ],
        'filters' => ['AndresRangel\MomentoShop\Models\Filter',
            'table' => 'andresrangel_momentoshop_pivots',
        ],
        'coupons' => [
            'AndresRangel\MomentoShop\Models\Coupon',
            'table' => 'andresrangel_momentoshop_pivots'
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


    public function inStock()
    {
        if (!$this->is_stockable) {
            return true;
        }

        return $this->stock > 0;
    }

    public function outOfStock()
    {
        return !$this->inStock();
    }

    public function getSquareThumb($size, $image)
    {
        return $image->getThumb($size, $size, ['mode' => 'crop']);
    }

    public function setUrl($pageName, $controller)
    {
        $params = [
            'id' => $this->id,
            'slug' => $this->slug,
        ];

        if (array_key_exists('categories', $this->getRelations())) {
            $params['category'] = $this->categories->count() ? $this->categories->first()->slug : null;
        }

        return $this->url = $controller->pageUrl($pageName, $params);
    }

    public function beforeSave()
    {
        $postProduct = post('Product');
        if(strlen($postProduct['published_at']) == 0) $this->published_at = Carbon::now();
    }

    public function afterDelete()
    {
        if($this->picture) $this->picture->delete();
        if($this->pictures ){
            foreach ($this->pictures as $item) {
                $item->delete();
            }
        }
        if($this->files){
            foreach ($this->files as $item) {
                $item->delete();
            }
        }

        if($this->categories()) $this->categories()->detach();
        if($this->filters()) $this->filters()->detach();
        if($this->coupons()) $this->coupons()->detach();
    }

    public function getCategoriesOptions()
    {
        return Category::all()->lists('name', 'id');

    }

    public function getFiltersOptions(){
        return Filter::all()->list('name', 'id');
    }

    public function getCouponsOptions(){
        return Coupon::all()->list('name', 'id');
    }

    /**
     * Lists products for the front end
     * @param  array $options Display options
     * @return self
     */
    public function scopeListFrontEnd($query, $options)
    {
        /*
         * Default options
         */
        extract(array_merge([
            'page'       => 1,
            'perPage'    => 30,
            'sort'       => 'created_at',
            'categories' => null,
            'category'   => null,
            'filters'    => null,
            'search'     => '',
        ], $options));

        $searchableFields = ['name', 'slug', 'description'];

        /*
         * Sorting
         */

        if (!is_array($sort)) {
            $sort = [$sort];
        }

        foreach ($sort as $_sort) {

            if (in_array($_sort, array_keys(self::$allowedSortingOptions))) {
                $parts = explode(' ', $_sort);
                if (count($parts) < 2) {
                    array_push($parts, 'desc');
                }
                list($sortField, $sortDirection) = $parts;
                if ($sortField == 'random') {
                    $sortField = DB::raw('RAND()');
                }
                $query->orderBy($sortField, $sortDirection);
            }
        }

        /*
         * Search
         */
        $search = trim($search);
        if (strlen($search)) {
            $query->searchWhere($search, $searchableFields);
        }

        /*
         * Categories
         */
        if ($categories !== null) {
            if (!is_array($categories)) $categories = [$categories];
            $query->whereHas('categories', function($q) use ($categories) {
                $q->whereIn('id', $categories);
            });
        }

        /*
         * Category, including children
         */
        if ($category !== null) {
            $category = Category::find($category);
            if($category){
                $categories = $category->getAllChildrenAndSelf()->lists('id');
                $query->whereHas('categories', function($q) use ($categories) {
                    $q->whereIn('id', $categories);
                });
            }
        }

        if($filters !== null) {
            if(!is_array($filters)) $filters = [$filters];
            $query->whereHas('filters', function($q) use ($filters) {
                $q->whereIn('id', $filters);
            });
        }

        return $query->paginate($perPage, $page);
    }

    /**
     * Allows filtering for specifc categories
     * @param  Illuminate\Query\Builder  $query      QueryBuilder
     * @param  array                     $categories List of category ids
     * @return Illuminate\Query\Builder              QueryBuilder
     */
    public function scopeFilterCategories($query, $categories)
    {
        return $query->whereHas('categories', function($q) use ($categories) {
            $q->whereIn('id', $categories);
        });
    }

}