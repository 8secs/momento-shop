<?php namespace AndresRangel\MomentoShop\Components;

use Cms\Classes\ComponentBase;
use AndresRangel\MomentoShop\Models\Product as ProductModel;
use Cms\Classes\Page;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Lang;

class Product extends ComponentBase
{

    public $product;

    public $categoryPage;

    public $relatedProducts;

    public function componentDetails()
    {
        return [
            'name'        => 'andresrangel.momentoshop::lang.components.product.name',
            'description' => 'andresrangel.momentoshop::lang.components.product.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'maxItems' => [
                'title'             => 'andresrangel.momentoshop::lang.labels.maxItems',
                'description'       => 'andresrangel.momentoshop::lang.descriptions.maxItems',
                'default'           => 20,
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
            ],
            'slug' => [
                'title'       => 'andresrangel.momentoshop::lang.labels.slug',
                'description' => 'andresrangel.momentoshop::lang.labels.slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ],
            'categoryPage' => [
                'title'       => 'andresrangel.momentoshop::lang.category.label',
                'description' => 'andresrangel.momentoshop::lang.category.description',
                'type'        => 'dropdown',
                'default'     => 'portfolio/category',
            ],
            'orderBy'  => [
                'title'       => 'andresrangel.momentoshop::lang.labels.orderBy',
                'description' => 'andresrangel.momentoshop::lang.descriptions.orderBy',
                'type'        => 'dropdown',
                'default'     => 'id',
            ],
            'sort'     => [
                'title'       => 'andresrangel.momentoshop::lang.labels.sort',
                'description' => 'andresrangel.momentoshop::lang.descriptions.sort',
                'type'        => 'dropdown',
                'default'     => 'desc',
            ],
        ];
    }

    public function onRun()
    {
        $this->product = $this->page['product'] = $this->loadProduct();
        $this->relatedProducts = $this->page['relatedProducts'] = $this->loadRelated();
    }

    protected function loadProduct()
    {
        $slug = $this->property('slug');
        $product = ProductModel::where('slug', $slug)->first();

        if ($product && $product->categories->count()) {
            $product->categories->each(function($category){
                $category->setUrl($this->categoryPage, $this->controller);
            });
        }

        return $product;
    }

    protected function loadRelated(){
        if($this->product && $this->product->categories->count()) {
            foreach($this->product->categories as $category){
                $product_categories[] = $category->id;
            }
            $related = ProductModel::with('picture')
                ->whereHas('categories',
                    function($query) use ($product_categories){
                        $query->whereIn('id', $product_categories);
                    })
                ->where('id', '<>', $this->product->id)
                ->take(3)
                ->get();
            return $related;
        }
    }

    public function getCategoryPageOptions(){
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getOrderByOptions()
    {
        $schema = Schema::getColumnListing('andresrangel_momentoshop_products');
        foreach ($schema as $column) {
            $options[$column] = ucwords(str_replace('_', ' ', $column));
        }
        return $options;
    }

    public function getSortOptions()
    {
        return [
            'desc' => Lang::get('andresrangel.momentoshop::lang.labels.descending'),
            'asc'  => Lang::get('andresrangel.momentoshop::lang.labels.ascending'),
        ];
    }

}