<?php
if (zen_not_null($_POST)) {
  $products_name = (isset($_POST['products_name']) ? $_POST['products_name'] : '');
}
?>
    <?php echo zen_draw_label(TEXT_PRODUCTS_NAME, 'products_name', 'class="col-sm-3 control-label"'); ?>
<div class="col-sm-9 col-md-6">
      <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
    <div class="input-group">
      <span class="input-group-addon">
      <?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>
      </span>
    <?php echo zen_draw_input_field('products_name[' . $languages[$i]['id'] . ']', htmlspecialchars(isset($products_name[$languages[$i]['id']]) ? stripslashes($products_name[$languages[$i]['id']]) : zen_get_products_name($productInfo['products_id']['value'], $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), zen_set_field_length(TABLE_PRODUCTS_DESCRIPTION, 'products_name') . ' class="form-control"'); ?>
    </div>
    <br>
<?php } ?>
</div>