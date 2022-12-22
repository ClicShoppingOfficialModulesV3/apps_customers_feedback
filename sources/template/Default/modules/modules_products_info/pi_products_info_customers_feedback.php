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
  use ClicShopping\Sites\Common\HTMLOverrideCommon;

  class pi_products_info_customers_feedback {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_products_info_customers_feedback');
      $this->description = CLICSHOPPING::getDef('module_products_info_customers_feedback_description');

      if (defined('MODULE_PRODUCTS_INFO_CUSTOMERS_FEEDBACK_STATUS')) {
        $this->sort_order = MODULE_PRODUCTS_INFO_CUSTOMERS_FEEDBACK_SORT_ORDER;
        $this->enabled = (MODULE_PRODUCTS_INFO_CUSTOMERS_FEEDBACK_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');

      if ($CLICSHOPPING_ProductsCommon->getID() && isset($_GET['Description']) && isset($_GET['Products'])) {

        $content_width = (int)MODULE_PRODUCTS_INFO_CUSTOMERS_FEEDBACK_CONTENT_WIDTH;

        $CLICSHOPPING_Db = Registry::get('Db');
        $CLICSHOPPING_Template = Registry::get('Template');

        $products_customers_feedback_content = HTMLOverrideCommon::starHeaderTagRateYo();

//*******************************************
// customers_feedback
//********************************************

        $QorderProducts = $CLICSHOPPING_Db->prepare('select  products_id,
                                                              orders_id
                                                       from :table_orders_products
                                                       where products_id = :products_id
                                                     ');
        $QorderProducts->bindValue(':products_id', $CLICSHOPPING_ProductsCommon->getID());
        $QorderProducts->execute();

         if ($QorderProducts->rowCount() >= 1) {

          $products_customers_feedback_content .= '<!-- Start products_customers_feedback -->' . "\n";
          $products_customers_feedback_content .= '<div class="' . $content_width . '">';
          $products_customers_feedback_content .= '<div class="separator"></div>';
          $products_customers_feedback_content .= '<div class="col-md-12 moduleProductsInfoReviewsRow">';
          $products_customers_feedback_content .= '<div class="moduleProductsInfoReviewsTitle">';
          $products_customers_feedback_content .= '<span class="page-title moduleProductsInfoReviewsTitle"><h3>' . CLICSHOPPING::getDef('heading_rewiews')  . ' ' . $CLICSHOPPING_ProductsCommon->getProductsName() . '</h3></span>';
          $products_customers_feedback_content .= '</div>';
          $products_customers_feedback_content .= '<div class="float-end">';
          $products_customers_feedback_content .= '';
          $products_customers_feedback_content .= '</div>';
          $products_customers_feedback_content .= '<div class="clearfix"></div>';
          $products_customers_feedback_content .= '<hr>';
          $products_customers_feedback_content .= '<div class="d-flex flex-wrap">';

//*******************************************
// customers_feedback
//********************************************
          while ($QorderProducts->fetch()) {

            $Qfeedback = $CLICSHOPPING_Db->prepare('select r.orders_id,
                                                            left(r.customers_name, 5) as customers_name,
                                                            r.date_added,
                                                            r.feedback_accept_to_publish,
                                                            r.status,
                                                            r.feedback_order_customers_feedback_rating as rating,
                                                            left (rd.feedback_order_customers_feedback_text, :limitText ) as feedback_order_customers_feedback_text,
                                                            r.feedback_order_reviews_rating
                                                     from :table_feedback_order_customers_feedback r,
                                                          :table_feedback_order_customers_feedback_description rd
                                                     where r.orders_id = :orders_id
                                                     and r.feedback_order_customers_feedback_id = rd.feedback_order_customers_feedback_id
                                                     and r.status = 1
                                                     and r.feedback_accept_to_publish = 1
                                                     limit :limit
                                                ');

            $Qfeedback->bindInt(':orders_id', (int)$QorderProducts->valueInt('orders_id'));
            $Qfeedback->bindInt(':limitText', MODULE_PRODUCTS_INFO_CUSTOMERS_FEEDBACK_NUMBER_WORDS);
            $Qfeedback->bindInt(':limit', (int)MODULE_PRODUCTS_INFO_CUSTOMERS_FEEDBACK_NUMBER_COMMENTS);
            $Qfeedback->execute();


            $countOrderFeedback = $Qfeedback->rowCount();

            while ($Qfeedback->fetch()) {

              $customer_name  = '*** ' . HTML::outputProtected(substr($Qfeedback->value('customers_name') . ' ' , 1, -1 )) . ' ***';

              $products_customers_feedback_content .= '<div class="col-md-12 productsInfotextReviewBy">';
              $products_customers_feedback_content .= '<span class="productsInfotextReviewByName">';
              $products_customers_feedback_content .=  CLICSHOPPING::getDef('text_review_by', ['customer_name' => $customer_name]);
              $products_customers_feedback_content .= '</span>';
              $products_customers_feedback_content .= '<div class="float-end">';
              $products_customers_feedback_content .= '<meta itemprop="worstRating" content = "1">';
              $products_customers_feedback_content .= '<span class="productsInfoReviewsRating">' . HTML::stars($Qfeedback->value('feedback_order_reviews_rating ')) . '</span>';
              $products_customers_feedback_content .= '</div>';

              $products_customers_feedback_content .= '</div>';
              $products_customers_feedback_content .= '<div class="col-md-12  moduleProductsInfoDateReviewAdded" itemprop="datePublished" content="' . DateTime::toLong($Qfeedback->value('date_added')) . '">';
              $products_customers_feedback_content .= '<span class="moduleProductsInfoDateReviewAdded">' . CLICSHOPPING::getDef('text_review_date_added', ['date' => DateTime::toLong($Qfeedback->value('date_added'))]) . '</span>';
              $products_customers_feedback_content .= '</div>';

              $products_customers_feedback_content .= '<div class="col-md-12">';
              $products_customers_feedback_content .= '<div class="ProductInfoReviewText">';
              $products_customers_feedback_content .= HTML::breakString(HTML::outputProtected($Qfeedback->value('feedback_order_customers_feedback_text')), 60, '-<br />') . ((strlen($Qfeedback->value['feedback_order_customers_feedback_text']) >= MODULE_PRODUCTS_INFO_CUSTOMERS_FEEDBACK_NUMBER_WORDS) ? '..' : '') . '<br />';
              $products_customers_feedback_content .= '</div>';
              $products_customers_feedback_content .= '</div>';
              $products_customers_feedback_content .= '<hr>';
            }
          }


          if ($countOrderFeedback != 0) {

            $details_button = HTML::button(CLICSHOPPING::getDef('button_all_customers_feedback'), null, CLICSHOPPING::link(null, 'Products&Reviews&products_id=' . $CLICSHOPPING_ProductsCommon->getID()), 'info');
            $write_button = HTML::button(CLICSHOPPING::getDef('button_write_review'), null, CLICSHOPPING::link(null, 'Products&ReviewsWrite&products_id=' . $CLICSHOPPING_ProductsCommon->getID()), 'success');

            $products_customers_feedback_content .= '<div class="clearfix"></div>';

            $products_customers_feedback_content .= '<span class="col-md-2">' . $details_button . '</span>';
            $products_customers_feedback_content .= '<span class="col-md-10 text-end">' . $write_button . '</span>';
            $products_customers_feedback_content .= '</div>' . "\n";
            $products_customers_feedback_content .= '</div>' . "\n";
            $products_customers_feedback_content .= '<!-- end products_CUSTOMERS_FEEDBACK -->' . "\n";

            $CLICSHOPPING_Template->addBlock($products_customers_feedback_content, $this->group);
          }
        }
      }
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_PRODUCTS_INFO_CUSTOMERS_FEEDBACK_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_CUSTOMERS_FEEDBACK_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select the width of the display?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_CUSTOMERS_FEEDBACK_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Please enter a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'How many comments would you like to display ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_CUSTOMERS_FEEDBACK_NUMBER_COMMENTS',
          'configuration_value' => '5',
          'configuration_description' => 'Please indicate the number of comments you wish to display ?',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'How many words do you want to display ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_CUSTOMERS_FEEDBACK_NUMBER_WORDS',
          'configuration_value' => '300',
          'configuration_description' => 'Please indicate the number of words you wish to display ?',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_CUSTOMERS_FEEDBACK_SORT_ORDER',
          'configuration_value' => '102',
          'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
          'configuration_group_id' => '6',
          'sort_order' => '4',
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
        'MODULE_PRODUCTS_INFO_CUSTOMERS_FEEDBACK_STATUS',
        'MODULE_PRODUCTS_INFO_CUSTOMERS_FEEDBACK_CONTENT_WIDTH',
        'MODULE_PRODUCTS_INFO_CUSTOMERS_FEEDBACK_NUMBER_COMMENTS',
        'MODULE_PRODUCTS_INFO_CUSTOMERS_FEEDBACK_NUMBER_WORDS',
        'MODULE_PRODUCTS_INFO_CUSTOMERS_FEEDBACK_SORT_ORDER'
      );
    }
  }
