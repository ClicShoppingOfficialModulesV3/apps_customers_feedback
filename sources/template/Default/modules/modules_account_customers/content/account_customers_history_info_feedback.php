<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>

<?php
    if (defined('MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_TITLE_STATUS')) {
      if (MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_TITLE_STATUS == 'True') {
?>
      <span class="float-end"><?php echo $button_feedback; ?></span>
<?php
      }
    }
?>
</div>
