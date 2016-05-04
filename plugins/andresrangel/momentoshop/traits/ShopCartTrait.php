<?php
/**
 * Created by PhpStorm.
 * User: andres
 * Date: 30/03/16
 * Time: 10:36
 */

namespace AndresRangel\MomentoShop\Traits;

use AndresRangel\MomentoShop\Models\Currency;
use AndresRangel\MomentoShop\Models\GeoZone;
use AndresRangel\MomentoShop\Models\Shipping;
use AndresRangel\MomentoShop\Models\Shop;
use AndresRangel\MomentoShop\Models\TaxRate;
use AndresRangel\MomentoShop\Models\Product as ShopProduct;
use Backend\Models\BrandSettings;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use October\Rain\Support\Facades\Flash;
use RainLab\Location\Models\Country;
use RainLab\Location\Models\State;

trait ShopCartTrait
{
    /**
     * Global shop settings
     * @var shop settings
     */
    public $shop;

    /**
     * @var Country shop object
     */
    public $shopCountry;

    /**
     * @var State shop object
     */
    public $shopState;

    /**
     * @var Currency shop object
     */
    public $shopCurrency;

    /**
     * Shop Geo Zone obtain from Country Shop
     *
     * @var GeoZone
     */
    public $geoZone;

    /**
     * Default tax rate of the shop
     * @var float
     */
    public $taxRateShop;

    /**
     * Default tax rate type of the shop:
     * Options:
     *      P = Percentage
     *      F = Fix Amount
     * @var char
     */
    public $taxRateTypeShop;

    /**
     * Array of shippings based on Shop GeoZone
     * @var array
     */
    public $shippings;

    /**
     * Subtotal amount needed before the free
     * shipping module becomes available
     * @var float
     */
    public $freeShipping;

    /**
     * Selected Shipping Object selected by the user
     * when Free Shipping module is not available
     * @var Shipping Object
     */
    public $selectedShippingRate;

    /**
     * Property use to know items remove from cart
     * @var int
     */

    public $items_removed = 0;

    /**
     * Items in basket
     * @var int
     */
    public $basketItems;

    /**
     * Total amount with taxes and shipping
     * @var float
     */
    public $basketTotal;

    /**
     * Total amount before taxes and shipping
     * @var float
     */
    public $basketSubtotal;

    /**
     * Total amount basket taxes
     * @var float
     */
    public $basketTaxRate;

    /**
     * @var int
     */
    public $basketCount = 0;


    /**
     * Boot the cart model
     *
     * @return void|bool
     */
    public static function boot(){ parent::boot(); }

    /**
     * Current shop settings.
     *
     * @return void
     */
    public function getShop()
    {
        $this->shop = Shop::instance();

        $this->shopCountry = Country::find($this->shop->country);
        $this->shopState = State::find($this->shop->state);
        $this->shopCurrency = Currency::find($this->shop->currency);

        $this->getGeoZone();
        $this->getTaxRateShop();
        $this->getFreeShipping();
        $this->getShippings();
    }

    public function registerBasketInfo()
    {
        $content = Cart::instance('shopping')->content();
        $content->each(function ($row) {
            $product = ShopProduct::with('picture')
                ->where('id', $row->product->id)
                ->first();
            $picture = $product->getRelation('picture');
            $row->slug = $row->product->slug;
            $row->picture = $picture;
        });

        /**
         * Check shop settings for ajax calls
         * if not shop, get it.
         */
        if(!isset($this->shop)) $this->getShop();

        $this->basketItems = $this->page['basketItems'] = $content;
        $this->basketCount = $this->page['basketCount'] = Cart::instance('shopping')->count();
        $this->basketSubtotal = $this->page['basketSubtotal'] = Cart::instance('shopping')->total();

        $this->calculateTotal();
    }

    public function onAddProduct()
    {
        $id = post('id');
        $quantity = post('quantity') ?: 1;
        $product = ShopProduct::find($id);

        Cart::instance('shopping')->associate('Product', 'AndresRangel\MomentoShop\Models')->add(
            $id,
            $product->name,
            $quantity,
            $product->price
        );

        $this->registerBasketInfo();
    }

    public function onUpdateCart()
    {
        $post = post();
        $content = Cart::instance('shopping')->content();

        foreach($content as $row){
            Cart::instance('shopping')->update($row->rowid,
                array('qty' => $post['qty-'.$row->rowid]));
        }
        $this->registerBasketInfo();
    }

    protected function assignQty($post, $index){
        return $post[$index];
    }

    public function onRemoveProduct()
    {
        Cart::instance('shopping')->remove(post('row_id'));

        $this->registerBasketInfo();

        return [
            'total' => $this->basketTotal ?: 0,
            'count' => $this->basketCount ?: 0,
        ];
    }

    public function onGoToCheckout()
    {
        if (!$this->stockCheck()) {
            return $this->redirectBackWithRemovedError();
        }

        return Redirect::to($this->checkoutPage);
    }

