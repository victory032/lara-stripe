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

class StripePaySession
{

    private $currency = 'usd';
    private $description = 'Stripe payment checkout by lara-stripe';
    private $amount;
    private $secretKey;
    private $successURI;
    private $cancelURI;
    private $referenceID;

    public function __construct()
    {
        if(config::get('lara-stripe.driver') === 'config') {
            $this->currency = config::get('lara-stripe.currency');
            $this->secretKey = config::get('lara-stripe.secret_key');
        }
    }

    public function setup($data)
    {
        $this->secretKey = $data['secret'];
        $this->currency = strtolower($data['currency']);
        return $this;
    }
    public function configure($data)
    {
        $this->successURI = $data['success_url'];
        $this->cancelURI = $data['cancel_url'];
        $this->referenceID = $data['ref_id'];
        return $this;
    }

    public function description($text)
    {
        $this->description = $text;
        return $this;
    }

    public function amount($amount)
    {
        $this->amount = round($amount,2) * 100;
        return $this;
    }

    public function getSession()
    {
        Stripe::setApiKey($this->secretKey);
        $session = Session::create([
          'payment_method_types' => ['card'],
          'line_items' => [[
            'name' => 'T-shirt',
            'description' => $this->description,
            'images' => ['https://example.com/t-shirt.png'],
            'amount' => $this->amount,
            'currency' => 'usd',
            'quantity' => 1,
          ]],
          'success_url' => $this->successURI,
          'cancel_url' => $this->cancelURI,
          'client_reference_id' => $this->referenceID,

        ]);
        return $session->id;
    }

    public function retrieve($sessionToken)
    {
        Stripe::setApiKey($this->secretKey);
        $infos = Session::retrieve($sessionToken);
        return $infos;
    }


}
