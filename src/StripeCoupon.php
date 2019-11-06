<?php
namespace Code4mk\LaraStripe;

use Stripe\Stripe;
use Stripe\Coupon;
use Config;

// https://stripe.com/docs/api/coupons

class StripeCoupon
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

    private $amount;
    private $type;
    private $duration;
    private $name;
    private $durationMonth;
    private $couponData;

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

    public function amount($amount,$type,$currency = 'usd')
    {
        if ($type === 'fixed') {
            $this->amount = round($amount,2) * 100;
            $this->type = 'fixed';
            $this->currency = $currency;
        } else {
            $this->amount = $amount;
            $this->type = 'per';
        }

      return $this;
    }

    public function duration($type,$month = 1)
    {
        //forever, once, or repeating.
      if($type === 'repeating') {
          $this->durationMonth = $month;
      }
      $this->duration = $type;
      return $this;
    }

    public function name($name)
    {
      $this->name = $name;
      return $this;
    }

    public function get()
    {
        if ($this->type === 'per') {
            $this->couponData['percent_off'] = $this->amount;
        } else {
            $this->couponData['amount_off'] = $this->amount;
            $this->couponData['currency'] = $this->currency;
        }

        if ($this->name) {
            $this->couponData['name'] = $this->name;
            $this->couponData['id'] = $this->name;
        }
        if ($this->duration === 'forever' || $this->duration === 'once') {
            $this->couponData['duration'] = $this->duration;
        } else {
            $this->couponData['duration'] = $this->duration;
            $this->couponData['duration_in_months'] = $this->durationMonth;
        }

       try {
         Stripe::setApiKey($this->secretKey);
         $coupon = Coupon::create($this->couponData);
         return $coupon;
       } catch (\Exception $e) {
         return (object)['isError' => 'true','message'=> $e->getMessage()];
       }
    }
    
    public function delete($id)
    {
        try {
          Stripe::setApiKey($this->secretKey);
          $coupon = Coupon::retrieve($id);
          $coupon->delete();
          return $coupon;
        } catch (\Exception $e) {
          return (object)['isError' => 'true','message'=> $e->getMessage()];
        }
    }


}
