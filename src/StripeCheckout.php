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
use Stripe\Checkout\Session;
use Config;

class StripeCheckout
{
    private $currency = 'usd';
    private $description = 'Stripe payment checkout by lara-stripe';
    private $products = [];
    private $secretKey;
    private $publicKey;
    private $successURI;
    private $cancelURI;
    private $referenceKey;

    public function __construct()
    {
        if(config::get('lara-stripe.driver') === 'config') {
            $this->currency = config::get('lara-stripe.currency');
            $this->secretKey = config::get('lara-stripe.secret_key');
            $this->publicKey = config::get('lara-stripe.public_key');
        }
    }

    public function setup($data)
    {
        $this->secretKey = $data['secret_key'];
        $this->publicKey = $data['public_key'];
        $this->currency = strtolower($data['currency']);
        return $this;
    }

    public function configure($data)
    {
        $this->successURI = $data['success_url'];
        $this->cancelURI = $data['cancel_url'];
        $this->referenceKey = $data['ref_key'];
        return $this;
    }

    public function publicKey()
    {
        return $this->publicKey;
    }

    public function products($data)
    {
        if (is_array($data) && sizeof($data) > 0) {
            $this->products = $data;
        }
        return $this;
    }

    public function getSession()
    {

        for($i=0;$i<sizeof($this->products);$i++){
            $this->products[$i]['currency'] = $this->currency;
            $this->products[$i]['amount'] = round($this->products[$i]['amount'],2) * 100;
            if (!isset($this->products[$i]['quantity'])) {
                $this->products[$i]['quantity'] = 1;
            }
        }

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
    }

    public function retrieve($sessionToken)
    {
        Stripe::setApiKey($this->secretKey);
        $infos = Session::retrieve($sessionToken);
        return $infos;
    }
}
