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

  namespace ClicShopping\Sites\Shop\Pages\Account\Actions\MyFeedBack;

  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class Process extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (isset($_POST['action']) && ($_POST['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] === $_SESSION['sessiontoken'])) {
        $process = true;
        $error = false;

        if (isset($_POST['order_id']) && is_numeric($_POST['order_id']) && $process == 'true') {
          $error = true;

          $QcheckOrder = $CLICSHOPPING_Db->prepare('select orders_id
                                                    from :table_orders
                                                    where orders_id = :orders_id
                                                    and customers_id = :customers_id
                                                    limit 1
                                                  ');
          $QcheckOrder->bindInt(':orders_id', HTML::sanitize($_POST['order_id']));
          $QcheckOrder->bindInt(':customers_id', $CLICSHOPPING_Customer->getId());

          $QcheckOrder->execute();

          if ($QcheckOrder->fetch() !== false) {
            $QcheckFeedback = $CLICSHOPPING_Db->prepare('select feedback_order_reviews_id
                                                          from :table_feedback_order_reviews
                                                          where orders_id = :orders_id
                                                          and customers_id = :customers_id
                                                          limit 1
                                                        ');
            $QcheckFeedback->bindInt(':orders_id', $QcheckOrder->valueInt('order_id'));
            $QcheckFeedback->bindInt(':customers_id', $CLICSHOPPING_Customer->getId());

            $QcheckFeedback->execute();

            if ($QcheckFeedback->fetch() === false) {
              $rating = HTML::sanitize($_POST['rating']);
              $review = HTML::sanitize($_POST['review']);
              $order_id = HTML::sanitize($_POST['order_id']);
              $feedback_accept_to_publish = HTML::sanitize($_POST['accept_to_publish']);
              $customer_agree_privacy = HTML::sanitize($_POST['customer_agree_privacy']);

              if (isset($feedback_accept_to_publish)) {
                $feedback_accept_to_publish = 1;
              } else {
                $feedback_accept_to_publish = 0;
              }

              $error = false;
            } else {
              $error = true;
            }

            if (DISPLAY_PRIVACY_CONDITIONS == 'true') {
              if ($customer_agree_privacy != 'on') {
                $error = true;

                $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_agreement_check_error'), 'error');
              }
            }

            if ($order_id == 0 || is_null($order_id)) {
              $error = true;
              $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('module_account_customers_my_feedback_order_id_not_matchin'), 'error');
            }

            if (strlen($review) < REVIEW_TEXT_MIN_LENGTH) {
              $error = true;
              $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('module_account_customers_my_feedback_review_text'), 'error');
            }

            if ($rating < 0 || $rating > 5) {
              $error = true;
              $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('module_account_customers_my_feedback_review_rating'), 'error');
            }

            if ($error === false) {
              $CLICSHOPPING_Db->save('feedback_order_reviews', ['orders_id' => (int)$order_id,
                  'customers_id' => (int)$CLICSHOPPING_Customer->getID(),
                  'customers_name' => $CLICSHOPPING_Customer->getLastName() . ' ' . $CLICSHOPPING_Customer->getFirstName(),
                  'feedback_order_reviews_rating' => (int)$rating,
                  'date_added' => 'now()',
                  'feedback_accept_to_publish' => (int)$feedback_accept_to_publish,
                  'status' => 0
                ]
              );

              $insert_id = $CLICSHOPPING_Db->lastInsertId();

              $CLICSHOPPING_Db->save('feedback_order_reviews_description', ['feedback_order_reviews_id' => (int)$insert_id,
                  'languages_id' => (int)$CLICSHOPPING_Language->getId(),
                  'feedback_order_reviews_text' => HTML::sanitize($review)
                ]
              );

              $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('module_accout_customers_my_feedback_order_id_matchin'), 'error');

            } else {
              $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('module_accout_customers_my_feedback_notmatchin'), 'error');
            } // end error

          } else {
            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_error_feedback'), 'error');
          } // end isset($_POST['order_id']

          CLICSHOPPING::redirect(null, 'Account&Main');
        } // end isset($_POST['action'])
      }
    }
  }