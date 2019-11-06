<?php
namespace Code4mk\LaraStripe;

/**
 * @author    @code4mk <hiremostafa@gmail.com>
 * @author    @kawsarsoft <with@kawsarsoft.com>
 * @copyright Kawsar Soft. (http://kawsarsoft.com)
 */

 use Illuminate\Support\Str;
 use Stripe\Stripe;
 use Stripe\Coupon;
 use Config;

/**
 * Coupon class
 * @source https://stripe.com/docs/api/coupons
 */
class StripeCoupon
{
    /**
     * Secret key
     * @var string
     */
    private $secretKey;

    /**
     * Currency when coupon amount is fixed
     * @var string
     */
    private $currency;

    /**
     * Coupon amount
     * @var integer|float
     */
    private $amount;

    /**
     * Coupon amount type
     * @var string per or fixed
     */
    private $type;

    /**
     * Coupon duration
     * @var string once
     */
    private $duration;

    /**
     * Coupon name & coupon id
     * @var string must be camel_case
     */
    private $name;

    /**
     * Coupon duration month
     * @var integer
     */
    private $durationMonth;

    /**
     * All coupon data for create
     * @var [type]
     */
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

    /**
     * Coupon amount
     * @param  int|float $amount
     * @param  string $type     [fixed,per]
     * @param  string $currency  fixed amount purpose
     * @source https://stripe.com/docs/api/coupons/object#coupon_object-amount_off
     * @return $this
     */
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

    /**
     * Coupon duration
     * @param  string  $type  [forever,once,repeating]
     * @param  integer $month
     * @source https://stripe.com/docs/api/coupons/create#create_coupon-duration
     * @return $this
     */
    public function duration($type,$month = 1)
    {
        //forever, once, or repeating.
      if($type === 'repeating') {
          $this->durationMonth = $month;
      }
      $this->duration = $type;
      return $this;
    }

    /**
     * Coupon name & id
     * @param  string $name snake_case
     * @return $this
     */
    public function name($name)
    {
      $this->name = Str::snake($name);
      return $this;
    }

    /**
     * Create coupon & retrieve data
     * @return  object
     */
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
         return (object)['isError' => 'true','message'=> $e->getMessage(),'stripe' => $e->getJsonBody()['error']];
       }
    }

    /**
     * Delete a coupon
     * @param  string $id coupon id
     * @return object
     */
    public function delete($id)
    {
        try {
          Stripe::setApiKey($this->secretKey);
          $coupon = Coupon::retrieve($id);
          $coupon->delete();
          return $coupon;
        } catch (\Exception $e) {
          return (object)['isError' => 'true','message'=> $e->getMessage(),'stripe' => $e->getJsonBody()['error']];
        }
    }
}
