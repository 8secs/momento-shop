<?php namespace AndresRangel\MomentoShop\Components;


use AndresRangel\MomentoShop\Traits\ShopCartTrait;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;

class Basket extends ComponentBase
{
    use ShopCartTrait;



    public function componentDetails()
    {
        return [
            'name'        => 'andresrangel.momentoshop::lang.components.basket.name',
            'description' => 'andresrangel.momentoshop::lang.components.basket.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'checkoutPage' => [
                'title'       => 'Checkout Page',
                'description' => 'Name of the page to redirect to when a user clicks Proceed to Checkout.',
                'type'        => 'dropdown',
                'default'     => 'shop/checkout',
                'group'       => 'Links',
            ],
            'productPage' => [
                'title'       => 'Product Page',
                'description' => 'Name of the product page for the product titles.',
                'type'        => 'dropdown',
                'default'     => 'shop/product',
                'group'       => 'Links',
            ],
            'recipientName' => [
                'title'       => 'Recipient Name',
                'description' => 'Name of the person to receive order confirmations',
                'group'       => 'Order confirmation email',
            ],
            'recipientEmail' => [
                'title'       => 'Recipient Email',
                'description' => 'Email address to receive order confirmation emails',
                'group'       => 'Order confirmation email',
            ],
        ];
    }

    public function getCheckoutPageOptions()
    {
        return $this->getPagesDropdown();
    }

    public function getProductPageOptions()
    {
        return $this->getPagesDropdown();
    }

    public function getPagesDropdown()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->prepareVars();
    }

    public function prepareVars()
    {
        $this->getShop();
        $this->registerBasketInfo();
        $this->recipientEmail = $this->page['recipientEmail'] = $this->property('recipientEmail');
        $this->recipientName = $this->page['recipientName'] = $this->property('recipientName');
    }

}
