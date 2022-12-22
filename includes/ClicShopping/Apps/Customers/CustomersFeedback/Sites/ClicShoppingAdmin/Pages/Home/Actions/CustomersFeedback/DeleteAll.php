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

  class DeleteAll extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('CustomersFeedback');
    }


    public function execute()
    {

      if (isset($_POST['selected'])) {
        foreach ($_POST['selected'] as $id) {

          $Qdelete = $this->app->db->prepare('delete
                                              from :table_feedback_order_reviews
                                              where feedback_order_reviews_id = :feedback_order_reviews_id
                                            ');
          $Qdelete->bindInt(':feedback_order_reviews_id', $id);
          $Qdelete->execute();

          $Qdelete = $this->app->db->prepare('delete
                                              from :table_feedback_order_reviews_description
                                              where feedback_order_reviews_id = :feedback_order_reviews_id
                                            ');
          $Qdelete->bindInt(':feedback_order_reviews_id', $id);
          $Qdelete->execute();

        }
      }

      $this->app->redirect('CustomersFeedback');
    }
  }
