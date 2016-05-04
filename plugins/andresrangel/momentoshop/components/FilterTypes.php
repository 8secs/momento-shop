<?php namespace AndresRangel\MomentoShop\Components;

use AndresRangel\MomentoShop\Models\FilterType;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Schema;

class FilterTypes extends ComponentBase
{

    public $filter_types;

    /**
     * @var string Reference to the page name for linking to categories.
     */
    public $categoryPage;

    public function componentDetails()
    {
        return [
            'name'        => 'andresrangel.momentoshop::lang.components.filter_types.name',
            'description' => 'andresrangel.momentoshop::lang.components.filter_types.description'
        ];
    }

    public function defineProperties()
    {
        return [
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
            'categoryPage' => [
                'title'       => 'andresrangel.momentoshop::lang.category.label',
                'description' => 'andresrangel.momentoshop::lang.lang.category.description',
                'type'        => 'dropdown',
                'default'     => 'shop/categories',
                'group'       => 'Links',
            ],
        ];
    }

    public function getCategoryPageOptions (){
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $filter_types = FilterType::published()
            ->with('filters')
            ->orderBy($this->property('orderBy', 'id'), $this->property('sort', 'desc'))
            ->get();
        $this->filter_types = $this->page['filter_types'] = $filter_types;


    }

    public function getOrderByOptions()
    {
        $schema = Schema::getColumnListing('andresrangel_momentoshop_filter_types');
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