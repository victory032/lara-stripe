<?php
namespace Code4mk\LaraStripe;

/**
 * @author    @code4mk <hiremostafa@gmail.com>
 * @author    @kawsarsoft <with@kawsarsoft.com>
 * @copyright Kawsar Soft. (http://kawsarsoft.com)
 */

use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Product;
use Stripe\Stripe;
use Stripe\SKU;
use Config;

class StripeRequestPayment
{
    /**
     * secret key
     * @var string
     */

    private $secretKey;
    private $title;
    private $description;
    private $amount;
    private $currency;

    public function __construct()
    {
        if(config::get('lara-stripe.driver') === 'config') {
            $this->secretKey = config::get('lara-stripe.secret_key');
        }
    }

    /**
     * Set secret key
     * @param  array $data
     * @return $this
     */
    public function setup($data)
    {
        if ($data['secret_key']) {
            $this->secretKey = $data['secret_key'];
        }
        return $this;
    }

    public function title($title)
    {
        $this->title = $title;
        return $this;
    }

    public function description($des)
    {
        $this->description = $des;
        return $this;
    }

    public function amount($amount,$currency='usd')
    {
        $this->amount = round($amount,2) * 100;
        $this->currency = $currency;
        return $this;
    }

    /**
     * Get balance
     * @return object
     */
    public function get()
    {
        try {
            Stripe::setApiKey($this->secretKey);
            $product = Product::create([
              'name' => $this->title,
              'type' => 'good',
              'description' => $this->description,
              'attributes' => ['name'],
            ]);

            $skus = SKU::create([
              'attributes' => [
                'name' => $this->title
              ],
              'price' => $this->amount,
              'currency' => $this->currency,
              'inventory' => [
                  'type' => 'infinite',
              ],
              'product' => $product->id,
            ]);

            $output = (object) [
                'skus' => $skus->id,
                'req_id' => $product->id
            ];
            return $output;

        } catch (\Exception $e) {
            return (object)['isError' => 'true','message'=> $e->getMessage()];
        }
    }

    public function status($sessionToken)
    {
        try {
            Stripe::setApiKey($this->secretKey);
            $session = Session::retrieve($sessionToken);
            $skus = $session->display_items[0]->sku->id;
            $req_id = $session->display_items[0]->sku->product;
            $pi = PaymentIntent::retrieve(
              $session->payment_intent
            );
            if ($pi->status === 'succeeded') {
                return (object) [
                    'status' => $pi->status,
                    'skus'   => $skus,
                    'req_id' => $req_id
                ];
            }
            return (object) [
                'status' => $pi->status,
                'skus'   => $skus,
                'req_id' => $req_id
            ];

        } catch (\Exception $e) {
            return (object)['isError' => 'true','message'=> $e->getMessage()];
        }
    }
}
