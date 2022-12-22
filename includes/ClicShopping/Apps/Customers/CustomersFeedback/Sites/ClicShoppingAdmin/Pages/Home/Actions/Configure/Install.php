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

  namespace ClicShopping\Apps\Customers\CustomersFeedback\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  use ClicShopping\OM\Cache;

  class Install extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_CustomersFeedback = Registry::get('CustomersFeedback');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_CustomersFeedback->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('CustomersFeedbackAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();
      static::installDb();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_CustomersFeedback->getDef('alert_module_install_success'), 'success', 'customers_feedback');

      $CLICSHOPPING_CustomersFeedback->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration()
    {
      $CLICSHOPPING_CustomersFeedback = Registry::get('CustomersFeedback');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_CustomersFeedback->db->get('administrator_menu', 'app_code', ['app_code' => 'app_customers_customers_feedback']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 5,
          'link' => 'index.php?A&Customers\CustomersFeedback&CustomersFeedback',
          'image' => 'customers_services.png',
          'b2b_menu' => 1,
          'access' => 0,
          'app_code' => 'app_customers_customers_feedback'
        ];

        $insert_sql_data = ['parent_id' => 4];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_CustomersFeedback->db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_CustomersFeedback->db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_CustomersFeedback->getDef('title_menu')];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_CustomersFeedback->db->save('administrator_menu_description', $sql_data_array);
        }

        Cache::clear('menu-administrator');
      }
    }

    private static function installDb()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_feedback_order_reviews"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_feedback_order_reviews (
  feedback_order_reviews_id int not null auto_increment,
  customers_id int not null,
  customers_name varchar(255) not null,
  feedback_order_reviews_rating int(1),
  date_added datetime,
  last_modified datetime,
  feedback_order_reviews_read int(5) default(0) not null,
  status tinyint(1) default(0) not null,
  feedback_accept_to_publish tinyint(1) default(0) not null,
  orders_id int default(0) not null,
  PRIMARY KEY (feedback_order_reviews_id),
  KEY idx_reviews_orders__id (orders_id),
  KEY idx_reviews_customers_id (customers_id)
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
        $CLICSHOPPING_Db->exec($sql);
      }

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_feedback_order_reviews_description"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_feedback_order_reviews_description (
  feedback_order_reviews_id int default(0) not null,
  languages_id int default(0) not null,
  feedback_order_reviews_text text not null,
  PRIMARY KEY feedback_order_reviews_id (languages_id)
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
        $CLICSHOPPING_Db->exec($sql);
      }
    }
  }
