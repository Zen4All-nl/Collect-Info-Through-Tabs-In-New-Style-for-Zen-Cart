<?php

/**
 * @package admin
 * @copyright (c) 2008-2017, Zen4All
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: z4a_delete_product.php Zen4All
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

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
