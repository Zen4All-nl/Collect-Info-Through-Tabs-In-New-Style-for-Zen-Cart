<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="deleteProductModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <i class="fa fa-times" aria-hidden="true"></i>
          <span class="sr-only"><?php echo TEXT_CLOSE; ?></span>
        </button>
        <h4 class="modal-title" id="deleteProductHeading"><?php echo TEXT_INFO_HEADING_DELETE_PRODUCT; ?></h4>
      </div>
      <form name="formDeleteProductConfirm" method="post" enctype="multipart/form-data" id="deleteProductForm" class="form-horizontal">
        <?php echo zen_draw_hidden_field('securityToken', $_SESSION['securityToken']); ?>
        <div class="modal-body">
          <div class="row">
            <?php echo zen_draw_hidden_field('cPath', '', 'id="cPath"'); ?>
            <?php echo zen_draw_hidden_field('products_id', '', 'id="deleteProductId"'); ?>
            <div class="col-sm-12">
              <p><strong><?php echo TEXT_DELETE_CATEGORY_INTRO; ?></strong></p>
            </div>
            <div class="col-sm-12">
              <p><strong><?php echo TEXT_DELETE_CATEGORY_INTRO_LINKED_PRODUCTS; ?></strong></p>
            </div>
            <div class="col-sm-12">
              <p id="delCatModalCatName"></p>
            </div>
            <div class="col-sm-12">
              <p id="childs_count">
                <?php echo TEXT_DELETE_WARNING_CHILDS_START; ?>&nbsp;<span id="childs_count_number"></span>&nbsp;<?php echo TEXT_DELETE_WARNING_CHILDS_END; ?>
              </p>
            </div>
            <div class="col-sm-12">
              <p id="products_count">
                <?php echo TEXT_DELETE_WARNING_PRODUCTS_START; ?>&nbsp;<span id="products_count_number"></span>&nbsp;<?php echo TEXT_DELETE_WARNING_PRODUCTS_END; ?>
              </p>
            </div>
            <?php
            /*
              // future cat specific
              if ($cInfo->products_count > 0) {
              $contents[] = array('text' => TEXT_PRODUCTS_LINKED_INFO . '<br>' .
              zen_draw_radio_field('delete_linked', '1') . ' ' . TEXT_PRODUCTS_DELETE_LINKED_YES . '<br>' .
              zen_draw_radio_field('delete_linked', '0', true) . ' ' . TEXT_PRODUCTS_DELETE_LINKED_NO);
              }
             */
            ?>
            <div class="col-sm-12 text-center">
              <button type="submit" class="btn btn-danger" onclick="deleteCategoryConfirm();"><?php echo IMAGE_DELETE; ?></button> <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo IMAGE_CANCEL; ?></button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
$heading[] = array('text' => '<h4>' . TEXT_INFO_HEADING_DELETE_PRODUCT . '</h4>');

$contents = array('form' => zen_draw_form('products', FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'action=delete_product_confirm&product_type=' . $product_type . '&cPath=' . $cPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''), 'post', 'class="form-horizontal"') . zen_draw_hidden_field('products_id', $pInfo->products_id));
$contents[] = array('text' => TEXT_DELETE_PRODUCT_INTRO);
$contents[] = array('text' => '<strong>' . $pInfo->products_name . ' ID#' . $pInfo->products_id . '</srong>');

// zen_get_category_name(zen_get_parent_category_id($pInfo->products_id), (int)$_SESSION['languages_id'])

$product_categories_string = '';
$product_categories = zen_generate_category_path($pInfo->products_id, 'product');

if (sizeof($product_categories) > 1) {
  $contents[] = array('text' => '<strong><span class="text-danger">' . TEXT_MASTER_CATEGORIES_ID . '</span>' . '</strong>');
}
for ($i = 0, $n = sizeof($product_categories); $i < $n; $i++) {
  $category_path = '';
  for ($j = 0, $k = sizeof($product_categories[$i]); $j < $k; $j++) {
    $category_path .= $product_categories[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
  }
  $category_path = substr($category_path, 0, -16);
  if (sizeof($product_categories) > 1 && zen_get_parent_category_id($pInfo->products_id) == $product_categories[$i][sizeof($product_categories[$i]) - 1]['id']) {
    $product_categories_string .= '<div class="checkbox">
  <label><strong><span class="text-danger">' . zen_draw_checkbox_field('product_categories[]', $product_categories[$i][sizeof($product_categories[$i]) - 1]['id'], true) . $category_path . '</strong></span></div></label>';
  } else {
    $product_categories_string .= '<div class="checkbox">
  <label><strong>' . zen_draw_checkbox_field('product_categories[]', $product_categories[$i][sizeof($product_categories[$i]) - 1]['id'], true) . $category_path . '</div></label>';
  }
}
$product_categories_string = substr($product_categories_string, 0, -4);

$contents[] = array('text' => $product_categories_string);
$contents[] = array('align' => 'center', 'text' => '<button type="submit" class="btn btn-danger">' . IMAGE_DELETE . '</button> <a href="' . zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>');