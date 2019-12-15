<?php
include (DIR_WS_LANGUAGES . $_SESSION['language'] . '/products_price_manager.php');
if (isset($_GET['pID']) && empty($_POST)) {
  $product_mdq = $db->Execute("SELECT products_mixed_discount_quantity
                               FROM " . TABLE_PRODUCTS . "
                               WHERE products_id = " . (int)$_GET['pID']);
  $productInfo['products_mixed_discount_quantity']['value'] = $product_mdq->fields['products_mixed_discount_quantity'];
} else {
  $productInfo['products_mixed_discount_quantity']['value'] = '0';
}
?>
<?php
// Product is product discount type - None, Percentage, Actual Price, $$ off
$discount_type_array = array(
  array('id' => '0', 'text' => DISCOUNT_TYPE_DROPDOWN_0),
  array('id' => '1', 'text' => DISCOUNT_TYPE_DROPDOWN_1),
  array('id' => '2', 'text' => DISCOUNT_TYPE_DROPDOWN_2),
  array('id' => '3', 'text' => DISCOUNT_TYPE_DROPDOWN_3));

// Product is product discount type from price or special
$discount_type_from_array = array(
  array('id' => '0', 'text' => DISCOUNT_TYPE_FROM_DROPDOWN_0),
  array('id' => '1', 'text' => DISCOUNT_TYPE_FROM_DROPDOWN_1));
if ($productInfo['products_id']['value'] != '') {
  $discounts_qty = $db->Execute("SELECT *
                                 FROM " . TABLE_PRODUCTS_DISCOUNT_QUANTITY . "
                                 WHERE products_id = " . (int)$productInfo['products_id']['value'] . "
                                 ORDER BY discount_qty");
}
$i = 0;
$discount_name = [];

if (isset($discounts_qty) && $discounts_qty->RecordCount() > 0) {
  foreach ($discounts_qty as $item) {
    $i++;
    $discount_name[] = [
      'id' => $i,
      'discount_qty' => $item['discount_qty'],
      'discount_price' => $item['discount_price']];
  }
} elseif (isset($productInfo['discount_qty']['value']) && $productInfo['discount_qty']['value'] != '') {
  $tempDiscountQty = $productInfo['discount_qty']['value'];
  $tempDiscountPrice = $productInfo['discount_price']['value'];
  for ($i = 0, $n = sizeof($tempDiscountQty); $i < $n; $i++) {
    $tempDiscount[$i + 1] = array(
      'discount_qty' => $tempDiscountQty[$i + 1],
      'discount_price' => $tempDiscountPrice[$i + 1]);
  }

  foreach ($tempDiscount as $item) {
    $i++;
    $discount_name[] = [
      'id' => $i,
      'discount_qty' => $item['discount_qty'],
      'discount_price' => $item['discount_price']];
  }
}
$disqountRow = sizeof($discount_name);
?>
<div class="table-responsive">
  <table id="qty_discount" class="table table-striped table-bordered table-hover">
    <thead>
      <tr>
        <td colspan="6">
          <?php echo zen_draw_label(TEXT_PRODUCTS_MIXED_DISCOUNT_QUANTITY, 'products_mixed_discount_quantity'); ?>
          <div class="input-group">
            <div class="radioBtn btn-group">
              <a class="btn btn-info <?php echo($productInfo['products_mixed_discount_quantity']['value'] == '1' ? 'active' : 'notActive'); ?>" data-toggle="products_mixed_discount_quantity" data-title="1"><?php echo TEXT_YES; ?></a>
              <a class="btn btn-info <?php echo($productInfo['products_mixed_discount_quantity']['value'] == '0' ? 'active' : 'notActive'); ?>" data-toggle="products_mixed_discount_quantity" data-title="0"><?php echo TEXT_NO; ?></a>
            </div>
            <?php echo zen_draw_hidden_field('products_mixed_discount_quantity', $productInfo['products_mixed_discount_quantity']['value'], 'class="products_mixed_discount_quantity"'); ?>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="main">
            <?php echo TEXT_DISCOUNT_TYPE_INFO; ?>
        </td>
        <td colspan="2" class="main">
            <?php echo TEXT_DISCOUNT_TYPE . ' ' . zen_draw_pull_down_menu('products_discount_type', $discount_type_array, $productInfo['products_discount_type']['value'], 'class="form-control"'); ?>
        </td>
        <td colspan="2" class="main">
            <?php echo TEXT_DISCOUNT_TYPE_FROM . ' ' . zen_draw_pull_down_menu('products_discount_type_from', $discount_type_from_array, $productInfo['products_discount_type_from']['value'], 'class="form-control"'); ?>
        </td>
      </tr>
      <tr>
        <td><?php echo TEXT_PRODUCTS_DISCOUNT_QTY_TITLE; ?></td>
        <td><?php echo TEXT_PRODUCTS_DISCOUNT_QTY; ?></td>
        <td><?php echo TEXT_PRODUCTS_DISCOUNT_PRICE; ?></td>
        <?php
        if (DISPLAY_PRICE_WITH_TAX_ADMIN == 'true') {
          ?>
          <td class="text-center"><?php echo TEXT_PRODUCTS_DISCOUNT_PRICE_EACH_TAX; ?></td>
          <td class="text-center"><?php echo TEXT_PRODUCTS_DISCOUNT_PRICE_EXTENDED_TAX; ?></td>
        <?php } else { ?>
          <td class="text-center"><?php echo TEXT_PRODUCTS_DISCOUNT_PRICE_EACH; ?></td>
          <td class="text-center"><?php echo TEXT_PRODUCTS_DISCOUNT_PRICE_EXTENDED; ?></td>
        <?php } ?>
        <td>&nbsp;</td>
      </tr>
    </thead>
    <tbody>
        <?php
        foreach ($discount_name as $row) {
          switch ($productInfo['products_discount_type']['value']) {
            // none
            case '0':
              $discounted_price = 0;
              break;
            // percentage discount
            case '1':
              if ($productInfo['products_discount_type_from']['value'] == '0') {
                $discounted_price = $display_price - ($display_price * ($row['discount_price'] / 100));
              } else {
                if (!$display_specials_price) {
                  $discounted_price = $display_price - ($display_price * ($row['discount_price'] / 100));
                } else {
                  $discounted_price = $display_specials_price - ($display_specials_price * ($row['discount_price'] / 100));
                }
              }

              break;
            // actual price
            case '2':
              if ($productInfo['products_discount_type_from']['value'] == '0') {
                $discounted_price = $row['discount_price'];
              } else {
                $discounted_price = $row['discount_price'];
              }
              break;
            // amount offprice
            case '3':
              if ($productInfo['products_discount_type_from']['value'] == '0') {
                $discounted_price = $display_price - $row['discount_price'];
              } else {
                if (!$display_specials_price) {
                  $discounted_price = $display_price - $row['discount_price'];
                } else {
                  $discounted_price = $display_specials_price - $row['discount_price'];
                }
              }
              break;
          }
          ?>
        <tr <?php echo 'id="discount-row' . $row['id'] . '"'; ?>>
          <td><?php echo TEXT_PRODUCTS_DISCOUNT . ' ' . $row['id']; ?></td>
          <td><?php echo zen_draw_input_field('discount_qty[' . $row['id'] . ']', $row['discount_qty'], 'class="form-control"'); ?></td>
          <td>
              <?php echo zen_draw_input_field('discount_price[' . $row['id'] . ']', $row['discount_price'], 'class="form-control"'); ?>
          </td>
          <?php
          if (DISPLAY_PRICE_WITH_TAX_ADMIN == 'true') {
            ?>
            <td class="text-right"><?php echo $currencies->display_price($discounted_price, 0, 1) . ' ' . $currencies->display_price($discounted_price, zen_get_tax_rate(1), 1); ?></td>
            <td class="text-right"><?php echo ' x ' . number_format($row['discount_qty']) . ' = ' . $currencies->display_price($discounted_price, 0, $row['discount_qty']) . ' ' . $currencies->display_price($discounted_price, zen_get_tax_rate(1), $row['discount_qty']); ?></td>
            <?php
          } else {
            ?>
            <td class="text-right"><?php echo $currencies->display_price($discounted_price, 0, 1); ?></td>
            <td class="text-right"><?php echo ' x ' . number_format($row['discount_qty']) . ' = ' . $currencies->display_price($discounted_price, 0, $row['discount_qty']); ?></td>
            <?php
          }
          ?>
          <td><button type="button" onclick="$('#discount-row<?php echo $row['id'] ?>').remove();" data-toggle="tooltip" title="" class="btn btn-danger" data-original-title="Remove Dicount"><i class="fa fa-minus-circle"></i></button></td>
        </tr>
        <?php
      }
      ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="5">&nbsp;</td>
        <td><button type="button" onclick="addDiscount();" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="Add Discount"><i class="fa fa-plus-circle"></i></button></td>
      </tr>
    </tfoot>
  </table>
</div>

<script>
  var discount_row = <?php echo $disqountRow + 1; ?>;

  function addDiscount() {
      html = '<tr id="discount-row' + discount_row + '">';
      html += '  <td><?php echo TEXT_PRODUCTS_DISCOUNT; ?>' + discount_row + '</td>';
      html += '  <td><input type="text" name="discount_qty[' + discount_row + ']" value="" class="form-control" /></td>';
      html += '  <td><input type="text" name="discount_price[' + discount_row + ']" value="" class="form-control" /></td>';
<?php
if (DISPLAY_PRICE_WITH_TAX_ADMIN == 'true') {
  ?>
        html += '  <td class="text-right"></td>';
        html += '  <td class="text-right"></td>';
  <?php
} else {
  ?>
        html += '  <td class="text-right"></td>';
        html += '  <td class="text-right"></td>';
  <?php
}
?>
      html += '  <td class="text-left"><button type="button" onclick="$(\'#discount-row' + discount_row + '\').remove();" data-toggle="tooltip" title="Remove" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
      html += '</tr>';

      $('#qty_discount tbody').append(html);

      discount_row++;
  }
</script>
