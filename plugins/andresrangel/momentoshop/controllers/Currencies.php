<?php namespace AndresRangel\MomentoShop\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Currencies Back-end Controller
 */
class Currencies extends Controller
{

    public $requiredPermissions = ['andresrangel.momentoshop.access_currencies'];

    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('AndresRangel.MomentoShop', 'momentoshop', 'currencies');
    }

    /**
     * Deleted checked services.
     */
    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $categoryId) {
                if (!$category = Category::find($categoryId)) {
                    continue;
                }

                $category->delete();
            }

            Flash::success(Lang::get('andresrangel.momentoshop::lang.currencies.delete_selected_success'));
        } else {
            Flash::error(Lang::get('andresrangel.momentoshop::lang.currencies.delete_selected_empty'));
        }

        return $this->listRefresh();
    }
}