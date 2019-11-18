<script>
  var tax_rates = new Array();
<?php
for ($i = 0, $n = sizeof($tax_class_array); $i < $n; $i++) {
  if ($tax_class_array[$i]['id'] > 0) {
    echo 'tax_rates["' . $tax_class_array[$i]['id'] . '"] = ' . zen_get_tax_rate_value($tax_class_array[$i]['id']) . ';' . "\n";
  }
}
?>

  function doRound(x, places) {
      return Math.round(x * Math.pow(10, places)) / Math.pow(10, places);
  }

  function getTaxRate() {
      var parameterVal = $('select[name="products_tax_class_id"]').val();
      if ((parameterVal > 0) && (tax_rates[parameterVal] > 0)) {
          return tax_rates[parameterVal];
      } else {
          return 0;
      }
  }

  function updateGross() {
      var taxRate = getTaxRate();
      var grossValue = $('input[name="products_price"]').val();
      if (taxRate > 0) {
          grossValue = grossValue * ((taxRate / 100) + 1);
      }

      $('input[name="products_price_gross"]').val(doRound(grossValue, 4));
  }

  function updateNet() {
      var taxRate = getTaxRate();
      var netValue = $('input[name="products_price_gross"]').val();
      if (taxRate > 0) {
          netValue = netValue / ((taxRate / 100) + 1);
      }

      $('input[name="products_price"]').val(doRound(netValue, 4));
  }
</script>
<div class="well" style="background-color: #ebebff;padding: 10px 10px 0 0;">
  <div class="form-group">
      <?php echo zen_draw_label(TEXT_PRODUCTS_PRICE_NET, 'products_price', 'class="col-sm-3 control-label"'); ?>
    <div class="col-sm-9 col-md-6">
      <div class="input-group">
          <?php if ($currencySymbolLeft != '') { ?>
          <span class="input-group-addon"><?php echo $currencySymbolLeft; ?></span>
        <?php } ?>
        <?php echo zen_draw_input_field('products_price', $productInfo['products_price']['value'], 'onkeyup="updateGross()" class="form-control"'); ?>
        <?php if ($currencySymbolRight != '') { ?>
          <span class="input-group-addon"><?php echo $currencySymbolRight; ?></span>
        <?php } ?>
      </div>
    </div>
  </div>
  <div class="form-group">
      <?php echo zen_draw_label(TEXT_PRODUCTS_PRICE_GROSS, 'products_price_gross', 'class="col-sm-3 control-label"'); ?>
    <div class="col-sm-9 col-md-6">
      <div class="input-group">
          <?php if ($currencySymbolLeft != '') { ?>
          <span class="input-group-addon"><?php echo $currencySymbolLeft; ?></span>
        <?php } ?>
        <?php echo zen_draw_input_field('products_price_gross', $productInfo['products_price']['value'], 'onkeyup="updateNet()" class="form-control"'); ?>
        <?php if ($currencySymbolRight != '') { ?>
          <span class="input-group-addon"><?php echo $currencySymbolRight; ?></span>
        <?php } ?>
      </div>
    </div>
  </div>
</div>
<script>
  updateGross();
</script>