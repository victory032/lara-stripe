<?php
namespace Code4mk\LaraStripe;

/**
 * @author    @code4mk <hiremostafa@gmail.com>
 * @author    @kawsarsoft <with@kawsarsoft.com>
 * @copyright Kawsar Soft. (http://kawsarsoft.com)
 */

use Stripe\Product;
use Stripe\Stripe;
use Stripe\Plan;
use Config;

/**
 * Plan class.
 * @source https://stripe.com/docs/api/plans/create
 */
class StripePlans
{
    /**
     * Secret key
     * @var string
     */
    private $secretKey;

    /**
     * Set currency for plan.
     * @var string
     */
    private $currency;

    /**
     * plan billing recurring interval
     * @var string
     */
    private $interval;

    /**
     * Plan price.
     * @var integer|float
     */
    private $amount;

    /**
     * Plan products
     * @var array
     */
    private $product = [];

    /**
     * Plan extra properties
     * @var array
     */
    private $extra = [];

    /**
     * Trail day
     * @var integer
     */
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

    /**
     * Plan products
     * @param  aray $data
     * @source https://stripe.com/docs/api/plans/create#create_plan-product
     * @return $this
     */
    public function product($data)
    {
      $this->product = $data;
      return $this;
    }

    /**
     * Plan price
     * @param  int|float $amount
     * @return $this
     */
    public function amount($amount)
    {
      $this->amount = round($amount,2) * 100;
      return $this;
    }

    /**
     * Plan recurring interval
     * @param  string $type [day,week,month,year]
     * @return $this
     */
    public function interval($type)
    {
      $this->interval = $type;
      return $this;
    }

    /**
     * Plan currency
     * @param   $currency
     * @return $this
     */
    public function currency($currency)
    {
      $this->currency = $currency;
      return $this;
    }

    /**
     * Plan extra properties
     * @param  array $data associate array
     * @return $this
     */
    public function extra ($data)
    {
        $this->extra = $data;
        return $this;
    }

    /**
     * Plan trial time (day).
     * @param  integer $day
     * @return $this
     */
    public function trial($day)
    {
        $this->trial = $day;
        return $this;
    }

    /**
     * Create plan & retrive data.
     * @return object
     */
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
           $this->extra
         ]);
         return $plan;
       } catch (\Exception $e) {
         return (object)['isError' => 'true','message'=> $e->getMessage()];
       }
    }

    /**
     * Retrieve a plan with $id.
     * @param  string $id
     * @return object
     */
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

    /**
     * Active a plan
     * @param  string $id
     * @return object
     */
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

    /**
     * Deactive a plan.
     * @param  string $id
     * @return $this
     */
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

    /**
     * Delete a plan and same time delete product.
     * @param  string $id
     * @return object
     */
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
}
