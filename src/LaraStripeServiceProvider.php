<?php

namespace Code4mk\LaraStripe;

/**
 * @author    @code4mk <hiremostafa@gmail.com>
 * @author    @0devco <with@0dev.co>
 * @copyright 0dev.co (https://0dev.co)
 */

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Code4mk\LaraStripe\StripePay;
use Code4mk\LaraStripe\StripePaySession;
use Code4mk\LaraStripe\StripeBalance;
use Code4mk\LaraStripe\StripeCustomer;

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

      AliasLoader::getInstance()->alias('LaraStripe', 'Code4mk\LaraStripe\Facades\LStripe');
      AliasLoader::getInstance()->alias('LaraStripeSession', 'Code4mk\LaraStripe\Facades\LStripeSession');
      AliasLoader::getInstance()->alias('LaraStripeBalance', 'Code4mk\LaraStripe\Facades\LStripeBalance');
      AliasLoader::getInstance()->alias('LaraStripeCustomer', 'Code4mk\LaraStripe\Facades\LStripeCustomer');
   }

  /**
   * Register any application services.
   *
   * @return void
   */
   public function register()
   {
     $this->app->bind('laraStripe', function () {
         return new StripePay;
     });

     $this->app->bind('laraStripeSession', function () {
         return new StripePaySession;
     });

     $this->app->bind('laraStripeBalance', function () {
         return new StripeBalance;
     });

     $this->app->bind('laraStripeCustomer', function () {
         return new StripeCustomer;
     });

   }
}
