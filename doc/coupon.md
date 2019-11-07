# Coupon

lara-stripe has `LaraStripeCoupon` alias for coupon related task.

#Methods

## setup() `required`

setup method as usual setup secret key

* `secret_key`  required

```php
setup([
    'secret_key' => '********'
])
```

## name() `required`

Coupon name. Automatic convert camel_case

```php
name('launch_20')
```

## amount() `required`

Set coupon amount and copoun type `fixed/per` fixed or percent.

* amount has 3 parameter amount (`required`) , type (`required`) and currency . `currency is required when type is fixed`

```php
# percent
amount(20,'per')
# fixed
amount(20.50,'fixed','usd')
````

## duration() `required`

* duration has 2 parameter one is type another is month.
* type `required` `[forever,once,repeating]`
* if type `repeating` that time `month` is `required`. month is integer value.

```php
duration('once')
# if repeating
duration('repeating',4)
```

## get() `required`

* create the coupon and retrive created coupon data.
* return `object`

```php
get()
```

# `Create coupon -> Full code`

```php
LaraStripeCoupon::setup(['secret_key' => '*****'])
            ->name('launch_20')
            ->amount(10,'per')
            ->duration('month')
            ->get()
```

# `Delete the coupon`

```php
LaraStripeCoupon::setup(['secret_key' => '*****'])
            ->delete('coupon_id')
```