    public function onCheckout()
    {
        if (!$this->stockCheck()) {
            return $this->redirectBackWithRemovedError();
        }

        $content = Cart::instance('shopping')->content()->toArray();
        $total = Cart::instance('shopping')->total();

        $this->formatPrices($content, $total);

        Mail::sendTo(post('email'), 'andresrangel\momentoshop::mail.orderconfirm', [
            'admin' => false,
            'name'  => post('first_name'),
            'site'  => BrandSettings::get('app_name'),
            'items' => $content,
            'total' => $total,
        ]);

        Mail::sendTo($this->recipientEmail, 'andresrangel\momentoshop::mail.orderconfirm_admin', [
            'admin'   => true,
            'name'    => $this->recipientName,
            'address' => implode('<br>', [
                post('first_name').' '.post('last_name'),
                post('address'),
                post('town'),
                post('county'),
                post('postcode')
            ]),
            'site' => BrandSettings::get('app_name'),
            'items' => $content,
            'total' => $total,
        ]);

        return Redirect::to('/');
    }

    protected function removeCartRow($rowId)
    {
        Cart::instance('shopping')->remove($rowId);

        $this->items_removed++;
    }

    protected function formatPrices(&$items, &$total)
    {

        $countryCode = $this->shopCountry->code;
        $currencyCode = $this->shopCurrency->attributes['code'];
        $formatter = new \NumberFormatter(strtolower($countryCode).'_'.strtoupper($countryCode), \NumberFormatter::CURRENCY);

        foreach ($items as $rowId => $item) {
            $items[$rowId]['price'] = $formatter->formatCurrency($item['price'], $currencyCode);
        };

        $total = $formatter->formatCurrency($total, $currencyCode);
    }

    protected function processItems($items)
    {
        foreach ($items as $item) {
            $this->processItem($item);

            if ($this->items_removed > 0) {
                return false;
            }
        }

        return true;
    }

    protected function processItem($item)
    {
        // If the product doesn't exist, or it does exist but is out
        // of stock, we remove it from the cart and return early
        if (! ($p = ShopProduct::find($item->id))
            ||(isset($p) && !$p->inStock())
        ) {
            $this->removeCartRow($item->rowid);

            return;
        }

        if (!$p->is_stockable) {
            return;
        }

        $p->stock -= $item->qty;
        $p->save();
    }

    protected function calculateTotal(){

        $basketTotal = 0.00;
        if($this->basketSubtotal > 0){
            if((float)$this->freeShipping > 0.00){
                $basketTotal += $this->getSelectedShippingRate();
            }else{
                $this->selectedShippingRate = 0.00;
            }
        }else{
            $this->selectedShippingRate = 0.00;
        }
        if($this->taxRateTypeShop === 'P')
            $this->basketTaxRate = (float)$this->basketSubtotal * (float)$this->taxRateShop;
        else
            $this->basketTaxRate = (float)$this->basketSubtotal + (float)$this->taxRateShop;
        $this->basketTotal += ((float)$basketTotal + (float)$this->basketSubtotal + (float)$this->basketTaxRate);
    }


    protected function stockCheck()
    {
        $this->prepareVars();

        $content = Cart::instance('shopping')->content();

        if (!$this->processItems($content)) {
            return false;
        }

        return $content;
    }

    protected function redirectBackWithRemovedError()
    {
        $removed_many = $this->items_removed > 1;

        Flash::error(sprintf(
            "andresrangel.momentoshop::lang.labels.redirect_cart_with_errors",
            $this->items_removed,
            ($removed_many ? 'items' : 'item'),
            ($removed_many ? 'were' : 'was')
        ));

        return Redirect::back();
    }

    protected function getGeoZone(){
        $shopCountryId[] = $this->shopCountry->id;
        $this->geoZone = GeoZone::whereHas('countries',
            function($query) use ($shopCountryId) {
                $query->whereIn('id', $shopCountryId);
            })->first();
    }

    protected function getShippings(){
        $this->shippings = Shipping::where('geo_zone_id', $this->geoZone->id)
            ->where('total', '=', 0)
            ->get();
    }

    protected function getFreeShipping(){
        $shipping = Shipping::where('total', '>', 0)
            ->where('geo_zone_id', $this->geoZone->id)
            ->first();
        if(isset($shipping)) $this->freeShipping = $shipping->total;
        else $this->freeShipping = 0;
    }

    protected function getSelectedShippingRate(){

        if(!isset($this->selectedShippingRate)){
            if((float)$this->freeShipping
                > (float)$this->basketSubtotal)
            {
                $shipping = Shipping::where('total', '=', 0)
                    ->where('geo_zone_id', $this->geoZone->id)
                    ->min('cost');
                $this->selectedShippingRate = isset($shipping) ? $shipping : 0.00;
            }else{
                $this->selectedShippingRate = 0.00;
            }
        }

        return $this->selectedShippingRate;
    }

    protected function getTaxRateShop(){
        $taxRate = TaxRate::find($this->shop->tax);
        $rate = $taxRate->rate;
        $type = $taxRate->type;
        $this->taxRateTypeShop = $type;
        if($type === 'P') $this->taxRateShop = $rate / 100;
        else $this->taxRateShop = $rate;
    }
}