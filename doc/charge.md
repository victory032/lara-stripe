
# Stripe charge

LaraStripe has charge alias `LaraStripe`.


# Methods

## setup()

setup method has `secret_key,public_key,currency` .

```php
LaraStripe::setup([
    'secret_key' => '******',
    'public_key' => '******',
    'currency'   => 'usd'
])
```

## card()

card method parameter will be card token which come from stripe js with request.

```php
LaraStripe::card($token);
```

## amount()

amount method parameter take amount.

```php
LaraStripe::amount(121.50);
```

## metadata()

metadata methods array parameter you can declare here your `product id`, `customer id` or `similiar` datas.

```php
LaraStripe::metaData(['product_id'=>'p-121','purchase_id' => 'pur-12321']);
```

## description()

description method declare products/charge details.

```php
LaraStripe::description('LaraStripe Laravel Stripe payment');
```

## purchase()

purchase method create charge.

```php
LaraStripe::purchase();
```

## get()

get method return some data . `type object`

```php
LaraStripe::get();
```

## getAll()

get method return all datas. `type object`

```php
LaraStripe::getAll();
```

# Full code

```php
$charge = LaraStripe::setup([
    'secret_key' => '******',
    'public_key' => '******',
    'currency'   => 'usd'
])
->card($token)
->amount(121.50)
->metaData(['product_id'=>'p-121','purchase_id' => 'pur-12321'])
->description('LaraStripe Laravel Stripe payment')
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
    "amount": 121.50,
    "currency": "usd",
    "balance_transaction": "txn_1FVAcYAHZl11YnL9Ld0Fq3lp",
    "description": "LaraStripe Laravel Stripe payment",
    "paid": true,
    "status": "succeeded",
    "meta_data": {
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
