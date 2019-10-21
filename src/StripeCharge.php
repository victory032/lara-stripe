<?php
namespace Code4mk\LaraStripe;

/**
 * @author    @code4mk <hiremostafa@gmail.com>
 * @author    @0devco <with@0dev.co>
 * @copyright 0dev.co (https://0dev.co)
 */

use Stripe\Stripe;
use Stripe\Token;
use Stripe\Charge;
use Stripe\Refund;
use Config;

class StripeCharge
{
    private $card = [];
    private $token = '';
    private $metadata = [];
    private $currency = 'usd';
    private $description = 'Stripe charge by lara-stripe';
    private $amount;
    private $secretKey;
    private $publicKey;
    private $payOption;

    /* $allOutput object */
    private $allOutput;

    /* $error object */
    private $error;

    public function __construct()
    {
        if(config::get('lara-stripe.driver') === 'config') {
            $this->currency = config::get('lara-stripe.currency');
            $this->secretKey = config::get('lara-stripe.secret_key');
            $this->secretKey = config::get('lara-stripe.public_key');
        }
    }
    /**
     * Set credentials, secret and public key
     *
     * Set stripe currency
     * @param array $data
     * @return $this
     */
    public function setup($data)
    {
        if (isset($data['secret_key'])) {
            $this->secretKey = $data['secret_key'];
        }
        if (isset($data['public_key'])) {
            $this->publicKey = $data['public_key'];
        }
        if (isset($data['currency'])) {
            $this->currency = strtolower($data['currency']);
        }

        return $this;
    }

    /**
     * Retrive public key
     *
     * @return string
     */
    public function publicKey()
    {
        return $this->publicKey;
    }
    /**
     * Set card info number,exp month,exp year and cvc.
     *
     * when pass string that is card token which generate by stripe.js
     * @param array|string $data
     * @return $this
     */
    public function card($data)
    {
        if (is_array($data)) {
            $this->card['number'] = $data['number'];
            $this->card['exp_month'] = $data['exp_month'];
            $this->card['exp_year'] = $data['exp_year'];
            $this->card['cvc'] = $data['cvc'];
        } else {
            $this->token = $data;
        }
        $this->payOption = 'source';
        return $this;
    }

    /**
     * set customer id
     * this method need for future charge as subcription
     * @param string $data
     * @return $this
     */
    public function customer($data)
    {
        $this->token = $data;
        $this->payOption = 'customer';

        return $this;
    }

    /**
     * set charge amount
     *
     * @param float|int|double $amount
     * @return $this
     */
    public function amount($amount)
    {
        $this->amount = round($amount,2) * 100 ;
        return $this;
    }

    /**
     * set some data which will be need for charge/payment
     *
     * ex: tnx_id,product_id,order_id or simliar
     *
     * @param array $data
     * @return $this
     */
    public function metadata($data)
    {
        $this->metadata = $data;
        return $this;
    }

    /**
     * set charge description
     *
     * @param string $text
     * @return $this
     */
    public function description($text)
    {
        $this->description = $text;
        return $this;
    }

    /**
     * create charge
     *
     * @return $this
     */
    public function purchase()
    {
        try {
            Stripe::setApiKey($this->secretKey);
        } catch (\Exception $e) {
            foreach ((array)$e as $key => $value) {
                $this->error[substr($key, 3)] = $value;
            }
            return $this;
        }

        if (sizeof($this->card) === 4 ) {
            try {
                $createToken = Token::create([
                    'card' => $this->card
                ]);
                $this->token = $createToken->id;
                // return $this->token;
            } catch (\Exception $e) {
                foreach ((array)$e as $key => $value) {
                    $this->error[substr($key, 3)] = $value;
                }
                return $this;
            }
        }

        try {
            $charge = Charge::create([
                'amount' => $this->amount,
                'currency' => $this->currency,
                $this->payOption => $this->token,
                'metadata' => $this->metadata,
                'description' => $this->description
            ]);
            $this->allOutput = $charge;
            return $this;
        } catch (\Exception $e) {
            foreach ((array)$e as $key => $value) {
                $this->error[substr($key, 3)] = $value;
            }
            return $this;
        }
    }

    /**
     * Retrieve all charge's data
     *
     * @return object
     */
    public function getAll()
    {
        if($this->error){
            return  (object)['isError' => 'true','stripeError'=>$this->error];
        }

        if ($this->allOutput !== '') {
            return $this->allOutput;
        }
    }

    /**
     * Retrieve charge's specific data as object
     *
     * @return object
     */
    public function get()
    {
        if($this->error){
            return  (object)['isError' => 'true','stripeError'=>$this->error];
        }
        if ($this->allOutput !== '') {
            $output = [
                'amount' => $this->allOutput->amount / 100,
                'currency' => $this->allOutput->currency,
                'balance_transaction' => $this->allOutput->balance_transaction,
                'description' => $this->allOutput->description,
                'paid' => $this->allOutput->paid,
                'status' => $this->allOutput->status,
                'metadata' => $this->allOutput->metadata,
                'created' => $this->allOutput->created,
            ];

            return (object) $output;
      }
    }

    /**
     * Charge refund with charge id.
     * Store charge id in database when create charge.
     *
     * @param string $chargeID
     * @return string.
     */
    public function refund($chargeID)
    {
        try {
            Stripe::setApiKey($this->secretKey);
            Refund::create([
                'charge' => $chargeID,
            ]);
            return 'refund';
        } catch (\Exception $e) {
            foreach ((array)$e as $key => $value) {
                $this->error[substr($key, 3)] = $value;
            }
            return (object)['isError' => 'true','stripeError'=>$this->error];
        }

    }
}
