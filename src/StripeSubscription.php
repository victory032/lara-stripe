<?php
namespace Code4mk\LaraStripe;

use Stripe\Customer;
use Stripe\Stripe;
use Stripe\Subscription;
use Config;

// https://stripe.com/docs/api/subscriptions/create

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

    public function get()
    {
        $this->createSubscriptionData['customer'] = $this->customer;
        $this->createSubscriptionData['items'] = [['plan' => $this->plan]];
        $subsData = array_merge($this->createSubscriptionData,$this->extra);

       try {
         Stripe::setApiKey($this->secretKey);
         $subs = Subscription::create($subsData);
         return $subs;
       } catch (\Exception $e) {
         return (object)['isError' => 'true','message'=> $e->getMessage()];
       }
    }
}
