<?php
namespace Code4mk\LaraStripe\Lib;

/**
 * @author    @code4mk <hiremostafa@gmail.com>
 * @author    @kawsarsoft <with@kawsarsoft.com>
 * @copyright Kawsar Soft. (http://kawsarsoft.com)
 */

use Stripe\Subscription;
use Stripe\Stripe;
use Config;

/**
 * Subscription class
 *
 * @source  https://stripe.com/docs/api/subscriptions/create
 */
class StripeSubscription
{
    /**
     * Secret key.
     * @var string
     */
    private $secretKey;

    /**
     * Subscription Customer
     * @var object
     */
    private $customer;

    /**
     * All data which properties of create the subscription.
     * @var array
     */
    private $createSubscriptionData = [];

    /**
     * Plan which will be subcription.
     * @var string
     */
    private $plan;

    /**
     * Add others properties of create the subscription.
     * @var array
     */
    private $extra = [];

    /**
     * Trial default from plan.
     * @var boolean
     */
    private $trialPlan = false;

    /**
     * How many trial day for subscription.
     * This will be override the plan trial day.
     * @var integer
     */
    private $trial;

    /**
     * Card source
     * @var string
     */
    private $source;

    /**
     * Subcription with coupon.
     * @var string
     */
    private $coupon;

    public function __construct()
    {
        if(config::get('lara-stripe.driver') === 'config') {
            $this->secretKey = config::get('lara-stripe.secret_key');
        }
    }
    /**
     * Set secret key.
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
     * set customer id which create by customer alias.
     * @param  string $id customer id
     * @return $this
     */
    public function customer($id)
    {
      $this->customer = $id;
      return $this;
    }

    /**
     * Plan id which generate by LaraStripePlan  alias.
     * @param  string $id plan id
     * @return $this
     */
    public function plan($id)
    {
      $this->plan = $id;
      return $this;
    }

    /**
     * stripe subscription others properties which not declare this package.
     * @param  array  $data
     * @return $this
     */
    public function extra($data = [])
    {
      if (is_array($data)) {
        $this->extra = $data;
      }
      return $this;
    }

    /**
     * Default trial time from plan.
     * @return $this
     */
    public function trialPlan()
    {
        $this->trialPlan = true;
        return $this;
    }

    /**
     * Set subscription trial.
     * override the plan trial day.
     * @param  integer $day
     * @return $this
     */
    public function trial($day) {
        $this->trial = $day;
        return $this;
    }

    /**
     * set customer card source
     * @param  string $code
     * @return $this
     */
    public function source($code) {
        $this->source = $code;
        return $this;
    }

    /**
     * Coupon apply
     * @param  string $code
     * @return $this
     */
    public function coupon($code) {
        $this->coupon = $code;
        return $this;
    }

    /**
     * Create & retreive all data.
     * @return object
     */
    public function get()
    {
        $this->createSubscriptionData['customer'] = $this->customer;
        $this->createSubscriptionData['items'] = [['plan' => $this->plan]];
        if ($this->trialPlan) {
            $this->createSubscriptionData['trial_from_plan'] = true;
        }
        if ($this->trial) {
            $this->createSubscriptionData['trial_period_days'] = $this->trial;
        }
        if ($this->coupon) {
            $this->createSubscriptionData['coupon'] = $this->coupon;
        }
        if ($this->source) {
            $this->createSubscriptionData['default_source'] = $this->source;
        }
        $subsData = array_merge($this->createSubscriptionData,$this->extra);

       try {
         Stripe::setApiKey($this->secretKey);
         $subs = Subscription::create($subsData);
         return $subs;
       } catch (\Exception $e) {
         return (object)['isError' => 'true','message'=> $e->getMessage()];
       }
    }

    /**
     * Retrieve a subscription with id
     * @param  string $id
     * @return object
     */
    public function retrieve($id)
    {
        try {
          Stripe::setApiKey($this->secretKey);
          $subs = Subscription::retrieve($id);
          return $subs;
        } catch (\Exception $e) {
          return (object)['isError' => 'true','message'=> $e->getMessage()];
        }
    }

    /**
     * Cancel a subscription.
     * @param  string $id
     * @return object
     */
    public function cancel($id)
    {
        try {
          Stripe::setApiKey($this->secretKey);
          $subs = Subscription::retrieve($id);
          $subs->cancel();
          return $subs;
        } catch (\Exception $e) {
          return (object)['isError' => 'true','message'=> $e->getMessage()];
        }
    }
}
