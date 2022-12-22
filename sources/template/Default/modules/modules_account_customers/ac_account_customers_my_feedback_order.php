<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Sites\Shop\Pages\Account\Classes\MyFeedbackHistory;

  class ac_account_customers_my_feedback_order {

    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_account_customers_my_feedback_order_title');
      $this->description = CLICSHOPPING::getDef('module_account_customers_my_feedback_order_description');

      if (defined('MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_ORDER_TITLE_STATUS')) {
        $this->sort_order = MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_ORDER_TITLE_SORT_ORDER;
        $this->enabled = (MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_ORDER_TITLE_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Db = Registry::get('Db');

      if (isset($_GET['Account']) && isset($_GET['MyFeedBack'])) {

        $content_width = (int)MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_ORDER_CONTENT_WIDTH;

        $QfeedBack = MyFeedbackHistory::getMyFeedBackHistory();
        $feedbackTotalRow = MyFeedbackHistory::getMyFeedBackHistoryTotalRows();

        if ($feedbackTotalRow > 0) {

          $account_customers_title_content = '<!-- Start account_customers_my_feedback_order -->' . "\n";

          $account_customers_title_content .= '<div class="clearfix"></div>';
          $account_customers_title_content .= '<div class="contentText">';
          $account_customers_title_content .= '<div class="ModuleAccountCustomersListOrderTitle"><h3>' . CLICSHOPPING::getDef('module_account_customers_my_feedback_order_heading'). '</h3></div>';
          $account_customers_title_content .= '<div class="separator"></div>';

          while ($QfeedBack->fetch()) {
            $date_intervalle = DateTime::getIntervalDate(date_create('now')->format('Y-m-d H:i:s'), $QfeedBack->value('date_added'));

            if ( $date_intervalle < (int)MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_ORDER_DELETE || (int)MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_ORDER_DELETE == 0 ) {

            $account_customers_title_content .= '<div class="col-md-3">' . DateTime::toShort($QfeedBack->value('date_added')) . '</div>';
            $account_customers_title_content .= '<div class="col-md-1">#' . $QfeedBack->value('orders_id') . '</div>';
            $account_customers_title_content .= '<div class="col-md-4">' . CLICSHOPPING::getDef('text_review_rating') . ' ' . HTML::stars($QfeedBack->valueInt('feedback_order_reviews_rating') . ' ' . CLICSHOPPING::getDef('module_account_customers_my_feedback_order_text_of_5_stars'))  . '</div>';
            $account_customers_title_content .= '<span class="col-md-8">' . $QfeedBack->value('feedback_order_reviews_text') . '</span>';
            $account_customers_title_content .= '<span class="col-md-2 float-end">'. HTML::button(CLICSHOPPING::getDef('module_account_customers_my_feedback_order_button_order'), null, CLICSHOPPING::link(null, 'Account&HistoryInfo&order_id=' . $QfeedBack->valueInt('orders_id')), 'info').'</span>';

            $account_customers_title_content .= '<div class="clearfix"></div>';

            } else {

              if ((int)MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_ORDER_DELETE != 0) {
                $id = $QfeedBack->valueInt('feedback_order_reviews_id');
                MyFeedbackHistory::deleteMyFeedBackHistor($id);
              }
            }
          } // end while

          $account_customers_title_content .= '<div class="separator"></div>';
          $account_customers_title_content .= '<div class="hr"></div>';
          $account_customers_title_content .= '</div>' . "\n";
          $account_customers_title_content .= '<!-- end account_customers_my_feedback_order -->' . "\n";

          $CLICSHOPPING_Template->addBlock($account_customers_title_content, $this->group);
        }
      } // php_self
    } // function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_ORDER_TITLE_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_ORDER_TITLE_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Délais de suppression du commentaire',
          'configuration_key' => 'MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_ORDER_DELETE',
          'configuration_value' => '90',
          'configuration_description' => 'Veuillez indiquer le nombre de jour que le commentaire sera valide sur le site et non supprimé',
          'configuration_group_id' => '6',
          'sort_order' => '10',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select the width of the display?',
          'configuration_key' => 'MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_ORDER_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Please enter a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_ORDER_TITLE_SORT_ORDER',
          'configuration_value' => '125',
          'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
          'configuration_group_id' => '6',
          'sort_order' => '10',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
                                               ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
      );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array (
        'MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_ORDER_TITLE_STATUS',
        'MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_ORDER_DELETE',
        'MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_ORDER_CONTENT_WIDTH',
        'MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_ORDER_TITLE_SORT_ORDER'
      );
    }
  }
