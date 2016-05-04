<?php namespace AndresRangel\MomentoShop\Components;

use AndresRangel\MomentoShop\Models\Address;
use Lang;
use Auth;
use Mail;
use Event;
use Flash;
use Input;
use RainLab\Location\Models\Country;
use RainLab\Location\Models\State;
use RainLab\User\Components\Account;
use RainLab\User\Models\User;
use Request;
use Redirect;
use Validator;
use ValidationException;
use ApplicationException;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use RainLab\User\Models\Settings as UserSettings;
use Exception;
use AndresRangel\MomentoShop\Models\Customer as CustomerModel;

class Customer extends Account
{

    public $countries;

    public $states;

    public $customer;


    public function componentDetails()
    {
        return [
            'name'        => 'andresrangel.momentoshop::lang.components.customer.name',
            'description' => 'andresrangel.momentoshop::lang.components.customer.description',
        ];
    }

    public function defineProperties()
    {
        return [
            'redirect' => [
                'title'       => 'andresrangel.momentoshop::lang.customer.redirect_to',
                'description' => 'andresrangel.momentoshop::lang.customer.redirect_to_desc',
                'type'        => 'dropdown',
                'default'     => ''
            ],
            'paramCode' => [
                'title'       => 'andresrangel.momentoshop::lang.customer.code_param',
                'description' => 'andresrangel.momentoshop::lang.customer.code_param_desc',
                'type'        => 'string',
                'default'     => 'code'
            ]
        ];
    }

    public function onRun()
    {

        $this->getCountries();
        parent::onRun();
        //$this->customer();
    }

    public function customer(){

        $customer = CustomerModel::getFromUser(parent::user());
        return $customer;
    }

    /**
     * Register the user
     */
    public function onRegister()
    {
        try {
            if (!UserSettings::get('allow_registration', true)) {
                throw new ApplicationException(Lang::get('andresrangel.momentoshop::lang.customer.registration_disabled'));
            }

            /*
             * Validate input
             */
            $data = post();


            if (!array_key_exists('password_confirmation', $data)) {
                $data['password_confirmation'] = post('password');
            }

            $rules = [
                'email'    => 'required|email|between:6,255',
                'password' => 'required|between:4,255'
            ];

            if ($this->loginAttribute() == UserSettings::LOGIN_USERNAME) {
                $rules['username'] = 'required|between:2,255';
            }

            $validation = Validator::make($data, $rules);
            if ($validation->fails()) {
                throw new ValidationException($validation);
            }

            /*
             * Register user
             */
            $requireActivation = UserSettings::get('require_activation', true);
            $automaticActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_AUTO;
            $userActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_USER;
            $user = Auth::register($data, $automaticActivation);

            if($user){

                $customer = new CustomerModel();
                $customer->user = $user;
                $customer->username = $user->username;
                $customer->phone = $data['phone'];
                $customer->save();

                $address = new Address();
                $address->customer = $customer;
                $address->address_1 = post('address_1');
                $address->address_2 = post('address_2');
                $address->city = post('city');
                $address->postcode = post('postcode');
                $address->country_id = post('countries');
                $address->state_id = post('states');
                $address->save();
                $customer->addresses = $address;
            }

            /*
             * Activation is by the user, send the email
             */
            if ($userActivation) {
                $this->sendActivationEmail($user);

                Flash::success(Lang::get('andresrangel.momentoshop::lang.customer.activation_email_sent'));
            }

            /*
             * Automatically activated or not required, log the user in
             */
            if ($automaticActivation || !$requireActivation) {
                Auth::login($user);
            }

            /*
             * Redirect to the intended page after successful sign in
             */
            $redirectUrl = $this->pageUrl($this->property('redirect'))
                ?: $this->property('redirect');

            if ($redirectUrl = post('redirect', $redirectUrl)) {
                return Redirect::intended($redirectUrl);
            }

        }
        catch (Exception $ex) {
            if (Request::ajax()) throw $ex;
            else Flash::error($ex->getMessage());
        }
    }

    /**
     * Update the user
     */
    public function onUpdate()
    {
        if (!$user = $this->user()) {
            return;
        }

        $user->fill(post());
        $user->save();

        /*
         * Password has changed, reauthenticate the user
         */
        if (strlen(post('password'))) {
            Auth::login($user->reload(), true);
        }

        Flash::success(post('flash', Lang::get('andresrangel.momentoshop::lang.customer.success_saved')));

        /*
         * Redirect
         */
        if ($redirect = $this->makeRedirection()) {
            return $redirect;
        }
    }

    public function getCountries(){

        $this->countries = Country::all();
        //print_r($this->countries);
    }

    public function onCountryChange(){
        //print_r("country ". post('country'));
        $countryID = post('country');
        $this->states = State::where('country_id', '=', $countryID)->get();
        return $this->states;
    }

    /**
     * Trigger a subsequent activation email
     */
    public function onSendActivationEmail()
    {
        try {
            if (!$user = $this->user()) {
                throw new ApplicationException(Lang::get('andresrangel.momentoshop::lang.customer.login_first'));
            }

            if ($user->is_activated) {
                throw new ApplicationException(Lang::get('andresrangel.momentoshop::lang.customer.already_active'));
            }

            Flash::success(Lang::get('andresrangel.momentoshop::lang.customer.activation_email_sent'));

            $this->sendActivationEmail($user);

        }
        catch (Exception $ex) {
            if (Request::ajax()) throw $ex;
            else Flash::error($ex->getMessage());
        }

        /*
         * Redirect
         */
        if ($redirect = $this->makeRedirection()) {
            return $redirect;
        }
    }

    /**
     * Sends the activation email to a user
     * @param  User $user
     * @return void
     */
    protected function sendActivationEmail($user)
    {
        $code = implode('!', [$user->id, $user->getActivationCode()]);
        $link = $this->currentPageUrl([
            $this->property('paramCode') => $code
        ]);

        $data = [
            'name' => $user->name,
            'link' => $link,
            'code' => $code
        ];

        Mail::send('andresrangel.momentoshop::mail.activate', $data, function($message) use ($user) {
            $message->to($user->email, $user->name);
        });
    }

}