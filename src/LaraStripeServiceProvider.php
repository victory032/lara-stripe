<?php

namespace Code4mk\LaraStripe;

/**
 * @author    @code4mk <hiremostafa@gmail.com>
 * @author    @kawsarsoft <with@kawsarsoft.com>
 * @copyright Kawsar Soft. (http://kawsarsoft.com)
 */

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Code4mk\LaraStripe\Lib\StripeCharge;
use Code4mk\LaraStripe\Lib\StripeCheckout;
use Code4mk\LaraStripe\Lib\StripeBalance;
use Code4mk\LaraStripe\Lib\StripeCustomer;
use Code4mk\LaraStripe\Lib\StripePlans;
use Code4mk\LaraStripe\Lib\StripeCoupon;
use Code4mk\LaraStripe\Lib\StripeSubscription;

class LaraStripeServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
   public function boot()
   {


      // publish config
       $this->publishes([
        __DIR__ . '/../config/stripe.php' => config_path('lara-stripe.php')
       ], 'config');

      AliasLoader::getInstance()->alias('LaraStripeCharge', 'Code4mk\LaraStripe\Facades\LStripeCharge');
      AliasLoader::getInstance()->alias('LaraStripeCheckout', 'Code4mk\LaraStripe\Facades\LStripeCheckout');
      AliasLoader::getInstance()->alias('LaraStripeBalance', 'Code4mk\LaraStripe\Facades\LStripeBalance');
      AliasLoader::getInstance()->alias('LaraStripeCustomer', 'Code4mk\LaraStripe\Facades\LStripeCustomer');
      AliasLoader::getInstance()->alias('LaraStripeCoupon', 'Code4mk\LaraStripe\Facades\LStripeCoupon');
      AliasLoader::getInstance()->alias('LaraStripePlan', 'Code4mk\LaraStripe\Facades\LStripePlan');
      AliasLoader::getInstance()->alias('LaraStripeSubs', 'Code4mk\LaraStripe\Facades\LStripeSubscription');

   }

  /**
   * Register any application services.
   *
   * @return void
   */
   public function register()
   {
     $this->app->bind('laraStripeCharge', function () {
         return new StripeCharge;
     });

     $this->app->bind('laraStripeCheckout', function () {
         return new StripeCheckout;
     });

     $this->app->bind('laraStripeBalance', function () {
         return new StripeBalance;
     });

     $this->app->bind('laraStripeCustomer', function () {
         return new StripeCustomer;
     });

     $this->app->bind('laraStripeCoupon', function () {
         return new StripeCoupon;
     });

     $this->app->bind('laraStripePlan', function () {
         return new StripePlans;
     });

     $this->app->bind('laraStripeSubscription', function () {
         return new StripeSubscription;
     });

   }
}
