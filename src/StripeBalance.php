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
use Stripe\Balance;

use Config;

class StripeBalance
{

    private $currency = 'usd';
    private $secretKey;
    private $publicKey;

    /* $allOutput object */
    private $allOutput;

    /* $error object */
    private $error;

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

    public function get()
    {
        Stripe::setApiKey($this->secretKey);
        return Balance::retrieve();
    }



}
