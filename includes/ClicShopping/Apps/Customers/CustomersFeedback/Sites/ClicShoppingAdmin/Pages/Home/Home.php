<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Customers\CustomersFeedback\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Customers\CustomersFeedback\CustomersFeedback;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public mixed $app;

    protected function init()
    {
      $CLICSHOPPING_CustomersFeedback = new CustomersFeedback();
      Registry::set('CustomersFeedback', $CLICSHOPPING_CustomersFeedback);

      $this->app = Registry::get('CustomersFeedback');

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
