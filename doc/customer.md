# Customer

`customer` for get future payment as like `subscription fee`.

LaraStripe has LaraStripeCustomer alias for customer related task.

# create new customer

## methods

### setup

Set secret key .

* `secret_key` required

```php
LaraStripeCustomer::setup([
    'secret_key'=>'sk_test_mBGoFuccDy2KCD4pobbaixKK00qUW0ghu1'
])

```

### create

Customer important data as like source(card token),email

* `source` required
* [see more on stripe](https://stripe.com/docs/api/customers/create)

```php
LaraStripeCustomer::create([
    ['source' => 'card token','email' => 'test@test.co']
])
```

### metadata

* customer extra info or data set with metadata as array.

```php
LaraStripeCustomer::metadata([
    'phone' => '212',
    'age' => 23
])
```

### get

`get` method return customer data after customer create complete.

* return type `object`

```php
LaraStripeCustomer::get();
```

## full code

```php
$cus = LaraStripeCustomer::setup([
    'secret_key'=>'sk_test_mBGoFuccDy2KCD4pobbaixKK00qUW0ghu1'
])
->create(['source' => 'tok_visa','email' => 'test@test.co'])
->metadata(['phone' => '212','age'=>23])
->get();

return response()->json($cus);
```

`~ NB: store customer id on db for future payment`

## `see - charge with customer `

 same as  [charge create with card](https://github.com/code4mk/lara-stripe/blob/master/doc/charge.md#full-code) with card. only `card method` exchage with `customer method`

* `customer($cusId)`

```php
$charge = LaraStripeCharge::setup([
                    'secret_key'=>'sk_test_mBGoFuccDy2KCD4pobbaixKK00qUW0ghu1',
                    'public_key' => 'pk_test_VNi7F1zcwwffZIi1tAkX1dVs00JfKPsCGR',
                    'currency'=>'usd'
                  ])
                  ->customer('cus_G2L2KoumL45hzn')
                  ->amount(25.54567)
                  ->metadata(['tnx_id' => 'tnx-32343','purchase_id' => 'trgtrg45'])
                  ->description('charge with customer id ')
                  ->purchase()
                  ->get();
return response()->json($charge);
```

# retrieve customer

* `retrieve` method has `cusId` param which is customer id.
* return type `object`

```php
$cus = LaraStripeCustomer::setup([
    'secret_key'=>'sk_test_mBGoFuccDy2KCD4pobbaixKK00qUW0ghu1'
])
->retrieve('cus_G2L2KoumL45hzn');

return response()->json($cus);
```

# change card

Sometimes customer want to change their card.

* `changeCard($cusId,$cardToken)` has `cusId` param which is customer id and `cardToken` param which is card token .
* return type `object`

```php
$cus = LaraStripeCustomer::setup([
    'secret_key'=>'sk_test_mBGoFuccDy2KCD4pobbaixKK00qUW0ghu1'
])
->changeCard('cus_G2L2KoumL45hzn','tok_amex');

return response()->json($cus);

```
