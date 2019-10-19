<?php
namespace Code4mk\LaraStripe;

/**
 * @author    @code4mk <hiremostafa@gmail.com>
 * @author    @0devco <with@0dev.co>
 * @copyright 0dev.co (https://0dev.co)
 */

use Stripe\Stripe;
use Stripe\Customer;

use Config;

class StripeCustomer
{

    private $currency = 'usd';
    private $secretKey;
    private $publicKey;
    private $customer;
    private $metadata = [];
    private $createCustomerData = [];

    /* $allOutput object */
    private $allOutput;

    /* $error object */
    private $error;

    public function __construct()
    {
        if(config::get('lara-stripe.driver') === 'config') {
            $this->currency = config::get('lara-stripe.currency');
            $this->secretKey = config::get('lara-stripe.secret_key');
            $this->publicKey = config::get('lara-stripe.public_key');
        }
    }

    public function setup($data)
    {
        $this->secretKey = $data['secret_key'];
        $this->publicKey = $data['public_key'];
        $this->currency = strtolower($data['currency']);
        return $this;
    }


    public function create($datas)
    {
        foreach ($datas as $key => $data) {
            $this->createCustomerData[$key] = $data;
        }

        if (!isset($this->createCustomerData['metadata'])) {
            $this->createCustomerData['metadata'] = $this->metadata;
        }
        return $this;
    }

    public function metadata($data)
    {
        $this->metadata = $data;
        return $this;
    }

    public function get()
    {
        Stripe::setApiKey($this->secretKey);
        $this->customer = Customer::create($this->createCustomerData);
        return $this->customer;
    }

    public function retrieve($id)
    {
        try{
            Stripe::setApiKey($this->secretKey);
            return Customer::retrieve($id);
        } catch (\Exception $e) {

        }

    }

    public function lists()
    {
        Stripe::setApiKey($this->secretKey);
        return Customer::all();
    }



}
