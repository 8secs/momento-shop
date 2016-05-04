<?php namespace AndresRangel\MomentoShop\Controllers;

use AndresRangel\MomentoShop\Models\FilterType;
use BackendMenu;
use Backend\Classes\Controller;
use Illuminate\Support\Facades\Lang;
use October\Rain\Support\Facades\Flash;

/**
 * Filter Types Back-end Controller
 */
class FilterTypes extends Controller
{
    public $requiredPermissions = ['andresrangel.momentoshop.access_filter_types'];

    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController',
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('AndresRangel.MomentoShop', 'momentoshop', 'filtertypes');
    }

    /**
     * Deleted checked services.
     */
    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $categoryId) {
                if (!$category = FilterType::find($categoryId)) {
                    continue;
                }

                $category->delete();
            }

            Flash::success(Lang::get('andresrangel.momentoshop::lang.filter_types.delete_selected_success'));
        } else {
            Flash::error(Lang::get('andresrangel.momentoshop::lang.filter_types.delete_selected_empty'));
        }

        return $this->listRefresh();
    }
}