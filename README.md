# LaraStripe
Laravel Stripe payment



```php
$payment = LaraStripe::setup(['secret'=>'sk_test_mBGoFuccDy2KCD4pobbaixKK00qUW0ghu1','currency'=>'usd'])
       // ->card([
       //      'number' => '6011111111111117',
       //      'exp_month' => 10,
       //      'exp_year' => 2020,
       //      'cvc' => '314'
       //  ])
        ->card('tok_visa')
        ->amount(22.267654)
        ->metaData(['tnx_id' => 'tnx-32343','purchase_id' => 'trgtrg45'])
        ->description('gautam dada tester')
        ->purchase()
        ->get();

   return response()->json($payment);
```
