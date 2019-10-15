<?php

namespace Code4mk\LaraStripe;

/**
 * @author    @code4mk <hiremostafa@gmail.com>
 * @author    @0devco <with@0dev.co>
 * @copyright 0dev.co (https://0dev.co)
 */

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Code4mk\LaraStripe\StripePay as StripePay;

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
   }
}
