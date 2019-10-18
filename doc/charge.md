
# Stripe charge

LaraStripe has payment checkout (session) alias `LaraStripe`.


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

## metaData()

metaData methods array parameter you can declare here your `product id`, `customer id` or `similiar` datas.

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

get method return some data.

```php
LaraStripe::get();
```

## getAll()

get method return all datas.

```php
LaraStripe::getAll();
```
