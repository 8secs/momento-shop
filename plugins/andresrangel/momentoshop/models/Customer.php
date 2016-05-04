<?php namespace AndresRangel\MomentoShop\Models;

use Auth;
use Str;


/**
 * Customer Model
 */
class Customer extends Model
{
    public $default_address;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'andresrangel_momentoshop_customers';

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [

    ];
    public $hasMany = [
        'addresses' => 'AndresRangel\MomentoShop\Models\Address',
    ];
    public $belongsTo = [
        'user' => 'RainLab\User\Models\User',
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    /**
     * Automatically creates a forum member for a user if not one already.
     * @param  RainLab\User\Models\User $user
     * @return AndresRangel\MomentoShop\Models\Customer
     */
    public static function getFromUser($user = null)
    {

        if ($user === null)
            $user = Auth::getUser();

        if (!$user)
            return null;

        if (!$user->customer) {
            $generatedUsername = explode('@', $user->email);
            $generatedUsername = array_shift($generatedUsername);
            $generatedUsername = Str::limit($generatedUsername, 24, '') . $user->id;

            $customer = new static;
            $customer->user = $user;
            $customer->username = $generatedUsername;
            $customer->save();

            $customer->addresses = static::getAddresses();
            $user->customer = $customer;
        }else{
            $customer = $user->customer;
            $customer->addresses = static::getAddresses($customer->id);

            $user->customer = $customer;
        }
        return $user->customer;
    }

    public function getAddressesOptions(){
        return Address::where('customer_id', '=', $this->id)->get();
    }

    public static function getAddresses($id){
        return Address::where('customer_id', '=', $id)->get();
    }
}