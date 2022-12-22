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
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  use ClicShopping\Sites\Common\HTMLOverrideCommon;

  class ac_account_customers_my_feedback {

    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);
      $this->title = CLICSHOPPING::getdef('module_account_customers_my_feedack_title');
      $this->description = CLICSHOPPING::getdef('module_account_customers_my_feedack_description');

      $this->title = CLICSHOPPING::getDef('module_account_customers_my_feedback_title');
      $this->description = CLICSHOPPING::getDef('module_account_customers_my_feedback_description');

      if (defined('MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_TITLE_STATUS')) {
        $this->sort_order = MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_TITLE_SORT_ORDER;
        $this->enabled = (MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_TITLE_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if (isset($_GET['Account']) && isset($_GET['MyFeedBack'])) {
        $firstname = HTML::outputProtected($CLICSHOPPING_Customer->getFirstName());
        $lastname= HTML::outputProtected($CLICSHOPPING_Customer->getLastName());

        $content_width = (int)MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_CONTENT_WIDTH;
        $min_caracters_to_write = (int)REVIEW_TEXT_MIN_LENGTH;

        $header_tag = '<!--   Rate Yo start -->'."\n";
        $header_tag .= HTMLOverrideCommon::starHeaderTagRateYo();
        $header_tag .= '<!--   Rate Yo  end -->'."\n";
        $CLICSHOPPING_Template->addBlock($header_tag, 'header_tags');

        $footer_tag = '<!--   Rate Yo start -->'."\n";
        $footer_tag .='<script> ';
        $footer_tag .= '$(function () { ';
        $footer_tag .= '$("#rateYo").rateYo({ ';
        $footer_tag .= 'rating: 0,  ';
        $footer_tag .= 'fullStar: true, ';
//        $footer_tag .= 'normalFill: "' . MODULES_PRODUCTS_REVIEWS_WRITE_RATING_COLOR . '" ';
        $footer_tag .= '}) ';
        $footer_tag .= '.on("rateyo.set", function (e, data) { ';
        $footer_tag .= 'document.getElementById("rateyoid").value=data.rating; ';
        $footer_tag .= '}); ';
        $footer_tag .= '}); ';
        $footer_tag .='</script>';
        $footer_tag .= '<!--   Rate Yo End -->'."\n";
        $CLICSHOPPING_Template->addBlock($footer_tag, 'footer_scripts');

        $firstname = $CLICSHOPPING_Customer->getFirstName();
        $lastname = $CLICSHOPPING_Customer->getLastName();


        $QfeedbackOrders = $CLICSHOPPING_Db->prepare('select orders_id
                                                       from :table_feedback_order_reviews
                                                       where customers_id = :customers_id
                                                       limit 1
                                                      ');
        $QfeedbackOrders->bindInt(':customers_id',$CLICSHOPPING_Customer->getID());
        $QfeedbackOrders->execute();


        $feedbackOrders = $QfeedbackOrders->valueInt('orders_id');

        $QcustomerOrders = $CLICSHOPPING_Db->prepare('select orders_id
                                               from :table_orders
                                               where customers_id = :customers_id
                                               and orders_id <> :feedbackOrders
                                               order by orders_id
                                              ');
        $QcustomerOrders->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
        $QcustomerOrders->bindInt(':feedbackOrders', $feedbackOrders);
        $QcustomerOrders->execute();

        if ($QcustomerOrders->fetch() !== false) {
          while ($QcustomerOrders->fetch()) {
            $orders_array[] = ['id' => $QcustomerOrders->valueInt('orders_id'),
                               'name' =>  $QcustomerOrders->value('orders_id')
                              ];
          }


          $feedback = '<!-- Start account_customers_my_feed_back --> ' . "\n";

          $n = count($orders_array);

          if ($n >0) {
            for ($i=0, $n; $i<$n; $i++) {
              $array[$i] = ['id' => $orders_array[$i]['id'],
                            'text' => $orders_array[$i]['name'],
                           ];
            }

            $dropdown = HTML::selectMenu('order_id', $array);

            $form = HTML::form('write feedback', CLICSHOPPING::link(null, 'Account&MyFeedBack&Process'), 'post', 'id="write feedback"',  ['tokenize' => true, 'action' => 'process']);
            $endform ='</form>';


            $hidden_rating = HTML::hiddenField('rating', 1, 'id="rateyoid"');

            ob_start();
            require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/account_customers_my_feedback'));
            $feedback .= ob_get_clean();

          } else {

            $feedback .= '<div class="buttonSet col-md-12">';
            $feedback .= '<div class="col-md-6"><label for="buttonBack">' .  HTML::button(CLICSHOPPING::getDef('button_back'), null, CLICSHOPPING::link(null, 'Account&Main'), 'primary') . '</label></div>';
            $feedback .= '</div>';
          }


          $feedback .= '<!-- end account_customers_my_feed_back -->' . "\n";

          $CLICSHOPPING_Template->addBlock($feedback, $this->group);
        }
      }
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_TITLE_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_TITLE_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Autorisez-nous à publier votre feedback sur notre site ?',
          'configuration_key' => 'MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_CUSTOMER_AUTHORISE',
          'configuration_value' => 'True',
          'configuration_description' => 'Si le client accepte l\'autorisation, son commentaire peut apparaitre sur le site.<br><br><i>(Valeur True = Oui - Valeur False = Non)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select the width of the display?',
          'configuration_key' => 'MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_CONTENT_WIDTH',
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
          'configuration_key' => 'MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_TITLE_SORT_ORDER',
          'configuration_value' => '150',
          'configuration_description'=> 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array (
        'MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_TITLE_STATUS',
        'MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_CUSTOMER_AUTHORISE',
        'MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_CONTENT_WIDTH',
        'MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_TITLE_SORT_ORDER'
      );
    }
  }
