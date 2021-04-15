<script>
  let tax_rates = new Array();
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
    const parameterVal = $('select[name="products_tax_class_id"]').val();
    if ((parameterVal > 0) && (tax_rates[parameterVal] > 0)) {
      return tax_rates[parameterVal];
    } else {
      return 0;
    }
  }

  const taxRate = getTaxRate();

  function updateGross() {
    let grossValue = $('input[name="products_price"]').val();
    if (taxRate > 0) {
      grossValue = grossValue * ((taxRate / 100) + 1);
    }

    $('input[name="products_price_gross"]').val(doRound(grossValue, 4));
  }

  function updateNet() {
    let netValue = $('input[name="products_price_gross"]').val();
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
        <?php echo zen_draw_input_field('products_price', $productInformation->products_price['value'], 'onkeyup="updateGross()" class="form-control" id="products_price" step="0.0001"', '', 'number'); ?>
        <?php if ($currencySymbolRight != '') { ?>
          <span class="input-group-addon"><?php echo $currencySymbolRight; ?></span>
        <?php } ?>
        <?php if ($currencyCode != '') { ?>
          <span class="input-group-addon"><?php echo $currencyCode; ?></span>
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
        <?php echo zen_draw_input_field('products_price_gross', $productInformation->products_price['value'], 'onkeyup="updateNet()" class="form-control" id="products_price_gross" step="0.0001"', '', 'number'); ?>
        <?php if ($currencySymbolRight != '') { ?>
          <span class="input-group-addon"><?php echo $currencySymbolRight; ?></span>
        <?php } ?>
        <?php if ($currencyCode != '') { ?>
          <span class="input-group-addon"><?php echo $currencyCode; ?></span>
        <?php } ?>
      </div>
    </div>
  </div>
</div>
<script>
  updateGross();
</script>