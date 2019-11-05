<?php
namespace Code4mk\LaraStripe;

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
     * Secret key
     * @var string
     */
    private $secretKey;
    /**
     * Customer all data after create
     * @var object
     */
    private $customer;

    private $createSubscriptionData = [];

    private $plan;

    private $extra = [];
    // trail day from plan
    private $trialPlan = false;

    private $trial;
    private $coupon;

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

    public function customer($id)
    {
      $this->customer = $id;
      return $this;
    }

    public function plan($id)
    {
      $this->plan = $id;
      return $this;
    }

    public function extra($data = [])
    {
      if (is_array($data)) {
        $this->extra = $data;
      }
      return $this;
    }

    public function trialPlan()
    {
        $this->trialPlan = true;
        return $this;
    }

    public function trial($day) {
        $this->trial = $day;
        return $this;
    }

    public function coupon($code) {
        $this->coupon = $code;
        return $this;
    }

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
        $subsData = array_merge($this->createSubscriptionData,$this->extra);

       try {
         Stripe::setApiKey($this->secretKey);
         $subs = Subscription::create($subsData);
         return $subs;
       } catch (\Exception $e) {
         return (object)['isError' => 'true','message'=> $e->getMessage()];
       }
    }

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
