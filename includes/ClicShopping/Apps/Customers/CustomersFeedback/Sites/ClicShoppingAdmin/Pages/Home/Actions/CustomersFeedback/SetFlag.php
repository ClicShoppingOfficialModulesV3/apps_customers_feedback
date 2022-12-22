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


  namespace ClicShopping\Apps\Customers\CustomersFeedback\Sites\ClicShoppingAdmin\Pages\Home\Actions\CustomersFeedback;

  use ClicShopping\OM\Registry;


  use ClicShopping\Apps\Customers\CustomersFeedback\Classes\ClicShoppingAdmin\Status;

  class SetFlag extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {
      $CLICSHOPPING_CustomersFeedback = Registry::get('CustomersFeedback');

      Status::setFeedbackStatus($_GET['id'], $_GET['flag']);

      $CLICSHOPPING_CustomersFeedback->redirect('CustomersFeedback&' . (isset($_GET['page']) ? 'page=' . (int)$_GET['page'] . '&' : '') . 'rID=' . (int)$_GET['id']);
    }
  }

