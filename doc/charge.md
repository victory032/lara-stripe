
# Stripe charge

LaraStripeCharge has charge alias `LaraStripeCharge`.


# Methods

## setup()

setup method has `secret_key,public_key,currency` .

```php
LaraStripeCharge::setup([
    'secret_key' => '******',
    'public_key' => '******',
    'currency'   => 'usd'
])
```

## card()

card method parameter will be card token which come from stripe.js with request.

```php
LaraStripeCharge::card($token);
```

or direct card

```php
LaraStripeCharge::card([
    'number' => '4242424242424242'
    'exp_month' => '11',
    'exp_year' => '22',
    'cvc' => '222'
]);
```

## amount()

amount method set charge amount

```php
LaraStripeCharge::amount(121.50);
```

## metadata()

metadata methods array parameter you can declare here your `product id`, `customer id` or `similiar` data.

```php
LaraStripeCharge::metadata(['product_id'=>'p-121','purchase_id' => 'pur-12321']);
```

## description()

description method declare products/charge details.

```php
LaraStripeCharge::description('LaraStripeCharge Laravel Stripe payment');
```

## purchase()

purchase method create charge.

```php
LaraStripeCharge::purchase();
```

## get()

get method return some data . `type object`

```php
LaraStripeCharge::get();
```

## getAll()

get method return all datas. `type object`

```php
LaraStripeCharge::getAll();
```

# Full code

```php
$charge = LaraStripeCharge::setup([
    'secret_key' => '******',
    'public_key' => '******',
    'currency'   => 'usd'
])
->card($token)
->amount(121.50)
->metaData(['product_id'=>'p-121','purchase_id' => 'pur-12321'])
->description('LaraStripeCharge Laravel Stripe payment')
->purchase()
->get()
// or
// ->getAll()
# access response
// $charge->metadata->product_id
```

* response

```json
{
    "charge_id": "ch_1FWHbuAHZl11YnL9fU2BALTS",
    "amount": 121.50,
    "currency": "usd",
    "balance_transaction": "txn_1FVAcYAHZl11YnL9Ld0Fq3lp",
    "description": "LaraStripeCharge Laravel Stripe payment",
    "paid": true,
    "status": "succeeded",
    "metadata": {
        "product_id": "p-121",
        "purchase_id": "pur-12321"
    },
    "created": 1571463416
}
```

* access response

```php
# php file
$charge->metadata->product_id
```

# refund

* store charge id  when charge complete.

```php 
$refund = LaraStripeCharge::setup([
                    'secret_key'=>'sk_test_mBGoFuccDy2KCD4pobbaixKK00qUW0ghu1',
                  ])
                  ->refund('charge id');
return response()->json($refund);
```
