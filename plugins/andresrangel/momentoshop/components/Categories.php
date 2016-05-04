<?php namespace AndresRangel\MomentoShop\Components;

use AndresRangel\MomentoShop\Models\Category;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Illuminate\Support\Facades\DB;

class Categories extends ComponentBase
{

    /**
     * @var Collection A collection of categories to display
     */
    public $categories;

    /**
     * @var string Reference to the page name for linking to categories.
     */
    public $categoryPage;

    /**
     * @var string Reference to the current category slug.
     */
    public $currentCategorySlug;

    public function componentDetails()
    {
        return [
            'name'        => 'andresrangel.momentoshop::lang.components.categories.name',
            'description' => 'andresrangel.momentoshop::lang.components.categories.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'andresrangel.momentoshop::lang.labels.slug',
                'description' => 'andresrangel.momentoshop::lang.labels.slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ],
            'displayEmpty' => [
                'title'       => 'andresrangel.momentoshop::lang.labels.display_empty',
                'description' => 'andresrangel.momentoshop::lang.labels.display_empty_description',
                'type'        => 'checkbox',
                'default'     => 0
            ],
            'categoryPage' => [
                'title'       => 'andresrangel.momentoshop::lang.category.label',
                'description' => 'andresrangel.momentoshop::lang.lang.category.description',
                'type'        => 'dropdown',
                'default'     => 'portfolio/category',
                'group'       => 'Links',
            ],
        ];
    }

    public function getCategoryPageOptions (){
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->currentCategorySlug = $this->page['currentCategorySlug'] = $this->property('slug');
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
        $this->categories = $this->page['categories'] = $this->loadCategories();
    }

    protected function loadCategories()
    {
        $categories = Category::orderBy('name');
        if (!$this->property('displayEmpty')) {
            $categories->whereExists(function($query) {
                $query->select(Db::raw(1))
                    ->from('andresrangel_momentoshop_categories');
            });
        }

        $categories = $categories->getNested();
        return $this->linkCategories($categories);
    }

    protected function linkCategories($categories)
    {
        return $categories->each(function($category) {
            $category->setUrl($this->categoryPage, $this->controller);

            if ($category->children) {
                $this->linkCategories($category->children);
            }
        });
    }

}