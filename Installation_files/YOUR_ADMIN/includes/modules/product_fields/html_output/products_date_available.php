<?php echo zen_draw_label(TEXT_PRODUCTS_DATE_AVAILABLE, 'products_date_available', 'class="col-sm-3 control-label"'); ?>
<div class="col-sm-9 col-md-6">
  <div class="date input-group" id="datepicker">
    <span class="input-group-addon datepicker_icon">
      <i class="fa fa-calendar fa-lg"></i>
    </span>
    <?php echo zen_draw_input_field('products_date_available', $productInformation->products_date_available['value'], 'id="products_date_available" class="form-control" autocomplete="off"'); ?>
  </div>
  <span class="help-block errorText">(YYYY-MM-DD)</span>
</div>
<!-- script for datepicker -->
<script>
  $(function () {
    $('input[name="products_date_available"]').datepicker();
  })
</script>