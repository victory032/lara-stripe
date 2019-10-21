<?php
namespace Code4mk\LaraStripe;

/**
 * @author    @code4mk <hiremostafa@gmail.com>
 * @author    @0devco <with@0dev.co>
 * @copyright 0dev.co (https://0dev.co)
 */

// https://stripe.com/docs/saving-cards
// https://stripe.com/docs/api/customers/list
//
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
    /**
     * Set credentials, secret  key
     *
     * @param array $data
     * @return $this
     */
    public function setup($data)
    {
        if (isset($data['secret_key'])) {
            $this->secretKey = $data['secret_key'];
        }
        return $this;
    }

    /**
     * Customer data
     *
     * @param  array $datas customer data
     * @return $this
     */
    public function create($datas)
    {
        if (is_array($datas)) {
            foreach ($datas as $key => $data) {
                $this->createCustomerData[$key] = $data;
            }
        }
        return $this;
    }
    /**
     * Set customer metadata
     *
     * @param  array $data customer metadata
     * @return $this
     */
    public function metadata($data)
    {
        $this->metadata = $data;
        return $this;
    }

    /**
     * create customer and retrieve customer id
     * @return string
     */
    public function get()
    {

        // if (!isset($this->createCustomerData['metadata'])) {
        //     $this->createCustomerData['metadata'] = $this->metadata;
        // }

        try {
            Stripe::setApiKey($this->secretKey);
            $this->customer = Customer::create($this->createCustomerData);
            return $this->customer;
        } catch (\Exception $e) {
            return (object) ['isError' => 'true', 'message'=> $e->getMessage()];
        }

    }

    /**
     * retrieve customer with $id
     * @param  string $id customer id
     * @return [type]     [description]
     */
    public function retrieve($id)
    {
        try{
            Stripe::setApiKey($this->secretKey);
            $cus = Customer::retrieve($id);
            return $cus;
        } catch (\Exception $e) {
            return (object) ['isError' => 'true', 'message'=> $e->getMessage()];
        }

    }

    /**
     * Change customer credit card
     * @param  string $cusId     customer id
     * @param  string $cardToken card token
     * @return string|object
     */
    public function changeCard($cusId,$cardToken)
    {
        try {
            Stripe::setApiKey($this->secretKey);
            Customer::update($cusId,[
                'source' => $cardToken
            ]);
            return 'Customer card changed successfuly';
        } catch (\Exception $e) {
            return (object) ['isError' => 'true', 'message'=> $e->getMessage()];
        }
    }
}
