<?php
$record_company_array = array(array(
    'id' => '',
    'text' => TEXT_NONE));
$record_companies = $db->Execute("SELECT record_company_id, record_company_name
                                  FROM " . TABLE_RECORD_COMPANY . "
                                  ORDER BY record_company_name");
foreach ($record_companies as $record_company) {
  $record_company_array[] = array(
    'id' => $record_company['record_company_id'],
    'text' => $record_company['record_company_name']);
}
?>
<?php echo zen_draw_label(TEXT_PRODUCTS_RECORD_COMPANY, 'record_company_id', 'class="col-sm-3 control-label"'); ?>
<div class="col-sm-9 col-md-6">
  <?php echo zen_draw_pull_down_menu('record_company_id', $record_company_array, $productInformation->record_company_id['value'], 'class="form-control" id="record_company_id"'); ?>
</div>