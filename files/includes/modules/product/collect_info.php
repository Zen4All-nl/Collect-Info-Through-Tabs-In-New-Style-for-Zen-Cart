<?php
/**
 * @package admin
 * @copyright Copyright 2003-2016 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Author: DrByte  Sun Oct 18 02:03:48 2015 -0400 Modified in v1.5.5 $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

$parameters = array(
  'products_name' => '',
  'products_description' => '',
  'products_url' => '',
  'products_id' => '',
  'products_quantity' => '',
  'products_model' => '',
  'products_image' => '',
  'products_price' => '',
  'products_virtual' => DEFAULT_PRODUCT_PRODUCTS_VIRTUAL,
  'products_weight' => '',
  'products_date_added' => '',
  'products_last_modified' => '',
  'products_date_available' => '',
  'products_status' => '',
  'products_tax_class_id' => DEFAULT_PRODUCT_TAX_CLASS_ID,
  'manufacturers_id' => '',
  'products_quantity_order_min' => '',
  'products_quantity_order_units' => '',
  'products_priced_by_attribute' => '',
  'product_is_free' => '',
  'product_is_call' => '',
  'products_quantity_mixed' => '',
  'product_is_always_free_shipping' => DEFAULT_PRODUCT_PRODUCTS_IS_ALWAYS_FREE_SHIPPING,
  'products_qty_box_status' => PRODUCTS_QTY_BOX_STATUS,
  'products_quantity_order_max' => '0',
  'products_sort_order' => '0',
  'products_discount_type' => '0',
  'products_discount_type_from' => '0',
  'products_price_sorter' => '0',
  'master_categories_id' => '',
  'metatags_title_status' => '',
  'metatags_products_name_status' => '',
  'metatags_model_status' => '',
  'metatags_price_status' => '',
  'metatags_title_tagline_status' => '',
  'metatags_title' => '',
  'metatags_keywords' => '',
  'metatags_description' => ''
);

$pInfo = new objectInfo($parameters);

if (isset($_GET['pID']) && empty($_POST)) {
// check if new meta tags or existing
  $check_meta_tags_description = $db->Execute("select products_id from " . TABLE_META_TAGS_PRODUCTS_DESCRIPTION . " where products_id='" . (int)$_GET['pID'] . "'");
  if ($check_meta_tags_description->RecordCount() <= 0) {
    $product = $db->Execute("select pd.products_name, pd.products_description, pd.products_url,
                                    p.products_id, p.products_quantity, p.products_model,
                                    p.products_image, p.products_price, p.products_virtual, p.products_weight,
                                    p.products_date_added, p.products_last_modified,
                                    date_format(p.products_date_available, '%Y-%m-%d') as
                                    products_date_available, p.products_status, p.products_tax_class_id,
                                    p.manufacturers_id,
                                    p.products_quantity_order_min, p.products_quantity_order_units, p.products_priced_by_attribute,
                                    p.product_is_free, p.product_is_call, p.products_quantity_mixed,
                                    p.product_is_always_free_shipping, p.products_qty_box_status, p.products_quantity_order_max,
                                    p.products_sort_order,
                                    p.products_discount_type, p.products_discount_type_from,
                                    p.products_price_sorter, p.master_categories_id
                             from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd
                             where p.products_id = '" . (int)$_GET['pID'] . "'
                             and p.products_id = pd.products_id
                             and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
  } else {
    $product = $db->Execute("select pd.products_name, pd.products_description, pd.products_url,
                                    p.products_id, p.products_quantity, p.products_model,
                                    p.products_image, p.products_price, p.products_virtual, p.products_weight,
                                    p.products_date_added, p.products_last_modified,
                                    date_format(p.products_date_available, '%Y-%m-%d') as
                                    products_date_available, p.products_status, p.products_tax_class_id,
                                    p.manufacturers_id,
                                    p.products_quantity_order_min, p.products_quantity_order_units, p.products_priced_by_attribute,
                                    p.product_is_free, p.product_is_call, p.products_quantity_mixed,
                                    p.product_is_always_free_shipping, p.products_qty_box_status, p.products_quantity_order_max,
                                    p.products_sort_order,
                                    p.products_discount_type, p.products_discount_type_from,
                                    p.products_price_sorter, p.master_categories_id,
                                    p.metatags_title_status, p.metatags_products_name_status, p.metatags_model_status,
                                    p.metatags_price_status, p.metatags_title_tagline_status,
                                    mtpd.metatags_title, mtpd.metatags_keywords, mtpd.metatags_description
                             from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_META_TAGS_PRODUCTS_DESCRIPTION . " mtpd
                             where p.products_id = '" . (int)$_GET['pID'] . "'
                             and p.products_id = pd.products_id
                             and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                             and p.products_id = mtpd.products_id
                             and mtpd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
  }

  $pInfo->updateObjectInfo($product->fields);
} elseif (zen_not_null($_POST)) {
  $pInfo->updateObjectInfo($_POST);
  $products_name = $_POST['products_name'];
  $products_description = $_POST['products_description'];
  $products_url = $_POST['products_url'];
  $metatags_title = $_POST['metatags_title'];
  $metatags_keywords = $_POST['metatags_keywords'];
  $metatags_description = $_POST['metatags_description'];
}

$category_lookup = $db->Execute("select *
                                 from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd
                                 where c.categories_id ='" . (int)$current_category_id . "'
                                 and c.categories_id = cd.categories_id
                                 and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
if (!$category_lookup->EOF) {
  $cInfo = new objectInfo($category_lookup->fields);
} else {
  $cInfo = new objectInfo(array());
}

$manufacturers_array = array(array(
    'id' => '',
    'text' => TEXT_NONE));
$manufacturers = $db->Execute("SELECT manufacturers_id, manufacturers_name
                               FROM " . TABLE_MANUFACTURERS . "
                               ORDER BY manufacturers_name");
while (!$manufacturers->EOF) {
  $manufacturers_array[] = array(
    'id' => $manufacturers->fields['manufacturers_id'],
    'text' => $manufacturers->fields['manufacturers_name']);
  $manufacturers->MoveNext();
}

$tax_class_array = array(array(
    'id' => '0',
    'text' => TEXT_NONE));
$tax_class = $db->Execute("SELECT tax_class_id, tax_class_title
                           FROM " . TABLE_TAX_CLASS . "
                           ORDER BY tax_class_title");
while (!$tax_class->EOF) {
  $tax_class_array[] = array(
    'id' => $tax_class->fields['tax_class_id'],
    'text' => $tax_class->fields['tax_class_title']);
  $tax_class->MoveNext();
}

$languages = zen_get_languages();

if (!isset($pInfo->products_status)) {
  $pInfo->products_status = '1';
}
switch ($pInfo->products_status) {
  case '0':
    $in_status = false;
    $out_status = true;
    break;
  case '1':
  default:
    $in_status = true;
    $out_status = false;
    break;
}
// set to out of stock if categories_status is off and new product or existing products_status is off
if (zen_get_categories_status($current_category_id) == '0' and $pInfo->products_status != '1') {
  $pInfo->products_status = 0;
  $in_status = false;
  $out_status = true;
}

// Virtual Products
if (!isset($pInfo->products_virtual)) {
  $pInfo->products_virtual = DEFAULT_PRODUCT_PRODUCTS_VIRTUAL;
}
switch ($pInfo->products_virtual) {
  case '0':
    $is_virtual = false;
    $not_virtual = true;
    break;
  case '1':
    $is_virtual = true;
    $not_virtual = false;
    break;
  default:
    $is_virtual = false;
    $not_virtual = true;
}
// Always Free Shipping
if (!isset($pInfo->product_is_always_free_shipping)) {
  $pInfo->product_is_always_free_shipping = DEFAULT_PRODUCT_PRODUCTS_IS_ALWAYS_FREE_SHIPPING;
}
switch ($pInfo->product_is_always_free_shipping) {
  case '0':
    $is_product_is_always_free_shipping = false;
    $not_product_is_always_free_shipping = true;
    $special_product_is_always_free_shipping = false;
    break;
  case '1':
    $is_product_is_always_free_shipping = true;
    $not_product_is_always_free_shipping = false;
    $special_product_is_always_free_shipping = false;
    break;
  case '2':
    $is_product_is_always_free_shipping = false;
    $not_product_is_always_free_shipping = false;
    $special_product_is_always_free_shipping = true;
    break;
  default:
    $is_product_is_always_free_shipping = false;
    $not_product_is_always_free_shipping = true;
    $special_product_is_always_free_shipping = false;
    break;
}
// products_qty_box_status shows
if (!isset($pInfo->products_qty_box_status)) {
  $pInfo->products_qty_box_status = PRODUCTS_QTY_BOX_STATUS;
}
switch ($pInfo->products_qty_box_status) {
  case '0':
    $is_products_qty_box_status = false;
    $not_products_qty_box_status = true;
    break;
  case '1':
    $is_products_qty_box_status = true;
    $not_products_qty_box_status = false;
    break;
  default:
    $is_products_qty_box_status = true;
    $not_products_qty_box_status = false;
}
// Product is Priced by Attributes
if (!isset($pInfo->products_priced_by_attribute)) {
  $pInfo->products_priced_by_attribute = '0';
}
switch ($pInfo->products_priced_by_attribute) {
  case '0':
    $is_products_priced_by_attribute = false;
    $not_products_priced_by_attribute = true;
    break;
  case '1':
    $is_products_priced_by_attribute = true;
    $not_products_priced_by_attribute = false;
    break;
  default:
    $is_products_priced_by_attribute = false;
    $not_products_priced_by_attribute = true;
}
// Product is Free
if (!isset($pInfo->product_is_free)) {
  $pInfo->product_is_free = '0';
}
switch ($pInfo->product_is_free) {
  case '0':
    $in_product_is_free = false;
    $out_product_is_free = true;
    break;
  case '1':
    $in_product_is_free = true;
    $out_product_is_free = false;
    break;
  default:
    $in_product_is_free = false;
    $out_product_is_free = true;
}
// Product is Call for price
if (!isset($pInfo->product_is_call)) {
  $pInfo->product_is_call = '0';
}
switch ($pInfo->product_is_call) {
  case '0':
    $in_product_is_call = false;
    $out_product_is_call = true;
    break;
  case '1':
    $in_product_is_call = true;
    $out_product_is_call = false;
    break;
  default:
    $in_product_is_call = false;
    $out_product_is_call = true;
}
// Products can be purchased with mixed attributes retail
if (!isset($pInfo->products_quantity_mixed)) {
  $pInfo->products_quantity_mixed = '0';
}
switch ($pInfo->products_quantity_mixed) {
  case '0':
    $in_products_quantity_mixed = false;
    $out_products_quantity_mixed = true;
    break;
  case '1':
    $in_products_quantity_mixed = true;
    $out_products_quantity_mixed = false;
    break;
  default:
    $in_products_quantity_mixed = true;
    $out_products_quantity_mixed = false;
}

// metatags_products_name_status shows
if (empty($pInfo->metatags_keywords) && empty($pInfo->metatags_description)) {
  $pInfo->metatags_products_name_status = zen_get_show_product_switch($_GET['pID'], 'metatags_products_name_status');
}
switch ($pInfo->metatags_products_name_status) {
  case '0':
    $is_metatags_products_name_status = false;
    $not_metatags_products_name_status = true;
    break;
  case '1':
    $is_metatags_products_name_status = true;
    $not_metatags_products_name_status = false;
    break;
  default:
    $is_metatags_products_name_status = true;
    $not_metatags_products_name_status = false;
}
// metatags_title_status shows
if (empty($pInfo->metatags_keywords) && empty($pInfo->metatags_description)) {
  $pInfo->metatags_title_status = zen_get_show_product_switch($_GET['pID'], 'metatags_title_status');
}
switch ($pInfo->metatags_title_status) {
  case '0':
    $is_metatags_title_status = false;
    $not_metatags_title_status = true;
    break;
  case '1':
    $is_metatags_title_status = true;
    $not_metatags_title_status = false;
    break;
  default:
    $is_metatags_title_status = true;
    $not_metatags_title_status = false;
}
// metatags_model_status shows
if (empty($pInfo->metatags_keywords) && empty($pInfo->metatags_description)) {
  $pInfo->metatags_model_status = zen_get_show_product_switch($_GET['pID'], 'metatags_model_status');
}
switch ($pInfo->metatags_model_status) {
  case '0':
    $is_metatags_model_status = false;
    $not_metatags_model_status = true;
    break;
  case '1':
    $is_metatags_model_status = true;
    $not_metatags_model_status = false;
    break;
  default:
    $is_metatags_model_status = true;
    $not_metatags_model_status = false;
}
// metatags_price_status shows
if (empty($pInfo->metatags_keywords) && empty($pInfo->metatags_description)) {
  $pInfo->metatags_price_status = zen_get_show_product_switch($_GET['pID'], 'metatags_price_status');
}
switch ($pInfo->metatags_price_status) {
  case '0':
    $is_metatags_price_status = false;
    $not_metatags_price_status = true;
    break;
  case '1':
    $is_metatags_price_status = true;
    $not_metatags_price_status = false;
    break;
  default:
    $is_metatags_price_status = true;
    $not_metatags_price_status = false;
}
// metatags_title_tagline_status shows TITLE and TAGLINE in metatags_header.php
if (empty($pInfo->metatags_keywords) && empty($pInfo->metatags_description)) {
  $pInfo->metatags_title_tagline_status = zen_get_show_product_switch($_GET['pID'], 'metatags_title_tagline_status');
}
switch ($pInfo->metatags_title_tagline_status) {
  case '0':
    $is_metatags_title_tagline_status = false;
    $not_metatags_title_tagline_status = true;
    break;
  case '1':
    $is_metatags_title_tagline_status = true;
    $not_metatags_title_tagline_status = false;
    break;
  default:
    $is_metatags_title_tagline_status = true;
    $not_metatags_title_tagline_status = false;
}
// set image overwrite
$on_overwrite = true;
$off_overwrite = false;
// set image delete
$on_image_delete = false;
$off_image_delete = true;
?>
<script type="text/javascript">
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
      var selected_value = document.forms["new_product"].products_tax_class_id.selectedIndex;
      var parameterVal = document.forms["new_product"].products_tax_class_id[selected_value].value;
      if ((parameterVal > 0) && (tax_rates[parameterVal] > 0)) {
          return tax_rates[parameterVal];
      } else {
          return 0;
      }
  }

  function updateGross() {
      var taxRate = getTaxRate();
      var grossValue = document.forms["new_product"].products_price.value;
      if (taxRate > 0) {
          grossValue = grossValue * ((taxRate / 100) + 1);
      }

      document.forms["new_product"].products_price_gross.value = doRound(grossValue, 4);
  }

  function updateNet() {
      var taxRate = getTaxRate();
      var netValue = document.forms["new_product"].products_price_gross.value;
      if (taxRate > 0) {
          netValue = netValue / ((taxRate / 100) + 1);
      }

      document.forms["new_product"].products_price.value = doRound(netValue, 4);
  }
</script>
<div class="container-fluid">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="col-sm-11"><?php echo sprintf(TEXT_NEW_PRODUCT, zen_output_generated_category_path($current_category_id)); ?></h3>
      <?php echo zen_info_image($cInfo->categories_image, $cInfo->categories_name, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?>
    </div>
    <div class="panel-body">
        <?php
//  echo $type_admin_handler;
        echo zen_draw_form('new_product', $type_admin_handler, 'cPath=' . $cPath . (isset($_GET['product_type']) ? '&product_type=' . $_GET['product_type'] : '') . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '') . '&action=new_product_preview' . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . ( (isset($_GET['search']) && !empty($_GET['search'])) ? '&search=' . $_GET['search'] : '') . ( (isset($_POST['search']) && !empty($_POST['search']) && empty($_GET['search'])) ? '&search=' . $_POST['search'] : ''), 'post', 'enctype="multipart/form-data" class="form-horizontal"');
        ?>
        <?php
        $dir_info = zen_build_subdirectories_array(DIR_FS_CATALOG_IMAGES);
        $default_directory = substr($pInfo->products_image, 0, strpos($pInfo->products_image, '/') + 1);
        ?>
        <?php // BOF show when product is linked ?>
        <table>
        <?php
if (zen_get_product_is_linked($_GET['pID']) == 'true' and $_GET['pID'] > 0) {
?>
          <tr>
            <td class="main"><?php echo TEXT_MASTER_CATEGORIES_ID; ?></td>
            <td class="main">
              <?php
                // echo zen_draw_pull_down_menu('products_tax_class_id', $tax_class_array, $pInfo->products_tax_class_id);
                echo zen_image(DIR_WS_IMAGES . 'icon_yellow_on.gif', IMAGE_ICON_LINKED) . '&nbsp;&nbsp;';
                echo zen_draw_pull_down_menu('master_category', zen_get_master_categories_pulldown($_GET['pID']), $pInfo->master_categories_id); ?>
            </td>
          </tr>
<?php } else { ?>
          <tr>
            <td class="main"><?php echo TEXT_MASTER_CATEGORIES_ID; ?></td>
            <td class="main"><?php echo TEXT_INFO_ID . ($_GET['pID'] > 0 ? $pInfo->master_categories_id  . ' ' . zen_get_category_name($pInfo->master_categories_id, $_SESSION['languages_id']) : $current_category_id  . ' ' . zen_get_category_name($current_category_id, $_SESSION['languages_id'])); ?></td>
          </tr>
<?php } ?>
          <tr>
            <td colspan="2" class="main"><?php echo TEXT_INFO_MASTER_CATEGORIES_ID; ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_trans.gif', '100%', '2'); ?></td>
          </tr>
          </table>
          <?php // BOF show when product is linked ?>
      <ul class="nav nav-tabs" data-tabs="tabs">
        <li class="active">
          <a data-toggle="tab" href="#productTabs1">General</a>
        </li>
        <li>
          <a data-toggle="tab" href="#productTabs2">Data</a>
        </li>
        <li>
          <a data-toggle="tab" href="#productTabs3">Links</a>
        </li>
        <li>
          <a data-toggle="tab" href="#productTabs4">Image</a>
        </li>
        <?php
        $extraTabTitles = dirList(DIR_WS_MODULES . 'extra_tabs/', 'tab_title_collect_info.php');
        $i = 4;
        if (isset($extraTabTitles) && $extraTabTitles != '') {
          foreach ($extraTabTitles as $tabTitle) {
            ?>
            <li>
              <a data-toggle="tab" href="#productTabs<?php echo $i + 1; ?>"><?php include($tabTitle); ?></a>
            </li>
            <?php
            $i++;
          }
        }
        ?>
      </ul>
      <div class="tab-content">
        <div id="productTabs1" class="tab-pane fade in active">
          <ul class="nav nav-tabs" data-tabs="tabs">
              <?php
              for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                ?>
              <li <?php if ($i == 0) echo 'class="active"'; ?>>
                <a data-toggle="tab" href="#productNameTabs<?php echo $i + 1; ?>">
                    <?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . $languages[$i]['name']; ?>
                </a>
              </li>
            <?php } ?>
          </ul>
          <div class="tab-content">
              <?php
              for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                ?>
              <div class="tab-pane fade in <?php if ($i == 0) echo 'active'; ?>" id="productNameTabs<?php echo ($i + 1); ?>">
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_NAME, 'products_name[' . $languages[$i]['id'] . ']', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9">
                      <?php echo zen_draw_input_field('products_name[' . $languages[$i]['id'] . ']', htmlspecialchars(isset($products_name[$languages[$i]['id']]) ? stripslashes($products_name[$languages[$i]['id']]) : zen_get_products_name($pInfo->products_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), zen_set_field_length(TABLE_PRODUCTS_DESCRIPTION, 'products_name') . ' class="form-control"'); ?>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_DESCRIPTION, 'products_description[' . $languages[$i]['id'] . ']', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9">
                      <?php echo zen_draw_textarea_field('products_description[' . $languages[$i]['id'] . ']', 'soft', '100%', '30', htmlspecialchars((isset($products_description[$languages[$i]['id']])) ? stripslashes($products_description[$languages[$i]['id']]) : zen_get_products_description($pInfo->products_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), 'class="form-control ckeditor"'); ?>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_META_TAGS_TITLE, 'metatags_title[' . $languages[$i]['id'] . ']', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9">
                      <?php echo zen_draw_input_field('metatags_title[' . $languages[$i]['id'] . ']', htmlspecialchars(isset($metatags_title[$languages[$i]['id']]) ? stripslashes($metatags_title[$languages[$i]['id']]) : zen_get_metatags_title($pInfo->products_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), zen_set_field_length(TABLE_META_TAGS_PRODUCTS_DESCRIPTION, 'metatags_title', '150', false) . 'class="form-control"'); ?>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_META_TAGS_KEYWORDS, 'metatags_keywords', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9">
                      <?php echo zen_draw_textarea_field('metatags_keywords[' . $languages[$i]['id'] . ']', 'soft', '100%', '10', htmlspecialchars((isset($metatags_keywords[$languages[$i]['id']])) ? stripslashes($metatags_keywords[$languages[$i]['id']]) : zen_get_metatags_keywords($pInfo->products_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), 'class="form-control noEditor"'); ?>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_META_TAGS_DESCRIPTION, 'metatags_description', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9">
                      <?php echo zen_draw_textarea_field('metatags_description[' . $languages[$i]['id'] . ']', 'soft', '100%', '10', htmlspecialchars((isset($metatags_description[$languages[$i]['id']])) ? stripslashes($metatags_description[$languages[$i]['id']]) : zen_get_metatags_description($pInfo->products_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), 'class="form-control noEditor"'); ?>
                  </div>
                </div>
              </div>
              <?php
            }
            ?>
          </div>
          <div class="form-group">
            <span>
                <?php echo TEXT_META_TAG_TITLE_INCLUDES; ?>
            </span>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_METATAGS_PRODUCTS_NAME_STATUS, 'metatags_products_name_status', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
                <?php echo '<label class="radio-inline">' . zen_draw_radio_field('metatags_products_name_status', '1', $is_metatags_products_name_status) . '&nbsp;' . TEXT_YES . '</label><label class="radio-inline">' . zen_draw_radio_field('metatags_products_name_status', '0', $not_metatags_products_name_status) . '&nbsp;' . TEXT_NO . '</label>'; ?>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_METATAGS_TITLE_STATUS, 'metatags_title_status', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
                <?php echo '<label class="radio-inline">' . zen_draw_radio_field('metatags_title_status', '1', $is_metatags_title_status) . '&nbsp;' . TEXT_YES . '</label><label class="radio-inline">' . zen_draw_radio_field('metatags_title_status', '0', $not_metatags_title_status) . '&nbsp;' . TEXT_NO . '</label>'; ?>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_METATAGS_MODEL_STATUS, 'metatags_model_status', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
                <?php echo '<label class="radio-inline">' . zen_draw_radio_field('metatags_model_status', '1', $is_metatags_model_status) . '&nbsp;' . TEXT_YES . '</label><label class="radio-inline">' . zen_draw_radio_field('metatags_model_status', '0', $not_metatags_model_status) . '&nbsp;' . TEXT_NO . '</label>'; ?>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_METATAGS_PRICE_STATUS, 'metatags_price_status', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
                <?php echo '<label class="radio-inline">' . zen_draw_radio_field('metatags_price_status', '1', $is_metatags_price_status) . '&nbsp;' . TEXT_YES . '</label><label class="radio-inline">' . zen_draw_radio_field('metatags_price_status', '0', $not_metatags_price_status) . '&nbsp;' . TEXT_NO . '</label>'; ?>
            </div>
          </div>
          <div class="form-group">
            <?php echo zen_draw_label(TEXT_PRODUCTS_METATAGS_TITLE_TAGLINE_STATUS, 'metatags_title_tagline_status', 'class="col-sm-3 control-label"'); ?> <i class="fa fa-lg fa-question-circle" data-toggle="tooltip" data-placement="top" title="<?php echo TEXT_INFO_META_TAGS_USAGE; ?>"></i>
            <div class="col-sm-9">
                <?php echo '<label class="radio-inline">' . zen_draw_radio_field('metatags_title_tagline_status', '1', $is_metatags_title_tagline_status) . '&nbsp;' . TEXT_YES . '</label><label class="radio-inline">' . zen_draw_radio_field('metatags_title_tagline_status', '0', $not_metatags_title_tagline_status) . '&nbsp;' . TEXT_NO . '</label>'; ?>
            </div>
          </div>
        </div>
        <div id="productTabs2" class="tab-pane fade">
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_MODEL, 'products_model', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
                <?php echo zen_draw_input_field('products_model', htmlspecialchars(stripslashes($pInfo->products_model), ENT_COMPAT, CHARSET, TRUE), zen_set_field_length(TABLE_PRODUCTS, 'products_model') . 'class="form-control"'); ?>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_TAX_CLASS, 'products_tax_class_id', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
                <?php echo zen_draw_pull_down_menu('products_tax_class_id', $tax_class_array, $pInfo->products_tax_class_id, 'onchange="updateGross()" class="form-control"'); ?>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_PRICE_NET, 'products_price', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
                <?php echo zen_draw_input_field('products_price', $pInfo->products_price, 'onKeyUp="updateGross()" class="form-control"'); ?>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_PRICE_GROSS, 'products_price_gross', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
                <?php echo zen_draw_input_field('products_price_gross', $pInfo->products_price, 'OnKeyUp="updateNet()" class="form-control"'); ?>
            </div>
          </div>
          <script type="text/javascript">updateGross();</script>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_STATUS, 'products_status', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
              <?php echo '<label class="radio-inline">' . zen_draw_radio_field('products_status', '1', $in_status) . '&nbsp;' . TEXT_PRODUCT_AVAILABLE . '</label><label class="radio-inline">' . zen_draw_radio_field('products_status', '0', $out_status) . '&nbsp;' . TEXT_PRODUCT_NOT_AVAILABLE . '</label>'; ?>            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_QUANTITY, 'products_quantity', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
                <?php echo zen_draw_input_field('products_quantity', $pInfo->products_quantity, 'class="form-control"'); ?>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_DATE_AVAILABLE, 'products_date_available', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-3">
              <div class="input-group date" id="datepicker">
                <?php echo zen_draw_input_field('products_date_available', $pInfo->products_date_available, 'date-date-format="YYYY-MM-DD" class="form-control"'); ?><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span>
              </div>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCT_IS_FREE, 'product_is_free', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
                <?php echo '<label class="radio-inline">' . zen_draw_radio_field('product_is_free', '1', ($in_product_is_free == 1)) . '&nbsp;' . TEXT_YES . '</label><label class="radio-inline">' . zen_draw_radio_field('product_is_free', '0', ($in_product_is_free == 0)) . '&nbsp;' . TEXT_NO . ' ' . ($pInfo->product_is_free == 1 ? '</label><span class="errorText">' . TEXT_PRODUCTS_IS_FREE_EDIT . '</span>' : ''); ?>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCT_IS_CALL, 'product_is_call', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
                <?php echo '<label class="radio-inline">' . zen_draw_radio_field('product_is_call', '1', ($in_product_is_call == 1)) . '&nbsp;' . TEXT_YES . '</label><label class="radio-inline">' . zen_draw_radio_field('product_is_call', '0', ($in_product_is_call == 0)) . '&nbsp;' . TEXT_NO . ' ' . ($pInfo->product_is_call == 1 ? '</label><span class="errorText">' . TEXT_PRODUCTS_IS_CALL_EDIT . '</span>' : ''); ?>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_PRICED_BY_ATTRIBUTES, 'products_priced_by_attribute', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
                <?php echo '<label class="radio-inline">' . zen_draw_radio_field('products_priced_by_attribute', '1', $is_products_priced_by_attribute) . '&nbsp;' . TEXT_PRODUCT_IS_PRICED_BY_ATTRIBUTE . '</label><label class="radio-inline">' . zen_draw_radio_field('products_priced_by_attribute', '0', $not_products_priced_by_attribute) . '&nbsp;' . TEXT_PRODUCT_NOT_PRICED_BY_ATTRIBUTE . ' ' . ($pInfo->products_priced_by_attribute == 1 ? '</label><span class="errorText">' . TEXT_PRODUCTS_PRICED_BY_ATTRIBUTES_EDIT . '</span>' : ''); ?>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_VIRTUAL, 'products_virtual', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
                <?php echo '<label class="radio-inline">' . zen_draw_radio_field('products_virtual', '1', $is_virtual) . '&nbsp;' . TEXT_PRODUCT_IS_VIRTUAL . '</label><label class="radio-inline">' . zen_draw_radio_field('products_virtual', '0', $not_virtual) . '&nbsp;' . TEXT_PRODUCT_NOT_VIRTUAL . ' ' . ($pInfo->products_virtual == 1 ? '</label><span class="errorText">' . TEXT_VIRTUAL_EDIT . '</span>' : ''); ?>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_IS_ALWAYS_FREE_SHIPPING, 'product_is_always_free_shipping', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
                <?php echo '<label class="radio-inline">' . zen_draw_radio_field('product_is_always_free_shipping', '1', $is_product_is_always_free_shipping) . '&nbsp;' . TEXT_PRODUCT_IS_ALWAYS_FREE_SHIPPING . '</label><label class="radio-inline">' . zen_draw_radio_field('product_is_always_free_shipping', '0', $not_product_is_always_free_shipping) . '&nbsp;' . TEXT_PRODUCT_NOT_ALWAYS_FREE_SHIPPING . '</label><label class="radio-inline">' . zen_draw_radio_field('product_is_always_free_shipping', '2', $special_product_is_always_free_shipping) . '&nbsp;' . TEXT_PRODUCT_SPECIAL_ALWAYS_FREE_SHIPPING . ' ' . ($pInfo->product_is_always_free_shipping == 1 ? '</label><span class="errorText">' . TEXT_FREE_SHIPPING_EDIT . '</span>' : ''); ?>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_QTY_BOX_STATUS, 'products_qty_box_status', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
                <?php echo '<label class="radio-inline">' . zen_draw_radio_field('products_qty_box_status', '1', $is_products_qty_box_status) . '&nbsp;' . TEXT_PRODUCTS_QTY_BOX_STATUS_ON . '</label><label class="radio-inline">' . zen_draw_radio_field('products_qty_box_status', '0', $not_products_qty_box_status) . '&nbsp;' . TEXT_PRODUCTS_QTY_BOX_STATUS_OFF . ' ' . ($pInfo->products_qty_box_status == 0 ? '</label><span class="errorText">' . TEXT_PRODUCTS_QTY_BOX_STATUS_EDIT . '</span>' : ''); ?>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_QUANTITY_MIN_RETAIL, 'products_quantity_order_min', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
                <?php echo zen_draw_input_field('products_quantity_order_min', ($pInfo->products_quantity_order_min == 0 ? 1 : $pInfo->products_quantity_order_min), 'class="form-control"'); ?>
            </div>
          </div>
          <div class="form-group">
            <?php echo zen_draw_label(TEXT_PRODUCTS_QUANTITY_MAX_RETAIL, 'products_quantity_order_max', 'class="col-sm-3 control-label"'); ?> <i class="fa fa-lg fa-question-circle" data-toggle="tooltip" data-placement="top" title="<?php echo TEXT_PRODUCTS_QUANTITY_MAX_RETAIL_EDIT; ?>"></i>
            <div class="col-sm-9">
                <?php echo zen_draw_input_field('products_quantity_order_max', $pInfo->products_quantity_order_max, 'class="form-control"'); ?>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_QUANTITY_UNITS_RETAIL, 'products_quantity_order_units', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
                <?php echo zen_draw_input_field('products_quantity_order_units', ($pInfo->products_quantity_order_units == 0 ? 1 : $pInfo->products_quantity_order_units), 'class="form-control"'); ?>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_MIXED, 'products_quantity_mixed', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
              <label class="radio-inline">
                  <?php echo zen_draw_radio_field('products_quantity_mixed', '1', $in_products_quantity_mixed) . '&nbsp;' . TEXT_YES; ?>
              </label>
              <label class="radio-inline"><?php echo zen_draw_radio_field('products_quantity_mixed', '0', $out_products_quantity_mixed) . '&nbsp;' . TEXT_NO; ?>
              </label>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_WEIGHT, 'products_weight', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
                <?php echo zen_draw_input_field('products_weight', $pInfo->products_weight, 'class="form-control"'); ?>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_SORT_ORDER, 'products_sort_order', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
                <?php echo zen_draw_input_field('products_sort_order', $pInfo->products_sort_order, 'class="form-control"'); ?>
            </div>
          </div>
        </div>
        <div id="productTabs3" class="tab-pane fade">
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_MANUFACTURER, 'manufacturers_id', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
                <?php echo zen_draw_pull_down_menu('manufacturers_id', $manufacturers_array, $pInfo->manufacturers_id, 'class="form-control"'); ?>
            </div>
          </div>
          <?php
          for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            ?>
            <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_URL, 'products_url[' . $languages[$i]['id'] . ']', 'class="col-sm-3 control-label"'); ?> <i class="fa fa-lg fa-question-circle" data-toggle="tooltip" data-placement="top" title="<?php echo TEXT_PRODUCTS_URL_WITHOUT_HTTP; ?>"></i>
              <div class="col-sm-9">
                  <?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . zen_draw_input_field('products_url[' . $languages[$i]['id'] . ']', htmlspecialchars(isset($products_url[$languages[$i]['id']]) ? $products_url[$languages[$i]['id']] : zen_get_products_url($pInfo->products_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), zen_set_field_length(TABLE_PRODUCTS_DESCRIPTION, 'products_url') . 'class="form-control"'); ?>
              </div>
            </div>
            <?php
          }
          ?>
        </div>
        <div id="productTabs4" class="tab-pane fade">
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
              <thead>
                <tr>
                  <td class="text-left"><?php echo TEXT_PRODUCTS_IMAGE; ?></td>
                  <td class="text-left"><?php echo TEXT_IMAGE_CURRENT; ?></td>
                  <td class="text-right"><?php echo TEXT_ACTION; ?></td>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="text-left">
                    <a href="#" id="imagePopUp">
                        <?php echo zen_image(DIR_WS_CATALOG_IMAGES . $pInfo->products_image, '', SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'class="img-thumbnail" id="mainImage"'); ?>
                      <br/>
                      <?php echo CLICK_TO_ENLARGE; ?>
                    </a>
                    <?php echo zen_draw_hidden_field('products_image', $pInfo->products_image); ?>
                  </td>
                  <td class="text-left">
                      <?php echo ($pInfo->products_image != '' ? $pInfo->products_image : NONE); ?>
                  </td>
                  <td class="text-left">
                    <div class="form-group">
                      <div class="col-sm-12">
                          <?php echo zen_draw_file_field('products_image', '', 'class="form-control col-sm-12"') . zen_draw_hidden_field('products_previous_image', $pInfo->products_image); ?>
                      </div>
                    </div>
                    <div class="form-group">
                        <?php echo zen_draw_label(TEXT_PRODUCTS_IMAGE_DIR, 'img_dir', 'class="col-sm-3 control-label"'); ?>
                      <div class="col-sm-9">
                          <?php echo zen_draw_pull_down_menu('img_dir', $dir_info, $default_directory, 'class="form-control"'); ?>
                      </div>
                    </div>
                    <div class="form-group">
                        <?php echo zen_draw_label(TEXT_IMAGES_DELETE, 'image_delete', 'class="col-sm-3 control-label"'); ?>
                      <i class="fa fa-question-circle fa-lg" data-toggle="tooltip" data-placement="top" title data-original-title="<?php echo TEXT_IMAGES_DELETE_NOTE; ?>"></i>
                      <div class="col-sm-9">
                        <label class="radio-inline">
                            <?php echo zen_draw_radio_field('image_delete', '0', $off_image_delete) . '&nbsp;' . TABLE_HEADING_NO; ?>
                        </label>
                        <label class="radio-inline">
                            <?php echo zen_draw_radio_field('image_delete', '1', $on_image_delete) . '&nbsp;' . TABLE_HEADING_YES; ?>
                        </label>
                      </div>
                    </div>
                    <div class="form-group">
                        <?php echo zen_draw_label(TEXT_IMAGES_OVERWRITE, 'overwrite', 'class="col-sm-3 control-label"'); ?>
                      <div class="col-sm-9">
                        <label class="radio-inline">
                            <?php echo zen_draw_radio_field('overwrite', '0', $off_overwrite) . '&nbsp;' . TABLE_HEADING_NO; ?>
                        </label>
                        <label class="radio-inline">
                            <?php echo zen_draw_radio_field('overwrite', '1', $on_overwrite) . '&nbsp;' . TABLE_HEADING_YES; ?>
                        </label>
                      </div>
                    </div>
                    <div class="form-group">
                        <?php echo zen_draw_label(TEXT_PRODUCTS_IMAGE_MANUAL, 'products_image_manual', 'class="col-sm-3 control-label"'); ?>
                      <div class="col-sm-9">
                          <?php echo zen_draw_input_field('products_image_manual', '', 'class="form-control"'); ?>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- /* BOF future code */ -->
          <!--
            <div class="table-responsive">
              <table class="table table-striped table-bordered table-hover">
                <thead>
                  <tr>
                    <td class="text-left">Additional images</td>
                    <td class="text-left">Image name</td>
                    <td class="text-right">Actions</td>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="text-left">
          <?php echo zen_image(DIR_WS_CATALOG_IMAGES . $pInfo->products_image, '', '', '', 'class="img-thumbnail"'); ?>
                    </td>
                    <td class="text-left">
          <?php echo $pInfo->products_image; ?>
                    </td>
                    <td class="text-right">
                      <div class="btn-group">
                        <button type="button" id="button-edit-additional-image-1" class="btn btn-primary" data-original-title="<?php echo TEXT_CHANGE_IMAGE; ?>"><i class="fa fa-pencil"></i></button> <button type="button" id="button-delete-additional-image-1" class="btn btn-danger" data-original-title="<?php ?>"><i class="fa fa-trash-o"></i></button>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left">
                    </td>
                    <td class="text-left">
      
                    </td>
                    <td class="text-right">
                      <div class="btn-group">
                        <button type="button" id="button-add-additional-image-1" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          -->
          <!-- /* EOF future code */ -->
        </div>
        <?php
        $extraTabsContents = dirList(DIR_WS_MODULES . 'extra_tabs/', 'tab_contents_collect_info.php');
        $j = 4;
        if (isset($extraTabsContents) && $extraTabsContents != '') {
          foreach ($extraTabsContents as $tabContent) {
            ?>
            <div id="productTabs<?php echo ($j + 1); ?>" class="tab-pane fade">
                <?php include($tabContent); ?>
            </div>
            <?php
            $j++;
          }
        }
        ?>
      </div>

      <span>
          <?php
