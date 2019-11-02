<?php
namespace Code4mk\LaraStripe;

use Stripe\Customer;
use Stripe\Stripe;
use Stripe\Plan;
use Config;

// https://stripe.com/docs/api/plans/create

class StripePlans
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
    private $currency;

    private $interval;

    private $amount;

    private $product = [];

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

    public function product($data)
    {
      $this->product = $data;
      return $this;
    }

    public function amount($amount)
    {
      $this->amount = $amount;
      return $this;
    }

    public function interval($type)
    {
      $this->interval = $type;
      return $this;
    }

    public function currency($currency)
    {
      $this->currency = $currency;
      return $this;
    }

    public function get()
    {
       try {
         Stripe::setApiKey($this->secretKey);
         $plan = Plan::create([
           'amount' => $this->amount,
           'currency' => $this->currency,
           'interval' => $this->interval,
           'product' => $this->product
         ]);
         return $plan;
       } catch (\Exception $e) {
         return (object)['isError' => 'true','message'=> $e->getMessage()];
       }
    }
}
