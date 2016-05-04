<?php namespace AndresRangel\MomentoShop\Components;

use AndresRangel\MomentoShop\Models\Product;
use AndresRangel\MomentoShop\Models\Category as ProductCategory;
use Schema;
use Redirect;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;

class Products extends ComponentBase
{

    /**
     * A collection of products to display
     * @var Collection
     */
    public $products;

    /**
     * Parameter to use for the page number
     * @var string
     */
    public $pageParam;

    /**
     * If the post list should be filtered by a category, the model to use.
     * @var Model
     */
    public $category;

    /**
     * Message to display when there are no messages.
     * @var string
     */
    public $noProductMessage;

    /**
     * Reference to the page name for linking to products.
     * @var string
     */
    public $productPage;

    /**
     * Reference to the page name for linking to categories.
     * @var string
     */
    public $categoryPage;

    /**
     * If the post list should be ordered by another attribute.
     * @var string
     */
    public $sortOrder;

    public function componentDetails()
    {
        return [
            'name'        => 'andresrangel.momentoshop::lang.components.products.name',
            'description' => 'andresrangel.momentoshop::lang.components.products.description'
        ];
    }

    public function defineProperties()
    {
        return [

            'pageNumber' => [
                'title'       => 'andresrangel.momentoshop::lang.products.product_pagination',
                'description' => 'andresrangel.momentoshop::lang.products.product_pagination_description',
                'type'        => 'string',
                'default'     => '{{ :page }}',
            ],
            'categoryFilter' => [
                'title'       => 'andresrangel.momentoshop::lang.products.product_filter',
                'description' => 'andresrangel.momentoshop::lang.products.product_filter_description',
                'type'        => 'string',
                'default'     => ''
            ],
            'productsPerPage' => [
                'title'             => 'andresrangel.momentoshop::lang.products.product_per_page',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'andresrangel.momentoshop::lang.products.product_per_page_validation',
                'default'           => '10',
            ],
            'noProductsMessage' => [
                'title'        => 'andresrangel.momentoshop::lang.products.product_no_products',
                'description'  => 'andresrangel.momentoshop::lang.products.product_no_products_description',
                'type'         => 'string',
                'default'      => 'No products found',
                'showExternalParam' => false
            ],
            'sortOrder' => [
                'title'       => 'andresrangel.momentoshop::lang.products.product_order',
                'description' => 'andresrangel.momentoshop::lang.products.product_order_description',
                'type'        => 'dropdown',
                'default'     => 'published_at desc'
            ],
            'categoryPage' => [
                'title'       => 'andresrangel.momentoshop::lang.category.label',
                'description' => 'andresrangel.momentoshop::lang.category.description',
                'type'        => 'dropdown',
                'default'     => 'portfolio/category',
                'group'       => 'Links',
            ],
            'productPage' => [
                'title'       => 'andresrangel.momentoshop::lang.products.product_page',
                'description' => 'andresrangel.momentoshop::lang.products.product_page_description',
                'type'        => 'dropdown',
                'default'     => 'portfolio/project',
                'group'       => 'Links',
            ],
        ];
    }

    public function getCategoryPageOptions(){
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getProductPageOptions(){
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getSortOrderOptions(){
        return Product::$allowedSortingOptions;
    }

    public function onRun()
    {
        $this->prepareVars();

        $this->category = $this->page['category'] = $this->loadCategory();
        $this->products = $this->page['products'] = $this->listProducts();

        /*
         * If the page number is not valid, redirect
         */
        if ($pageNumberParam = $this->paramName('pageNumber')) {
            $currentPage = $this->property('pageNumber');

            if ($currentPage > ($lastPage = $this->products->lastPage()) && $currentPage > 1)
                return Redirect::to($this->currentPageUrl([$pageNumberParam => $lastPage]));
        }
    }

    protected function prepareVars()
    {
        $this->pageParam = $this->page['pageParam'] = $this->paramName('pageNumber');
        $this->noProductsMessage = $this->page['noProductsMessage'] = $this->property('noProductsMessage');

        /*
         * Page links
         */
        $this->productPage = $this->page['productPage'] = $this->property('productPage');
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
    }

    protected function listProducts()
    {
        $category = $this->category ? $this->category->id : null;

        /*
         * List all the products, eager load their categories
         */
        $products = Product::with('categories')->listFrontEnd([
            'page'       => $this->property('pageNumber'),
            'sort'       => $this->property('sortOrder'),
            'perPage'    => $this->property('productsPerPage'),
            'category'   => $category
        ]);

        /*
         * Add a "url" helper attribute for linking to each product and category

        $products->each(function($project) {
            $project->setUrl($this->productPage, $this->controller);

            $project->categories->each(function($category) {
                $category->setUrl($this->categoryPage, $this->controller);
            });
        });
        */
        $products = $this->setUrls($products);

        return $products;
    }

    protected function setUrls($products){
        $products->each(function($product) {
            $product->setUrl($this->productPage, $this->controller);

            $product->categories->each(function($category) {
                $category->setUrl($this->categoryPage, $this->controller);
            });
        });
        return $products;
    }

    protected function loadCategory()
    {
        if (!$categoryId = $this->property('categoryFilter'))
            return null;

        if (!$category = ProductCategory::whereSlug($categoryId)->first())
            return null;

        return $category;
    }

    public function onFilterSubmit(){
        $filters = array_keys(post());
        $category = $this->category ? $this->category->id : null;

        if(count($filters) > 0){
            $products = Product::with('categories')->listFrontEnd([
                'page'       => $this->property('pageNumber'),
                'sort'       => $this->property('sortOrder'),
                'perPage'    => $this->property('productsPerPage'),
                'category'   => $category,
                'filters'    => $filters,
            ]);
        }else{
            $products = Product::with('categories')->listFrontEnd([
                'page'       => $this->property('pageNumber'),
                'sort'       => $this->property('sortOrder'),
                'perPage'    => $this->property('productsPerPage'),
                'category'   => $category,
            ]);
        }


        $products = $this->setUrls($products);
        $this->products = $this->page['products'] = $products;
    }

}