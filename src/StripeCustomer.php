<?php
namespace Code4mk\LaraStripe;

/**
 * @author    @code4mk <hiremostafa@gmail.com>
 * @author    @kawsarsoft <with@kawsarsoft.com>
 * @copyright Kawsar Soft. (http://kawsarsoft.com)
 */

// https://stripe.com/docs/saving-cards
// https://stripe.com/docs/api/customers/list


use Stripe\Customer;
use Stripe\Stripe;
use Config;

class StripeCustomer
{
    /**
     * Secret key
     * @var string
     */
    private $secretKey;

    /**
     * Customer all data after create
     * @var object
     */
    private $customer;

    /**
     * Customer metadata
     * @var array
     */
    private $metadata = [];

    /**
     * Customer  all data
     * @var array
     */
    private $createCustomerData = [];

    public function __construct()
    {
        if(config::get('lara-stripe.driver') === 'config') {
            $this->secretKey = config::get('lara-stripe.secret_key');
        }
    }
    /**
     * Set secret key
     * @param  string $data
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
     * set customer source (card) , email
     * https://stripe.com/docs/api/customers/create
     *
     * @param  array $datas
     * @return $this
     */
    public function create($datas)
    {
        foreach ($datas as $key => $data) {
            $this->createCustomerData[$key] = $data;
        }
        return $this;
    }

    /**
     * Set customer metadata
     * @param  array $data
     * @return $this
     */
    public function metadata($data)
    {
        $this->metadata = $data;
        return $this;
    }

    /**
     * Create customer and return customer data
     * @return object
     */
    public function get()
    {
        if (!isset($this->createCustomerData['metadata'])) {
            $this->createCustomerData['metadata'] = $this->metadata;
        }

        try {
            Stripe::setApiKey($this->secretKey);
            $this->customer = Customer::create($this->createCustomerData);
            return $this->customer;
        } catch (\Exception $e) {
            return (object)['isError' => 'true','message'=> $e->getMessage()];
        }

    }

    /**
     * Retrive customer with $cusIdid.
     * @param  string $cusIdid
     * @return object
     */
    public function retrieve($cusId)
    {
        try{
            Stripe::setApiKey($this->secretKey);
            return Customer::retrieve($cusId);
        } catch (\Exception $e) {
            return (object)['isError' => 'true','message'=> $e->getMessage()];
        }

    }

    /**
     * Change customer card.
     * @param  string $cusId     [description]
     * @param  string $cardToken create with stripe.js or manually create.
     * @return object
     */
    public function changeCard($cusId,$cardToken)
    {
        try {
            Stripe::setApiKey($this->secretKey);
            $cus = Customer::update($cusId,[
                'source' => $cardToken
            ]);
            return $cus;
        } catch (\Exception $e) {
            return (object)['isError' => 'true','message'=> $e->getMessage()];
        }
    }
}
