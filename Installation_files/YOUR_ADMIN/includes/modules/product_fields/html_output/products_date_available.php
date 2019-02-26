<?php echo zen_draw_label(TEXT_PRODUCTS_DATE_AVAILABLE, 'products_date_available', 'class="col-sm-3 control-label"'); ?>
<div class="col-sm-9 col-md-6">
  <div class="date input-group" id="datepicker">
    <span class="input-group-addon datepicker_icon">
      <i class="fa fa-calendar fa-lg"></i>
    </span>
    <?php echo zen_draw_input_field('products_date_available', $productInfo['products_date_available']['value'], 'class="form-control"'); ?>
  </div>
  <span class="help-block errorText">(YYYY-MM-DD)</span>
</div>
<!-- script for datepicker -->
<script>
  $('input[name="products_date_available"]').daterangepicker({
      'singleDatePicker': true,
      'showDropdowns': true,
      'locale': {
          'format': 'YYYY-MM-DD',
          'daysOfWeek': [
              '<?php echo _SUNDAY_SHORT; ?>',
              '<?php echo _MONDAY_SHORT; ?>',
              '<?php echo _TUESDAY_SHORT; ?>',
              '<?php echo _WEDNESDAY_SHORT; ?>',
              '<?php echo _THURSDAY_SHORT; ?>',
              '<?php echo _FRIDAY_SHORT; ?>',
              '<?php echo _SATURDAY_SHORT; ?>'
          ],
          'monthNames': [
              '<?php echo _JANUARY; ?>',
              '<?php echo _FEBRUARY; ?>',
              '<?php echo _MARCH; ?>',
              '<?php echo _APRIL; ?>',
              '<?php echo _MAY; ?>',
              '<?php echo _JUNE; ?>',
              '<?php echo _JULY; ?>',
              '<?php echo _AUGUST; ?>',
              '<?php echo _SEPTEMBER; ?>',
              '<?php echo _OCTOBER; ?>',
              '<?php echo _NOVEMBER; ?>',
              '<?php echo _DECEMBER; ?>'
          ]
      }
  }
  );
</script>