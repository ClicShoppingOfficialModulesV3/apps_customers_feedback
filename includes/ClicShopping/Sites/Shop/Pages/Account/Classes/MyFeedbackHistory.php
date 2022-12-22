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

  namespace ClicShopping\Sites\Shop\Pages\Account\Classes;

  use ClicShopping\OM\Registry;

  class MyFeedBackHistory
  {

    public static function getMyFeedBackHistory()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Language = Registry::get('Language');

      $QfeedBack = $CLICSHOPPING_Db->prepare('select SQL_CALC_FOUND_ROWS r.feedback_order_reviews_id,
                                                                   r.orders_id,
                                                                   r.customers_id,
                                                                   r.feedback_order_reviews_rating,
                                                                   rd.feedback_order_reviews_text
                                        from :table_feedback_order_reviews r,
                                             :table_feedback_order_reviews_description rd
                                        where r.status = 1
                                        and r.customers_id = :customers_id
                                        and r.feedback_order_reviews_id = rd.feedback_order_reviews_id
                                        and rd.languages_id = :languages_id
                                        order by date_added
                                        limit :page_set_offset,
                                              :page_set_max_results
                                    ');
      $QfeedBack->bindInt(':customers_id', (int)$CLICSHOPPING_Customer->getID());
      $QfeedBack->bindInt(':languages_id', (int)$CLICSHOPPING_Language->getId());
      $QfeedBack->setPageSet(MAX_DISPLAY_ORDER_HISTORY);
      $QfeedBack->execute();

      return $QfeedBack;

    }

    public static function getMyFeedBackHistoryTotalRows(): int
    {
      $feedBack = static::getMyFeedbackHistory();

      $feedBackTotalRow = $feedBack->getPageSetTotalRows();

      return $feedBackTotalRow;
    }

    public static function deleteMyFeedBackHistor(int $id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qdelete = $CLICSHOPPING_Db->prepare('delete :table_feedback_order_reviews
                                                    where feedback_order_reviews_id = :feedback_order_reviews_id
                                                   ');
      $Qdelete->bindInt(':feedback_order_reviews_id', $id);
      $Qdelete->execute();
    }
  }