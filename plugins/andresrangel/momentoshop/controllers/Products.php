<?php namespace AndresRangel\MomentoShop\Controllers;

use AndresRangel\MomentoShop\Models\Product;
use BackendMenu;
use Backend\Classes\Controller;
use Illuminate\Support\Facades\Lang;
use October\Rain\Support\Facades\Flash;

/**
 * Products Back-end Controller
 */
class Products extends Controller
{
    public $requiredPermissions = ['andresrangel.momentoshop.access_products'];

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

        BackendMenu::setContext('AndresRangel.MomentoShop', 'momentoshop', 'products');
    }

    /**
     * Deleted checked products.
     */
    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $productId) {
                if (!$product = Product::find($productId)) {
                    continue;
                }

                $product->delete();
            }

            Flash::success(Lang::get('andresrangel.momentoshop::lang.products.delete_selected_success'));
        } else {
            Flash::error(Lang::get('andresrangel.momentoshop::lang.products.delete_selected_empty'));
        }

        return $this->listRefresh();
    }
}