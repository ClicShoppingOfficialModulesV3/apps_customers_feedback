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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;

  $CLICSHOPPING_CustomersFeedback = Registry::get('CustomersFeedback');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_ProductsAdmin = Registry::get('ProductsAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_Currencies = Registry::get('Currencies');

  if (!isset($_POST)) {
    $rInfo = new ObjectInfo($_POST);
  } else {
    $rID = HTML::sanitize($_GET['rID']);

    $Qreviews = $CLICSHOPPING_CustomersFeedback->db->prepare('select r.feedback_order_reviews_id,
                                                              r.orders_id,
                                                              r.customers_name,
                                                              r.date_added,
                                                              r.last_modified,
                                                              r.feedback_order_reviews_read,
                                                              r.status,
                                                              r.feedback_accept_to_publish,
                                                              rd.feedback_order_reviews_text,
                                                              r.feedback_order_reviews_rating
                                                       from :table_feedback_order_reviews r,
                                                            :table_feedback_order_reviews_description rd
                                                       where r.feedback_order_reviews_id = :feedback_order_reviews_id
                                                       and r.feedback_order_reviews_id = rd.feedback_order_reviews_id
                                                     ');
    $Qreviews->bindValue(':feedback_order_reviews_id', (int)$rID);
    $Qreviews->execute();

    $feedback = $Qreviews->fetch();

    $Qorder = $CLICSHOPPING_CustomersFeedback->db->prepare('select orders_id
                                                            from :table_orders
                                                            where orders_id = :orders_id
                                                           ');
    $Qorder->bindValue(':orders_id', (int)$Qreviews->valueInt('orders_id'));
    $Qorder->execute();

    $order = $Qorder->fetch();
    $rInfo_array = array_merge($feedback, $order);
    $rInfo = new ObjectInfo($rInfo_array);
  }
?>
<div class="contentBody">

  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/feedback.png', $CLICSHOPPING_CustomersFeedback->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-5 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_CustomersFeedback->getDef('heading_title'); ?></span>
          <span class="col-md-6 text-end">
<?php
  echo HTML::form('update', $CLICSHOPPING_CustomersFeedback->link('CustomersFeedback&Update&page=' . (int)$_GET['page'] . '&rID=' . $_GET['rID']), 'post', 'enctype="multipart/form-data"');

  foreach ($_POST as $key => $value) echo HTML::hiddenField($key, $value);

  echo HTML::button($CLICSHOPPING_CustomersFeedback->getDef('button_cancel'), null, $CLICSHOPPING_CustomersFeedback->link('CustomersFeedback&page=' . (int)$_GET['page'] . '&rID=' . $rInfo->feedback_order_reviews_id), 'warning') . ' ';
  echo HTML::button($CLICSHOPPING_CustomersFeedback->getDef('button_update'), null, null, 'success');
?>
            </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <?php
    $Qfeedback = $CLICSHOPPING_CustomersFeedback->db->prepare('select r.feedback_order_reviews_id,
                                                                r.orders_id,
                                                                r.customers_name,
                                                                r.date_added,
                                                                r.last_modified,
                                                                r.feedback_order_reviews_read,
                                                                r.feedback_accept_to_publish,
                                                                r.status,
                                                                rd.feedback_order_reviews_text,
                                                                r.feedback_order_reviews_rating
                                                        from :table_feedback_order_reviews r,
                                                              :table_feedback_order_reviews_description rd
                                                        where r.feedback_order_reviews_id = :feedback_order_reviews_id
                                                        and r.feedback_order_reviews_id = rd.feedback_order_reviews_id
                                                        ');
    $Qfeedback->bindValue(':feedback_order_reviews_id', (int)$rID);
    $Qfeedback->execute();

    $feedback = $Qfeedback->fetch();

    $Qorder = $CLICSHOPPING_CustomersFeedback->db->prepare('select orders_id
                                                      from :table_orders
                                                      where orders_id = :orders_id
                                                      ');
    $Qorder->bindValue(':orders_id', (int)$Qfeedback->valueInt('orders_id'));
    $Qorder->execute();

    $order = $Qorder->fetch();

    $rInfo_array = array_merge((array)$feedback, (array)$order);
    $rInfo = new ObjectInfo($rInfo_array);

    //creation du tableau pour le  dropdown des status des commentaires
    $status_array = array(array('id' => '1', 'text' => $CLICSHOPPING_CustomersFeedback->getDef('entry_status_yes')),
      array('id' => '0', 'text' => $CLICSHOPPING_CustomersFeedback->getDef('entry_status_no'))
    );

    if ($rInfo->feedback_accept_to_publish == 'no') {
      $feedback_accept_to_publish = $CLICSHOPPING_CustomersFeedback->getDef('entry_feedback_publish_no');
    } else {
      $feedback_accept_to_publish = $CLICSHOPPING_CustomersFeedback->getDef('entry_feedback_publish_yes');
    }
  ?>

  <div>
    <ul class="nav nav-tabs flex-column flex-sm-row" role="tablist" id="myTab">
      <li
        class="nav-item"><?php echo '<a href="#tab1" role="tab" data-toggle="tab" class="nav-link active">' . $CLICSHOPPING_CustomersFeedback->getDef('tab_general') . '</a>'; ?></li>
    </ul>
    <div class="tabsClicShopping">
      <div class="tab-content">
        <div class="col-md-12 mainTitle">
          <div><?php echo $CLICSHOPPING_CustomersFeedback->getDef('title_reviews_general'); ?></div>
        </div>
        <div class="adminformTitle">
          <div class="separator"></div>
          <div class="row">
            <div class="col-md-12">
              <span class="col-md-2"><?php echo $CLICSHOPPING_CustomersFeedback->getDef('entry_order'); ?></span>
              <span class="col-md-1"><?php echo '<strong>' . $rInfo->orders_id . '</strong>'; ?></span>
              <span
                class="col-md-1"><?php echo HTML::link(CLICSHOPPING::link(null, 'A&Orders\Orders&Orders&oID=' . (int)$Qfeedback->valueInt('orders_id') . '&action=edit'), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_CustomersFeedback->getDef('icon_edit'))); ?></span>
            </div>
            <div class="separator"></div>
            <div class="col-md-12">
              <span class="col-md-2"><?php echo $CLICSHOPPING_CustomersFeedback->getDef('customers_name'); ?></span>
              <span class="col-md-4"><?php echo '<strong>' . $rInfo->customers_name . '</strong>'; ?></span>
            </div>
            <div class="separator"></div>
            <div class="col-md-12">
              <span class="col-md-2"><?php echo $CLICSHOPPING_CustomersFeedback->getDef('entry_date'); ?></span>
              <span
                class="col-md-4"><?php echo '<strong>' . DateTime::toLong($rInfo->date_added) . '</strong>'; ?></span>
            </div>
            <div class="separator"></div>
            <div class="col-md-12">
              <span class="col-md-2"><?php echo $CLICSHOPPING_CustomersFeedback->getDef('entry_rating'); ?></span>
              <span
                class="col-md-4"><?php echo '<i>' . HTML::stars($rInfo->feedback_order_reviews_rating) . '</i>'; ?></span>
            </div>
            <div class="separator"></div>
            <div class="col-md-12">
              <span
                class="col-md-4"><?php echo $CLICSHOPPING_CustomersFeedback->getDef('entry_accept_to_publish'); ?></span>
              <span class="col-md-4"
                    style="color:red;"><?php echo '<strong>' . $feedback_accept_to_publish . '</strong>'; ?></span>
            </div>
            <div class="separator"></div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="<?php echo $CLICSHOPPING_CustomersFeedback->getDef('entry_status'); ?>"
                       class="col-1 col-form-label"><?php echo $CLICSHOPPING_CustomersFeedback->getDef('entry_status'); ?></label>
                <div class="col-md-3">
                  <?php echo HTML::selectMenu('status', $status_array, (($rInfo->status == '1') ? '1' : '0')); ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="separator"></div>
      <div class="col-md-12 mainTitle">
        <div><?php echo $CLICSHOPPING_CustomersFeedback->getDef('table_heading_product_order'); ?></div>
      </div>
      <div class="adminformTitle">
        <?php
          $Qproducts = $CLICSHOPPING_CustomersFeedback->db->prepare('select op.orders_id,
                                                                     op.products_id,
                                                                     op.products_name,
                                                                     op.products_model,
                                                                     op.products_quantity,
                                                                     op.products_price,
                                                                     op.final_price
                                                              from :table_orders_products op
                                                              where orders_id = :orders_id
                                                              ');
          $Qproducts->bindInt(':orders_id', $Qorder->valueInt('orders_id'));

          $Qproducts->execute();

          while ($Qproducts->fetch()) {
            ?>
            <table class="table table-sm table-hover">
              <thead>
              <tr>
                <td><?php echo $CLICSHOPPING_CustomersFeedback->getDef('table_heading_order_id'); ?></td>
                <td><?php echo $CLICSHOPPING_CustomersFeedback->getDef('table_heading_products_id'); ?></td>
                <td><?php echo $CLICSHOPPING_CustomersFeedback->getDef('table_heading_model'); ?></td>
                <td><?php echo $CLICSHOPPING_CustomersFeedback->getDef('table_heading_products_name'); ?></td>
                <td
                  class="text-end"><?php echo $CLICSHOPPING_CustomersFeedback->getDef('table_heading_price'); ?></td>
                <td
                  class="text-end"><?php echo $CLICSHOPPING_CustomersFeedback->getDef('table_heading_quantity'); ?></td>
                <td
                  class="text-end"><?php echo $CLICSHOPPING_CustomersFeedback->getDef('table_heading_final_price'); ?></td>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td><?php echo $Qproducts->value('orders_id'); ?></td>
                <td><?php echo $Qproducts->value('products_id'); ?></td>
                <td><?php echo $Qproducts->value('products_model'); ?></td>
                <td><?php echo $Qproducts->value('products_name'); ?></td>
                <td
                  class="text-end"><?php echo $CLICSHOPPING_Currencies->format($Qproducts->value('products_price'), true); ?></td>
                <td class="text-end"><?php echo $Qproducts->value('products_quantity'); ?></td>
                <td
                  class="text-end"><?php echo $CLICSHOPPING_Currencies->format($Qproducts->value('final_price'), true); ?></td>
              </tr>
              </tbody>
            </table>
            <div class="separator"></div>
            <?php
          }
        ?>
      </div>
      <!-- //################################################################################################################ -->
      <!--          avis client          //-->
      <!-- //################################################################################################################ -->
      <div class="separator"></div>
      <div class="col-md-12 mainTitle">
        <div><?php echo $CLICSHOPPING_CustomersFeedback->getDef('title_reviews_entry'); ?></div>
      </div>
      <div class="adminformTitle">
        <div class="separator"></div>
        <div class="row">
          <div class="col-md-12">
            <span
              class="col-md-4"><?php echo HTML::textAreaField('feedback_order_reviews_text', $rInfo->feedback_order_reviews_text, '60', '5'); ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php
    echo HTML::hiddenField('feedback_order_reviews_id', $rInfo->feedback_order_reviews_id);
    echo HTML::hiddenField('orders_id', $rInfo->orders_id);
    echo HTML::hiddenField('customers_name', $rInfo->customers_name);
    echo HTML::hiddenField('products_name', $Qproducts->value('products_name'));
    echo HTML::hiddenField('products_image', $Qproducts->value('products_image'));
    echo HTML::hiddenField('date_added', $rInfo->date_added);
  ?>
  </form>
</div>
</div>
