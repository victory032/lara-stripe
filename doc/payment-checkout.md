# Payment Checkout

LaraStripe has payment checkout (session) alias `LaraStripeCheckout`.

# Methods

## setup

`setup` method has `secret,public_key,currency` properties as `array`.

```php
LaraStripeCheckout::setup([
    'secret_key' => '********',
    'public_key' => '****',
    'currency' => 'usd'
])
```

## configure

`configure` method has `success_url,cancel_url,ref_id`.

* `ref_key` can be a `customer ID`, a `cart ID`, or `similar`, and can be used to `reconcile` the session with your internal systems.

```php
LaraStripeCheckout::configure([
    'success_url' => 'http://test.co/success?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => 'http://test.co'
    'ref_key' => 'tnx_4345623232'
])
```

## products

`products` method has
*  `name <required>` `sring`
* `description <optional>`  `string`
*  `images <optional>` `array`
*  `amount <required>` `number`
*  `quantity <optional>` `integer`
    * default quantity is 1 . if you add qunatity that will be multiplication with amount `ex amount $20 and quantity 2 that time stripe payment total amount will be $20 * 2 = $40`


```php
LaraStripeCheckout::products([
    [
        'name' => 'T-shirt',
        'description' => 'Banladeshi T-shirt model 23',
        'images' => ['https://cdn.pixabay.com/photo/2016/12/06/09/31/blank-1886008_960_720.png'],
        'amount' => 20.50,
        'quantity' => 2
    ],
    [
        'name' => 'Mobile',
        'amount' => 150
    ]
])
```

## getSession

getSession method return session id (`sid`) and public key (`pkey`) .  session id as like `cs_test_k8ep1Z7ndlRmAgl0JU0m7SciO8QjSpoFjAIDheeCtCflp4gRdBShozOs` and public key as `pk_test_VNi7F1zcwwffZIi1tAkX1dVs00JfKPsCGR`.

* `type object`
* pass this data  with view

```php
LaraStripeCheckout::getSession()
```

## full code LaraStripeCheckout

* session_id genereate

```php
$session = LaraStripeCheckout::setup([
    'secret' => '********',
    'public_key' => '****',
    'currency' => 'usd'
])
->configure([
    'success_url' => 'http://test.co/success?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => 'http://test.co'
    'ref_id' => 'tnx_4345623232'
])
->products([
    [
        'name' => 'T-shirt',
        'description' => 'Banladeshi T-shirt model 23',
        'images' => ['https://cdn.pixabay.com/photo/2016/12/06/09/31/blank-1886008_960_720.png'],
        'amount' => 20.50,
        'quantity' => 2
    ],
    [
        'name' => 'Mobile',
        'amount' => 150
    ]
])
->getSession();
// return view('checkout',['session' => $session]);
// return response()->json($session)
```

# stripe.js

* include `<script src="https://js.stripe.com/v3/"></script>`

~ javascript

```js

var stripe = Stripe('your_stripe_public_key');

stripe.redirectToCheckout({
  // Make the id field from the Checkout Session creation API response
  // available to this file, so you can provide it as parameter here
  // instead of the {{CHECKOUT_SESSION_ID}} placeholder.
  sessionId: '{{CHECKOUT_SESSION_ID}}'
}).then(function (result) {
  // If `redirectToCheckout` fails due to a browser or network
  // error, display the localized error message to your customer
  // using `result.error.message`.
});
```

* blade view

~ javascript

* include `<script src="https://js.stripe.com/v3/"></script>`

```js
var publicKey = '{{ $session->pkey }}'
var SessionId = '{{ $session->sid }}'
var stripe = Stripe(publicKey);

stripe.redirectToCheckout({
  sessionId: SessionId
}).then(function (result) {

});
```

* sessionId will be that code which generate  `getSession method`

# retrive data `success route`

After success payment stripe redirect to success_url . retrive session data in success_url.

```php
Route::get('success',function(Request $request){
    $data = LaraStripeCheckout::setup([
        'secret' => '******',
        'public_key' => '******',
        'currency' => 'usd'
    ])
    ->retrieve($request->session_id);
    $data->ref_id;
    // return `customer ID` `cart ID`, or `similar`
})
```

# reference

* [session stripe](https://stripe.com/docs/api/checkout/sessions/object#checkout_session_object-id)
* [stripe official doc](https://stripe.com/payments/checkout)
