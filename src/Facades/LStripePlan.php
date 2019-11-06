<?php

namespace Code4mk\LaraStripe\Facades;

/**
 * @author    @code4mk <hiremostafa@gmail.com>
 * @author    @kawsarsoft <with@kawsarsoft.com>
 * @copyright Kawsar Soft. (http://kawsarsoft.com)
 */

use Illuminate\Support\Facades\Facade;

class LStripePlan extends Facade
{
  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor()
  {
      return 'laraStripePlan';
  }
}
