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

  namespace ClicShopping\Apps\Customers\CustomersFeedback\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  class Status
  {

    protected $feedback_order_reviews_id;
    protected $status;

    /**
     * Status feedback -  Sets the status of a reviewt
     *
     * @param string feedback_order_reviews_id, status
     * @return string status on or off
     * @access public
     */

    Public static function setFeedbackStatus(int $feedback_order_reviews_id, int $status)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if ($status == 1) {

        return $CLICSHOPPING_Db->save('feedback_order_reviews', ['status' => 1,
          'last_modified' => 'now()'
        ],
          ['feedback_order_reviews_id' => (int)$feedback_order_reviews_id]
        );

      } elseif ($status == 0) {

        return $CLICSHOPPING_Db->save('feedback_order_reviews', ['status' => 0,
          'last_modified' => 'now()'
        ],
          ['feedback_order_reviews_id' => (int)$feedback_order_reviews_id]
        );

      } else {
        return -1;
      }
    }
  }