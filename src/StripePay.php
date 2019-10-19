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
use Config;

class StripePay
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

    public function setup($data)
    {
        $this->secretKey = $data['secret_key'];
        $this->publicKey = $data['public_key'];
        $this->currency = strtolower($data['currency']);
        return $this;
    }

    public function publicKey()
    {
        return $this->publicKey;
    }

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

    public function customer($data)
    {
        $this->token = $data;
        $this->payOption = 'customer';

        return $this;
    }

    public function amount($amount)
    {
        $this->amount = round($amount,2) * 100 ;
        return $this;
    }

    public function metadata($data)
    {
        $this->metadata = $data;
        return $this;
    }

    public function description($text)
    {
        $this->description = $text;
        return $this;
    }

    public function purchase()
    {
        try {
            Stripe::setApiKey($this->secretKey);
        } catch (\Exception $e) {
            $this->error = $e;
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
                $this->error = $e;
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
            $this->error = $e;
            return $this;
        }
    }

    public function getAll()
    {
        if($this->error){
            return [
                'pay_status' => 'error',
                'code' => $this->error->jsonBody{'error'}{'code'},
                'message' => $this->error->jsonBody{'error'}{'message'},
                // 'error' => $this->error,
            ];
        }

        if ($this->allOutput !== '') {
            return $this->allOutput;
        }
    }

    /**
     * Return charge specific datas as object
     *
     * @return object
     */
    public function get()
    {
        if($this->error){
            return [
                'pay_status' => 'error',
                'code' => $this->error->jsonBody{'error'}{'code'},
                'message' => $this->error->jsonBody{'error'}{'message'},
                // 'error' => $this->error,
            ];
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
}
