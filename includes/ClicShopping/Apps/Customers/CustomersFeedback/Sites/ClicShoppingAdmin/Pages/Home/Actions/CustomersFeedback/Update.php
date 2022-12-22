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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  class Update extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function __construct()
    {
      $this->app = Registry::get('CustomersFeedback');
    }

    public function execute()
    {
      $feedback_order_reviews_id = HTML::sanitize($_GET['rID']);
      $feedback_order_reviews_text = $_POST['feedback_order_reviews_text'];
      $feedback_status = HTML::sanitize($_POST['status']);

      $Qupdate = $this->app->db->prepare('update :table_feedback_order_reviews
                                          set  status = :status,
                                              last_modified = now()
                                          where feedback_order_reviews_id = :feedback_order_reviews_id
                                        ');
      $Qupdate->bindInt(':status', $feedback_status);
      $Qupdate->bindInt(':feedback_order_reviews_id', (int)$feedback_order_reviews_id);

      $Qupdate->execute();

      $Qupdate = $this->app->db->prepare('update :table_feedback_order_reviews_description
                                          set feedback_order_reviews_text = :feedback_order_reviews_text
                                          where feedback_order_reviews_id = :feedback_order_reviews_id
                                        ');
      $Qupdate->bindValue(':feedback_order_reviews_text', $feedback_order_reviews_text);
      $Qupdate->bindInt(':feedback_order_reviews_id', (int)$feedback_order_reviews_id);

      $Qupdate->execute();

      $this->app->redirect('CustomersFeedback&' . (isset($_GET['page']) ? 'page=' . (int)$_GET['page'] . '&' : '') . 'rID=' . $feedback_order_reviews_id);
    }
  }