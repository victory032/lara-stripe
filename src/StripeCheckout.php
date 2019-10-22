<?php
namespace Code4mk\LaraStripe;

/**
 * @author    @code4mk <hiremostafa@gmail.com>
 * @author    @0devco <with@0dev.co>
 * @copyright 0dev.co (https://0dev.co)
 */

use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Refund;
use Config;

class StripeCheckout
{
    /**
     * Checkout Currency
     * @var string length 3 and lowercase
     */
    private $currency = 'usd';

    /**
     * Checkout description
     * @var string
     */
    private $description = 'Stripe payment checkout by lara-stripe';

    /**
     * Checkout products data
     * @var array
     */
    private $products = [];

    /**
     * Secret key
     * @var string
     */
    private $secretKey;

    /**
     * Public key
     * @var string
     */
    private $publicKey;

    /**
     * Checkout success url
     * @var string
     */
    private $successURI;

    /**
     * Checkout cancel url
     * @var string
     */
    private $cancelURI;

    /**
     * Checkout ref ex: product id , payment id, card id similar.
     * @var string
     */
    private $referenceKey;

    public function __construct()
    {
        if(config::get('lara-stripe.driver') === 'config') {
            $this->currency = config::get('lara-stripe.currency');
            $this->secretKey = config::get('lara-stripe.secret_key');
            $this->publicKey = config::get('lara-stripe.public_key');
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
     * Configure success url , cancel url &  ref
     *
     * @param array $data
     * @return $this
     */
    public function configure($data)
    {
        if (isset($data['success_url'])) {
            $this->successURI = $data['success_url'];
        }
        if (isset($data['cancel_url'])) {
            $this->cancelURI = $data['cancel_url'];
        }
        if (isset($data['ref_key'])) {
            $this->referenceKey = $data['ref_key'];
        }
        return $this;
    }

    /**
     * Retrieve public key
     *
     * @return $this
     */
    public function publicKey()
    {
        return $this->publicKey;
    }

    /**
     * Set products
     *
     * @param array $data
     * @return $this
     */
    public function products($data)
    {
        if (is_array($data) && sizeof($data) > 0) {
            $this->products = $data;
        }
        return $this;
    }
    /**
     * Get session id and public key
     *
     * @return object sid and pkey
     */
    public function getSession()
    {
        for($i=0;$i<sizeof($this->products);$i++){
            $this->products[$i]['currency'] = $this->currency;
            $this->products[$i]['amount'] = round($this->products[$i]['amount'],2) * 100;
            if (!isset($this->products[$i]['quantity'])) {
                $this->products[$i]['quantity'] = 1;
            }
        }

        try {
            Stripe::setApiKey($this->secretKey);
            if (is_array($this->products) && sizeof($this->products) > 0) {
                $session = Session::create([
                  'payment_method_types' => ['card'],
                  'line_items' => $this->products,
                  'success_url' => $this->successURI,
                  'cancel_url' => $this->cancelURI,
                  'client_reference_id' => $this->referenceKey,

                ]);
                $output =  [
                    'sid' => $session->id,
                    'pkey' => $this->publicKey
                ];
                return (object) $output;
            }
        } catch (\Exception $e) {
            return (object)['isError' => 'true','message'=> $e->getMessage()];
        }
    }

    /**
     * Retrieve session (checkout).
     *
     * @param string $sessionToken
     * @return object $infos
     */
    public function retrieve($sessionToken)
    {
        try {
            Stripe::setApiKey($this->secretKey);
            $infos = Session::retrieve($sessionToken);
            return $infos;
        } catch (\Exception $e) {
            return (object)['isError' => 'true','message'=> $e->getMessage()];
        }
    }

    /**
     * Checkout refund
     * Store payment_intent when checkout success in DB.
     * @param  string $payment_intent get from database
     * @return object
     */
    public function refund($payment_intent)
    {
        try {
            Stripe::setApiKey($this->secretKey);
            $intent = \Stripe\PaymentIntent::retrieve($payment_intent);
            $re = \Stripe\Refund::create([
              'charge' => $intent->charges->data[0]->id
            ]);
            return $re;
        } catch (\Exception $e) {
            return (object)['isError' => 'true','message'=> $e->getMessage()];
        }
    }
}
