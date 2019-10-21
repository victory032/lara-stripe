# LaraStripe
Laravel Stripe payment


# charge .

```php
Route::get('pay',function(){

 $payment = LaraStripe::setup([
                            'secret'=>'sk_test_mBGoFuccDy2KCD4pobbaixKK00qUW0ghu1',
                            'public_key' => 'pk_test_VNi7F1zcwwffZIi1tAkX1dVs00JfKPsCGR',
                            'currency'=>'usd'
                        ])
                        // ->card([
                        //      'number' => '6011111111111117',
                        //      'exp_month' => 10,
                        //      'exp_year' => 2020,
                        //      'cvc' => '314'
                        //  ])
                         ->card('tok_visa')
                         ->amount(25.267654)
                         ->metaData(['tnx_id' => 'tnx-32343','purchase_id' => 'trgtrg45'])
                         ->description('kamal is here')
                         ->purchase()
                         ->get();

    return response()->json($payment);
});
```

# payment checkout

```php
Route::get('checkout',function(){
    $checkoutSession = LaraStripeSession::setup([
                                               'secret'=>'sk_test_mBGoFuccDy2KCD4pobbaixKK00qUW0ghu1',
                                               'public_key' => 'pk_test_VNi7F1zcwwffZIi1tAkX1dVs00JfKPsCGR',
                                               'currency'=>'usd'
                                          ])
                                      ->configure([
                                            'success_url' => 'http://localhost/TokenLite/public/asuccess?session_id={CHECKOUT_SESSION_ID}',
                                            'cancel_url' => 'http://localhost/TokenLite/public/',
                                            'ref_id' => 'yuyyryr'
                                        ])
                                        ->products([
                                            [
                                                'name' => 'T-shirt',
                                                'description' => 'ok t-shirt buy',
                                                'images' => ['https://cdn.pixabay.com/photo/2016/12/06/09/31/blank-1886008_960_720.png'],
                                                'amount' => 40,
                                                'quantity' => 2,
                                            ],
                                            [
                                                'name' => 'T-shirt2',
                                                'amount' => 50.23,
                                            ]
                                        ])
                                     ->getSession();
    return view('checkout',['checkoutSession'=> $checkoutSession ]);
});

Route::get('asuccess',function(){
 $r = LaraStripeSession::setup([
                            'secret'=>'sk_test_mBGoFuccDy2KCD4pobbaixKK00qUW0ghu1',
                            'public_key' => 'pk_test_VNi7F1zcwwffZIi1tAkX1dVs00JfKPsCGR',
                            'currency'=>'usd'
                        ])
                        ->retrieve('cs_test_MUuQVc4aa4VdSRzntxYvtMaJbnXBksNNTJiY7F8u7NZZNAuSFf17SFfX');
 return response()->json($r);
});
```
