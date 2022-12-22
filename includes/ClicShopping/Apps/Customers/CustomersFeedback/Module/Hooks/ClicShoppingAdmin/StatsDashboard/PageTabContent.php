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

  namespace ClicShopping\Apps\Customers\CustomersFeedback\Module\Hooks\ClicShoppingAdmin\StatsDashboard;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Customers\CustomersFeedback\CustomersFeedback as CustomersFeedbackApp;

  class PageTabContent implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('CustomersFeedback')) {
        Registry::set('CustomersFeedback', new CustomersFeedbackApp());
      }

      $this->app = Registry::get('CustomersFeedback');

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/StatsDashboard/page_tab_content');
    }

    private function statsAverageFeedback()
    {
      $Qaverage = $this->app->db->prepare('select ( (avg(feedback_order_reviews_rating) / 5) * 100) as average_rating
                                           from :table_feedback_order_reviews
                                          ');

      $Qaverage->execute();
      $average = round($Qaverage->value('average_rating'), 2) . ' %';

      return $average;
    }


    public function display()
    {

      if (!defined('CLICSHOPPING_APP_CUSTOMERS_CUSTOMERS_FEEDBACK_CF_STATUS') || CLICSHOPPING_APP_CUSTOMERS_CUSTOMERS_FEEDBACK_CF_STATUS == 'False') {
        return false;
      }

      if ($this->statsAverageFeedback() != 0) {
        $content = '
        <div class="row">
          <div class="col-md-11 mainTable">
            <div class="form-group row">
              <label for="' . $this->app->getDef('box_entry_average_feedback') . '" class="col-9 col-form-label"><a href="' . $this->app->link('CustomersFeedback') . '">' . $this->app->getDef('box_entry_average_feedback') . '</a></label>
              <div class="col-md-3">
                ' . $this->statsAverageFeedback() . '
              </div>
            </div>
          </div>
        </div>
        ';

        $output = <<<EOD
  <!-- ######################## -->
  <!--  Start FeedBack      -->
  <!-- ######################## -->
             {$content}
  <!-- ######################## -->
  <!--  Start FeedBack      -->
  <!-- ######################## -->
EOD;
        return $output;
      }
    }
  }