// hidden fields not changeable on products page
          echo zen_draw_hidden_field('master_categories_id', $pInfo->master_categories_id);
          if (array_search('discounts', $extraTabsContents)) {
            echo zen_draw_hidden_field('products_discount_type', $pInfo->products_discount_type);
            echo zen_draw_hidden_field('products_discount_type_from', $pInfo->products_discount_type_from);
          }
          echo zen_draw_hidden_field('products_price_sorter', $pInfo->products_price_sorter);
          echo zen_draw_hidden_field('products_date_added', (zen_not_null($pInfo->products_date_added) ? $pInfo->products_date_added : date('Y-m-d')));
          echo ( (isset($_GET['search']) && !empty($_GET['search'])) ? zen_draw_hidden_field('search', $_GET['search']) : '');
          echo ( (isset($_POST['search']) && !empty($_POST['search']) && empty($_GET['search'])) ? zen_draw_hidden_field('search', $_POST['search']) : '');
          ?>

      </span>

      <div class="btn-group">
        <a id="previewPopUp" class="btn btn-default" name="btnpreview" href="#">
          <i class="fa fa-tv"></i> Preview 
        </a>
        <button type="submit" class="btn btn-primary" id="btnsubmit" name="btnsubmit">
          <i class="fa fa-save"></i> Save
        </button>
        <a href="<?php echo zen_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '') . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . ( (isset($_GET['search']) && !empty($_GET['search'])) ? '&search=' . $_GET['search'] : '') . ( (isset($_POST['search']) && !empty($_POST['search']) && empty($_GET['search'])) ? '&search=' . $_POST['search'] : '')); ?>" class="btn btn-warning" id="btncancel" name="btncancel"><i class="fa fa-undo"></i> Back </a>
      </div>
      <?php echo'</form>'; ?>
    </div>
  </div>
</div>
<!-- Creates the bootstrap modal where the image will appear -->
<?php list($width, $height) = getimagesize(DIR_FS_CATALOG_IMAGES . $pInfo->products_image);
?>
<div id="imageModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="imageModalLabel">Image preview</h4>
      </div>
      <div class="modal-body">
          <?php echo zen_image(DIR_WS_CATALOG_IMAGES . $pInfo->products_image, '', $width, $height) ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Product preview modal-->
<?php include DIR_WS_MODULES . 'product/preview_modal.php'; ?>
<!-- script for datepicker -->
<script>
  $(function () {
      $('#datepicker').datetimepicker({
          format: 'YYYY-MM-DD',
          locale: '<?php echo $_SESSION['languages_code']; ?>',
          showTodayButton: true,
          keepOpen: true
      });
  });
</script>
<!-- script for tooltips -->
<script>
  $(document).ready(function () {
      $('[data-toggle="tooltip"]').tooltip();
  });
</script>
<!-- script for image popup -->
<script>
  $("#imagePopUp").on("click", function () {
      $('#imageModal').modal('show');
  });
</script>
<!-- script for preview popup -->
<script>
  $('#previewPopUp').on('click', function () {
      $('#previewmodal').modal('show');
  });
</script>