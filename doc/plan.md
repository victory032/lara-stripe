# Plan

lara-stripe has `LaraStripePlan` alias for plan related task.

#Methods

## setup() `required`

setup method as usual setup secret key

* `secret_key`  required

```php
setup([
    'secret_key' => '********'
])
```

## product() `required`

Plan for which product.

* `name`  required

```php
product(['name' => 'Sass Software Laravel'])
```

## amount() `required`

How much price for the plan.

```php
amount(20.50)
```

## currency() `required`

* [stripe supported currency](https://stripe.com/docs/currencies)

```php
currency('usd')
```

## interval() `required`

Plan recurring interval. `[day,week,month,year]`

```php
interval('week')
```

## extra `optional`

Add optional attributes which are not include this package for creating plan.

* [all attributes lists](https://stripe.com/docs/api/plans/create)

```php
extra([
    'tiers_mode' => 'volume'
     .....
])
```

## trial() `optional`

Set trial period for the plan. `integer value`

```php
trial(5)
```

## get() `required`

* create the plan and retrive created plan data.
* return `object`

```php
get()
```
# `Create a plan -> Full code`

```php
LaraStripePlan::setup(['secret_key' => '****'])
        ->product(['name' => 'Sass Software Laravel'])
        ->amount(20.50)
        ->currency('usd')
        ->interval('month')
        ->trial(3)
        ->get();
```

# `Retrive a plan`

* return `object`

```php
LaraStripePlan::setup(['secret_key' => '****'])
        ->retrieve('plan_id');
```

# `Deactived the plan`

```php
LaraStripePlan::setup(['secret_key' => '****'])
        ->deactive('plan_id');
```

# `Actived the plan`

```php
LaraStripePlan::setup(['secret_key' => '****'])
        ->active('plan_id');
```

# `Delete the plan`

```php
LaraStripePlan::setup(['secret_key' => '****'])
        ->delete('plan_id');
```
