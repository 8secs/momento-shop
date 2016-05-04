<?php namespace AndresRangel\MomentoShop;

use AndresRangel\MomentoShop\Models\Address;
use AndresRangel\MomentoShop\Models\Customer;
use Backend;
use Backend\Facades\BackendAuth;
use RainLab\Location\Models\Country;
use RainLab\Location\Models\State;
use System\Classes\PluginBase;
use BackendMenu;
use RainLab\User\Models\User as UserModel;
use RainLab\User\Controllers\Users as UserController;

/**
 * MomentoShop Plugin Information File
 */
class Plugin extends PluginBase
{

    public $require = ['RainLab.Location', 'RainLab.User'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'andresrangel.momentoshop::lang.plugin.name',
            'description' => 'andresrangel.momentoshop::lang.plugin.description',
            'author'      => 'Andres Rangel',
            'icon'        => 'icon-shopping-cart',
        ];
    }

    public function registerNavigation()
    {
        return [
            'momentoshop' => [
                'label'       => 'andresrangel.momentoshop::lang.plugin.name',
                'url'         => Backend::url('andresrangel/momentoshop/'. $this->startPage()),
                'icon'        => 'icon-shopping-cart',
                'permissions' => ['andresrangel.momentoshop.*'],
                'order'       => 500,

                'sideMenu'    => [
                    
                    'products'     => [
                        'label'       => 'andresrangel.momentoshop::lang.products.menu_label',
                        'icon'        => 'icon-rocket',
                        'url'         => Backend::url('andresrangel/momentoshop/products'),
                        'permissions' => ['andresrangel.momentoshop.access_products'],
                        'group'       => 'andresrangel.momentoshop::lang.sidebar.catalog',
                        'description' => 'andresrangel.momentoshop::lang.product.description',
                    ],
                    'categories'     => [
                        'label'       => 'andresrangel.momentoshop::lang.categories.menu_label',
                        'icon'        => 'icon-cubes',
                        'url'         => Backend::url('andresrangel/momentoshop/categories'),
                        'permissions' => ['andresrangel.momentoshop.access_categories'],
                        'group'       => 'andresrangel.momentoshop::lang.sidebar.catalog',
                        'description' => 'andresrangel.momentoshop::lang.category.description',
                    ],
                    'filters'     => [
                        'label'       => 'andresrangel.momentoshop::lang.filters.menu_label',
                        'icon'        => 'icon-filter',
                        'url'         => Backend::url('andresrangel/momentoshop/filters'),
                        'permissions' => ['andresrangel.momentoshop.access_filters'],
                        'group'       => 'andresrangel.momentoshop::lang.sidebar.catalog',
                        'description' => 'andresrangel.momentoshop::lang.filter.description',
                    ],
                    'filter_types'     => [
                        'label'       => 'andresrangel.momentoshop::lang.filter_types.menu_label',
                        'icon'        => 'icon-folder',
                        'url'         => Backend::url('andresrangel/momentoshop/filtertypes'),
                        'permissions' => ['andresrangel.momentoshop.access_filtertypes'],
                        'group'       => 'andresrangel.momentoshop::lang.sidebar.catalog',
                        'description' => 'andresrangel.momentoshop::lang.filter_type.description',
                    ],
                    'coupons'     => [
                        'label'       => 'andresrangel.momentoshop::lang.coupons.menu_label',
                        'icon'        => 'icon-gift',
                        'url'         => Backend::url('andresrangel/momentoshop/coupons'),
                        'permissions' => ['andresrangel.momentoshop.access_coupons'],
                        'group'       => 'andresrangel.momentoshop::lang.sidebar.marketing',
                        'description' => 'andresrangel.momentoshop::lang.coupon.description',
                    ],
                    'currencies'     => [
                        'label'       => 'andresrangel.momentoshop::lang.currencies.menu_label',
                        'icon'        => 'icon-money',
                        'url'         => Backend::url('andresrangel/momentoshop/currencies'),
                        'permissions' => ['andresrangel.momentoshop.access_currencies'],
                        'group'       => 'andresrangel.momentoshop::lang.sidebar.localisation',
                        'description' => 'andresrangel.momentoshop::lang.currency.description',
                    ],
                    'geozones'     => [
                        'label'       => 'andresrangel.momentoshop::lang.geo_zones.menu_label',
                        'icon'        => 'icon-globe',
                        'url'         => Backend::url('andresrangel/momentoshop/geozones'),
                        'permissions' => ['andresrangel.momentoshop.access_geo_zones'],
                        'group'       => 'andresrangel.momentoshop::lang.sidebar.localisation',
                        'description' => 'andresrangel.momentoshop::lang.geo_zone.description',
                    ],
                    'taxrates'     => [
                        'label'       => 'andresrangel.momentoshop::lang.tax_rates.menu_label',
                        'icon'        => 'icon-gavel',
                        'url'         => Backend::url('andresrangel/momentoshop/taxrates'),
                        'permissions' => ['andresrangel.momentoshop.access_tax_rates'],
                        'group'       => 'andresrangel.momentoshop::lang.sidebar.localisation',
                        'description' => 'andresrangel.momentoshop::lang.tax_rate.description',
                    ],
                    'shippings'     => [
                        'label'       => 'andresrangel.momentoshop::lang.shippings.menu_label',
                        'icon'        => 'icon-truck',
                        'url'         => Backend::url('andresrangel/momentoshop/shippings'),
                        'permissions' => ['andresrangel.momentoshop.access_shippings'],
                        'group'       => 'andresrangel.momentoshop::lang.sidebar.localisation',
                        'description' => 'andresrangel.momentoshop::lang.shipping.description',
                    ],
                ],
            ],
        ];
    }

    public function startPage($page = 'projects')
    {
        $user = BackendAuth::getUser();
        $permissions = array_reverse(array_keys($this->registerPermissions()));

        if (!isset($user->permissions['superuser']) && $user->hasAccess('andresrangel.momentoshop.*')) {
            foreach ($permissions as $access) {
                if ($user->hasAccess($access)) {
                    $page = explode('_', $access)[1];
                }
            }
        }
        //print_r($page);
        return $page;
    }

    public function registerPermissions()
    {
        return [
            
            'andresrangel.momentoshop.access_products'     => [
                'label' => 'andresrangel.momentoshop::lang.product.list_title',
                'tab'   => 'andresrangel.momentoshop::lang.plugin.name',
            ],
            'andresrangel.momentoshop.access_categories'     => [
                'label' => 'andresrangel.momentoshop::lang.category.list_title',
                'tab'   => 'andresrangel.momentoshop::lang.plugin.name',
            ],
            'andresrangel.momentoshop.access_filters'     => [
                'label' => 'andresrangel.momentoshop::lang.filter.list_title',
                'tab'   => 'andresrangel.momentoshop::lang.plugin.name',
            ],
            'andresrangel.momentoshop.access_coupons'     => [
                'label' => 'andresrangel.momentoshop::lang.coupon.list_title',
                'tab'   => 'andresrangel.momentoshop::lang.plugin.name',
            ],
            'andresrangel.momentoshop.access_currencies'     => [
                'label' => 'andresrangel.momentoshop::lang.currency.list_title',
                'tab'   => 'andresrangel.momentoshop::lang.plugin.name',
            ],
            'andresrangel.momentoshop.access_tax_rates'     => [
                'label' => 'andresrangel.momentoshop::lang.tax_rates.list_title',
                'tab'   => 'andresrangel.momentoshop::lang.plugin.name',
            ],
            'andresrangel.momentoshop.access_shippings'     => [
                'label' => 'andresrangel.momentoshop::lang.shippings.list_title',
                'tab'   => 'andresrangel.momentoshop::lang.plugin.name',
            ],
            'andresrangel.momentoshop.access_geo_zones'     => [
                'label' => 'andresrangel.momentoshop::lang.geo_zones.list_title',
                'tab'   => 'andresrangel.momentoshop::lang.plugin.name',
            ],
            'andresrangel.momentoshop.access_filter_types'     => [
                'label' => 'andresrangel.momentoshop::lang.filter_type.list_title',
                'tab'   => 'andresrangel.momentoshop::lang.plugin.name',
            ],
            'andresrangel.momentoshop.access_shop'      => [
                'label' => 'andresrangel.momentoshop::lang.shop.list_title',
                'tab'   => 'andresrangel.momentoshop::lang.plugin.name',
            ],
        ];
    }

    public function registerComponents()
    {
        return [
            'AndresRangel\MomentoShop\Components\Products'                => 'Products',
            'AndresRangel\MomentoShop\Components\Product'                 => 'Product',
            'AndresRangel\MomentoShop\Components\Categories'              => 'Categories',
            'AndresRangel\MomentoShop\Components\FilterTypes'             => 'FilterTypes',
            'AndresRangel\MomentoShop\Components\Basket'                  => 'ShopBasket',
            'AndresRangel\MomentoShop\Components\Customer'                => 'Customer',
        ];
    }

    public function registerSettings()
    {
        return [
            'shop' => [
                'label'       => 'andresrangel.momentoshop::lang.labels.shop',
                'description' => 'andresrangel.momentoshop::lang.labels.shop-settings',
                'category'    => 'system::lang.system.categories.system',
                'icon'        => 'icon-shopping-cart',
                'class'       => 'AndresRangel\MomentoShop\Models\Shop',
                'order'       => 500,
                'keywords'    => 'andresrangel.momentoshop::lang.shop.keywords',
                'permissions' => ['andresrangel.momentoshop.access_shop'],
            ],
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'andresrangel.momentoshop::mail.activate'   => 'Activation email sent to new users.',
            'andresrangel.momentoshop::mail.welcome'    => 'Welcome email sent when a user is activated.',
            'andresrangel.momentoshop::mail.restore'    => 'Password reset instructions for front-end users.',
            'andresrangel.momentoshop::mail.new_user'   => 'Sent to administrators when a new user joins.',
            'andresrangel.momentoshop::mail.reactivate' => 'Notification for users who reactivate their account.',
        ];
    }

    public function register()
    {
        BackendMenu::registerContextSidenavPartial('AndresRangel.MomentoShop', 'momentoshop', '@/plugins/andresrangel/momentoshop/partials/_sidebar.htm');
    }

    public function boot()
    {
        UserModel::extend(function($model){
            $model->hasOne['customer'] = ['AndresRangel\MomentoShop\Models\Customer'];

            $model->bindEvent('model.beforeDelete', function() use ($model) {
                $model->customer && $model->customer->delete();
            });

        });

        Country::extend(function($model){
            $model->belongsToMany['geoZone'] = ['AndresRangel\MomentoShop\Models\GeoZone'];
        });

        State::extend(function($model){
            $model->belongsToMany['geoZone'] = ['AndresRangel\MomentoShop\Models\GeoZone'];
        });

        UserController::extendFormFields(function($form, $model, $context){

            if(!$model instanceof UserModel)
                return;

            if(!$model->exists) return;

            if(!Customer::getFromUser($model)) return;

            $form->addTabFields([
                'customer[username]' => [
                    'label' => 'andresrangel.momentoshop::lang.labels.username',
                    'span'  => 'auto',
                    'tab'   => 'andresrangel.momentoshop::lang.labels.details',
                ],
                'customer[phone]' => [
                    'label' => 'andresrangel.momentoshop::lang.labels.phone',
                    'span'  => 'auto',
                    'tab'   => 'andresrangel.momentoshop::lang.labels.details',
                ],
                'customer[fax]' => [
                    'label' => 'andresrangel.momentoshop::lang.labels.fax',
                    'span'  => 'auto',
                    'tab'   => 'andresrangel.momentoshop::lang.labels.details',
                ],
                'customer[addresses]' => [
                    'label' => 'Addresses',
                    'span'  => 'auto',
                    'type'  => 'dropdown',
                    'tab'   => 'andresrangel.momentoshop::lang.labels.details',
                ],
            ]);
        });
    }

}
