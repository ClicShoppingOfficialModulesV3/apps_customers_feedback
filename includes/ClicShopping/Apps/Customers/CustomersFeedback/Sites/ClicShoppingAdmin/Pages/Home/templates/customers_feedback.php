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
  $CLICSHOPPING_Language = Registry::get('Language');

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/feedback.png', $CLICSHOPPING_CustomersFeedback->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-6 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_CustomersFeedback->getDef('heading_title'); ?></span>
          <span class="col-md-5 text-end">
            <?php echo HTML::form('delete_all', $CLICSHOPPING_CustomersFeedback->link('CustomersFeedback&DeleteAllpage=' . (int)$_GET['page'])); ?>
            <a onclick="$('delete').prop('action', ''); $('form').submit();"
               class="button"><span><?php echo HTML::button(CLICSHOPPING::getDef('button_delete'), null, null, 'danger'); ?></span></a>&nbsp;
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <td>
      <table class="table table-sm table-hover table-striped">
        <thead>
        <tr class="dataTableHeadingRow">
          <th width="1" class="text-center"><input type="checkbox"
                                                      onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"/>
          </th>
          <th><?php echo $CLICSHOPPING_CustomersFeedback->getDef('table_heading_orders'); ?></th>
          <th><?php echo $CLICSHOPPING_CustomersFeedback->getDef('table_heading_rating'); ?></th>
          <th><?php echo $CLICSHOPPING_CustomersFeedback->getDef('table_heading_feedback_author'); ?></th>
          <th
            class="text-center"><?php echo $CLICSHOPPING_CustomersFeedback->getDef('table_heading_date_added'); ?></th>
          <th
            class="text-center"><?php echo $CLICSHOPPING_CustomersFeedback->getDef('table_heading_approved'); ?></th>
          <th
            class="text-center"><?php echo $CLICSHOPPING_CustomersFeedback->getDef('table_heading_approved_customer'); ?></th>
          <th class="text-end"><?php echo $CLICSHOPPING_CustomersFeedback->getDef('table_heading_action'); ?>
            &nbsp;
          </th>
        </tr>
        </thead>
        <?php

          $Qfeedback = $CLICSHOPPING_CustomersFeedback->db->prepare('select  SQL_CALC_FOUND_ROWS  r.feedback_order_reviews_id,
                                                                                                  r.orders_id,
                                                                                                   r.date_added,
                                                                                                   r.customers_name,
                                                                                                   r.feedback_order_reviews_rating,
                                                                                                   r.status,
                                                                                                   r.feedback_accept_to_publish,
                                                                                                   o.orders_id
                                                                      from :table_feedback_order_reviews r,
                                                                           :table_orders o
                                                                      where o.orders_id = r.orders_id
                                                                      order by r.date_added DESC
                                                                      limit :page_set_offset,
                                                                            :page_set_max_results
                                                                      ');

          $Qfeedback->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
          $Qfeedback->execute();

          $listingTotalRow = $Qfeedback->getPageSetTotalRows();

          if ($listingTotalRow > 0) {

            while ($Qfeedback->fetch()) {

              if ((!isset($_GET['rID']) || (isset($_GET['rID']) && ((int)$_GET['rID'] == $Qfeedback->valueInt('feedback_order_reviews_id')))) && !isset($rInfo)) {
                $rInfo = new ObjectInfo($Qfeedback->toArray());
              }

              if ((!isset($_GET['rID']) || (isset($_GET['rID']) && ((int)$_GET['rID'] === $Qfeedback->valueInt('feedback_order_reviews_id')))) && !isset($rInfo)) {

                $QreviewsText = $CLICSHOPPING_CustomersFeedback->db->prepare('select r.feedback_order_reviews_read,
                                                                                     r.customers_name,
                                                                                     length(rd.feedback_order_reviews_text) as feedback_order_reviews_text_size
                                                                              from :table_feedback_order_reviews r,
                                                                                   :table_feedback_order_reviews_description rd
                                                                              where r.feedback_order_reviews_id = :feedback_order_reviews_id
                                                                              and r.feedback_order_reviews_id = rd.feedback_order_reviews_id
                                                                              ');
                $QreviewsText->bindValue(':feedback_order_reviews_id', (int)$Qfeedback->valueInt('feedback_order_reviews_id'));
                $QreviewsText->execute();

                $feedback_order_reviews_text = $QreviewsText->fetch();
              }
              ?>
              <td>
                <?php
                  if (isset($_POST['selected'])) {
                    ?>
                    <input type="checkbox" name="selected[]"
                           value="<?php echo $Qfeedback->valueInt('feedback_order_reviews_id'); ?>" checked="checked"/>
                    <?php
                  } else {
                    ?>
                    <input type="checkbox" name="selected[]"
                           value="<?php echo $Qfeedback->valueInt('feedback_order_reviews_id'); ?>"/>
                    <?php
                  }
                ?>
              </td>
              <th scope="row"><?php echo $Qfeedback->valueInt('orders_id'); ?></th>
              <td><?php echo '<i>' . HTML::stars($Qfeedback->valueInt('feedback_order_reviews_rating')) . '</i>'; ?></td>
              <td><?php echo $Qfeedback->value('customers_name'); ?></td>
              <td class="text-center"><?php echo DateTime::toShort($Qfeedback->value('date_added')); ?></td>
              <td class="text-center">
                <?php
                  if ($Qfeedback->valueInt('status') == 1) {
                    echo '<a href="' . $CLICSHOPPING_CustomersFeedback->link('CustomersFeedback&SetFlag&flag=0&id=' . $Qfeedback->valueInt('feedback_order_reviews_id')) . '"><i class="fas fa-check fa-lg" aria-hidden="true"></i></a>';
                  } else {
                    echo '<a href="' . $CLICSHOPPING_CustomersFeedback->link('CustomersFeedback&SetFlag&flag=1&id=' . $Qfeedback->valueInt('feedback_order_reviews_id')) . '"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a>';
                  }
                ?>
              </td>
              <td class="text-center">
                <?php
                  if ($Qfeedback->valueInt('feedback_accept_to_publish') == 1) {
                    echo '<i class="fas fa-check fa-lg" aria-hidden="true"></i></a>';
                  } else {
                    echo '<i class="fas fa-times fa-lg" aria-hidden="true"></i></a>';
                  }
                ?>
              </td>

              <td class="text-end">
                <?php echo '<a href="' . $CLICSHOPPING_CustomersFeedback->link('Edit&page=' . (int)$_GET['page'] . '&rID=' . $Qfeedback->valueInt('feedback_order_reviews_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_CustomersFeedback->getDef('icon_edit')) . '</a>'; ?>
              </td>
              </tr>
              <?php
            } //end while
          } // end $listingTotalRow
        ?>
      </table>
    </td>
  </table>
  </form><!-- end form delete all -->

  <?php
    if ($listingTotalRow > 0) {
      ?>
      <div class="row">
        <div class="col-md-12">
          <div
            class="col-md-6 float-start pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qfeedback->getPageSetLabel($CLICSHOPPING_CustomersFeedback->getDef('text_display_number_of_link')); ?></div>
          <div
            class="float-end text-end"> <?php echo $Qfeedback->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
        </div>
      </div>
      <?php
    }
  ?>
  <!-- body_eof //-->
</div>