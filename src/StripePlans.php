<?php
namespace Code4mk\LaraStripe;

/**
 * @author    @code4mk <hiremostafa@gmail.com>
 * @author    @kawsarsoft <with@kawsarsoft.com>
 * @copyright Kawsar Soft. (http://kawsarsoft.com)
 */

use Stripe\Customer;
use Stripe\Stripe;
use Stripe\Plan;
use Stripe\Product;
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

    private $extra = [];

    private $trial;

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
      $this->amount = round($amount,2) * 100;
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

    public function extra ($data)
    {
        $this->extra = $data;
        return $this;
    }

    public function trial($day)
    {
        $this->trial = $day;
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
           'product' => $this->product,
           'trial_period_days' => $this->trial,
           // $this->extra
         ]);
         return $plan;
       } catch (\Exception $e) {
         return (object)['isError' => 'true','message'=> $e->getMessage()];
       }
    }

    public function retrieve($id)
    {
        try {
            Stripe::setApiKey($this->secretKey);
            $plan = Plan::retrieve($id);
            return $plan;
        } catch (\Exception $e) {
            return (object)['isError' => 'true','message'=> $e->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            Stripe::setApiKey($this->secretKey);
            $plan = Plan::retrieve($id);
            $product = $plan->product;
            $plan->delete();

            $getProduct = Product::retrieve($product);
            $getProduct->delete();

            return $plan;
        } catch (\Exception $e) {
            return (object)['isError' => 'true','message'=> $e->getMessage()];
        }
    }

    public function active($id)
    {
        try {
            Stripe::setApiKey($this->secretKey);
            $plan = Plan::update(
                $id,
                ['active' => true]
            );

            return $plan;
        } catch (\Exception $e) {
            return (object)['isError' => 'true','message'=> $e->getMessage()];
        }
    }

    public function deactive($id)
    {
        try {
            Stripe::setApiKey($this->secretKey);
            $plan = Plan::update(
                $id,
                ['active' => false]
            );
            return $plan;
        } catch (\Exception $e) {
            return (object)['isError' => 'true','message'=> $e->getMessage()];
        }
    }
}
