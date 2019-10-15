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
    private $metaData = [];
    private $currency = 'usd';
    private $description = 'Stripe charge by lara-stripe';
    private $amount;
    private $secretKey;
    private $output;
    private $allOutput;
    private $error;

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

        return $this;
    }

    public function amount($amount)
    {
        $this->amount = round($amount,2) * 100 ;
        return $this;
    }

    public function metaData($data)
    {
        $this->metaData = $data;
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
                'source' => $this->token,
                'metadata' => $this->metaData,
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
            $this->output['amount'] = $this->allOutput['amount'] / 100;
            $this->output['currency'] = $this->allOutput['currency'];
            $this->output['balance_transaction'] = $this->allOutput['balance_transaction'];
            $this->output['description'] = $this->allOutput['description'];
            $this->output['paid'] = $this->allOutput['paid'];
            $this->output['meta_data'] = $this->allOutput['metadata'];
            $this->output['created'] = $this->allOutput['created'];

            return $this->output;
      }
    }
}
