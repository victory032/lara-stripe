# Subscription

lara-stripe has `LaraStripeSubs` alias for subscription related task.

# Methods

## `setup()` `required`

setup method as usual setup secret key

* `secret_key`  `required`

```php
setup([
    'secret_key' => '********'
])
```

## `customer()` `required`

which customer subscribe.

* [create customer first](https://github.com/code4mk/lara-stripe/blob/master/doc/customer.md)

```php
customer('customer_id')
```

## `plan()` `required`

Customer subscription which plan.

* [create a plan first](https://github.com/code4mk/lara-stripe/blob/master/doc/customer.md)

```php
plan('plan_id')
```

## `trialPlan()` `optional`

If want to set trial period from plan.

```php
trialPlan()
```

## `trail()` `optional`

Set tril period for the subscription. This override the plan trail.

```php
trial(5)
```

## `source()` `optional`

set source (card token generate from stipe.js).

```php
source('tok_***')
```

## `coupon()` `optioanl`

Subscription with a coupon

```php
coupon('coupon_code')
```

## extra `optional`

Add optional attributes which are not include this package for creating subscription.

* [all attributes lists](https://stripe.com/docs/api/subscriptions/create)

```php
extra([
     .....
])
```

## `get()` `required`

* create the subscription and retrive created subscription data.
* return `object`

```php
get()
```

# `Create a Subscription -> Full code`


```php
LaraStripeSubs::setup(['secret_key'=>'******'])
            ->customer('customer_id')
            ->plan('plan_id')
            ->source('tok_***')
            ->trialPlan()
            ->coupon('coupon_id')
            ->get();
```

# `Retrieve the subscription`

```php
LaraStripeSubs::setup(['secret_key'=>'******'])
            ->retrieve('subs_id')        
```

# `Cancel the subscription`

```php
LaraStripeSubs::setup(['secret_key'=>'******'])
            ->cancel('subs_id')        
```
