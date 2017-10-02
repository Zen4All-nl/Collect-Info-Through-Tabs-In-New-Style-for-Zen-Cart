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

// search directories for the needed files
function recursiveDirList($dir, $prefix = '') {
  $dir = rtrim($dir, '/');
  $result = array();

  foreach (glob("$dir/*", GLOB_MARK) as &$f) {
    if (substr($f, -1) === '/') {
      $result = array_merge($result, recursiveDirList($f, $prefix . basename($f) . '/'));
    } else {
      $result[] = $prefix . basename($f);
    }
  }

  return $result;
}

$extraTabsPath = DIR_WS_MODULES . 'extra_tabs';
$extraTabsFiles = recursiveDirList($extraTabsPath);

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
foreach ($manufacturers as $manufacturer) {
  $manufacturers_array[] = [
    'id' => $manufacturer['manufacturers_id'],
    'text' => $manufacturer['manufacturers_name']
  ];
}

$tax_class_array = array(array(
    'id' => '0',
    'text' => TEXT_NONE));
$tax_class = $db->Execute("SELECT tax_class_id, tax_class_title
                           FROM " . TABLE_TAX_CLASS . "
                           ORDER BY tax_class_title");
foreach ($tax_class as $item) {
  $tax_class_array[] = [
    'id' => $item['tax_class_id'],
    'text' => $item['tax_class_title']];
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
if (zen_get_categories_status($current_category_id) == '0' && $pInfo->products_status != '1') {
  $pInfo->products_status = 0;
  $in_status = false;
  $out_status = true;
}

// Virtual Products
if (!isset($pInfo->products_virtual)) {
  $pInfo->products_virtual = DEFAULT_PRODUCT_PRODUCTS_VIRTUAL;
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
      <ul class="nav nav-tabs" data-tabs="tabs">
        <li class="active">
          <a data-toggle="tab" href="#productTabs1">General</a>
        </li>
        <li>
          <a data-toggle="tab" href="#productTabs2">Data</a>
        </li>
        <li>
          <a data-toggle="tab" href="#productTabs3">Manufacturer</a>
        </li>
        <li>
          <a data-toggle="tab" href="#productTabs4">Image</a>
        </li>
        <?php
        $tabTitleNeedle = 'tab_title_';
        $i = 4;
        if (isset($extraTabsFiles) && $extraTabsFiles != '') {
          foreach ($extraTabsFiles as $tabTitle) {
            if (strpos($tabTitle, $tabTitleNeedle) !== false) {
              ?>
              <li>
                <a data-toggle="tab" href="#productTabs<?php echo $i + 1; ?>"><?php include(DIR_WS_MODULES . 'extra_tabs/' . $tabTitle); ?></a>
              </li>
              <?php
              $i++;
            }
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
              <div class="tab-pane fade in <?php if ($i == 0) echo 'active'; ?>" <?php echo 'id="productNameTabs' . ($i + 1) . '"'; ?>>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_NAME, 'products_name[' . $languages[$i]['id'] . ']', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9">
                      <?php echo zen_draw_input_field('products_name[' . $languages[$i]['id'] . ']', htmlspecialchars(isset($products_name[$languages[$i]['id']]) ? stripslashes($products_name[$languages[$i]['id']]) : zen_get_products_name($pInfo->products_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), zen_set_field_length(TABLE_PRODUCTS_DESCRIPTION, 'products_name') . ' class="form-control"'); ?>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_DESCRIPTION, 'products_description[' . $languages[$i]['id'] . ']', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9">
                      <?php echo zen_draw_textarea_field('products_description[' . $languages[$i]['id'] . ']', 'soft', '100%', '30', htmlspecialchars((isset($products_description[$languages[$i]['id']])) ? stripslashes($products_description[$languages[$i]['id']]) : zen_get_products_description($pInfo->products_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), 'class="editorHook form-control"'); ?>
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
                      <?php echo zen_draw_textarea_field('metatags_keywords[' . $languages[$i]['id'] . ']', 'soft', '100%', '10', htmlspecialchars((isset($metatags_keywords[$languages[$i]['id']])) ? stripslashes($metatags_keywords[$languages[$i]['id']]) : zen_get_metatags_keywords($pInfo->products_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), 'class="noEditor form-control"'); ?>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_META_TAGS_DESCRIPTION, 'metatags_description', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9">
                      <?php echo zen_draw_textarea_field('metatags_description[' . $languages[$i]['id'] . ']', 'soft', '100%', '10', htmlspecialchars((isset($metatags_description[$languages[$i]['id']])) ? stripslashes($metatags_description[$languages[$i]['id']]) : zen_get_metatags_description($pInfo->products_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), 'class="noEditor form-control"'); ?>
                  </div>
                </div>
              </div>
              <?php
            }
            ?>
          </div>
          <div class="form-group">
            <label class="control-label">
                <?php echo TEXT_META_TAG_TITLE_INCLUDES; ?>
            </label>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_METATAGS_PRODUCTS_NAME_STATUS, 'metatags_products_name_status', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
              <div class="input-group">
                <div class="radioBtn btn-group">
                  <a class="btn btn-info <?php echo($is_metatags_products_name_status == true ? 'active' : 'notActive'); ?>" data-toggle="metatags_products_name_status" data-title="1"><?php echo TEXT_YES; ?></a>
                  <a class="btn btn-info <?php echo($not_metatags_products_name_status == true ? 'active' : 'notActive'); ?>" data-toggle="metatags_products_name_status" data-title="0"><?php echo TEXT_NO; ?></a>
                </div>
                <?php echo zen_draw_hidden_field('metatags_products_name_status', ($is_metatags_products_name_status == true ? '1' : '0'), 'class="metatags_products_name_status"'); ?>
              </div>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_METATAGS_TITLE_STATUS, 'metatags_title_status', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
              <div class="input-group">
                <div class="radioBtn btn-group">
                  <a class="btn btn-info <?php echo($is_metatags_title_status == true ? 'active' : 'notActive'); ?>" data-toggle="metatags_title_status" data-title="1"><?php echo TEXT_YES; ?></a>
                  <a class="btn btn-info <?php echo($not_metatags_title_status == true ? 'active' : 'notActive'); ?>" data-toggle="metatags_title_status" data-title="0"><?php echo TEXT_NO; ?></a>
                </div>
                <?php echo zen_draw_hidden_field('metatags_title_status', ($is_metatags_title_status == true ? '1' : '0'), 'class="metatags_title_status"'); ?>
              </div>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_METATAGS_MODEL_STATUS, 'metatags_model_status', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
              <div class="input-group">
                <div class="radioBtn btn-group">
                  <a class="btn btn-info <?php echo($is_metatags_model_status == true ? 'active' : 'notActive'); ?>" data-toggle="metatags_model_status" data-title="1"><?php echo TEXT_YES; ?></a>
                  <a class="btn btn-info <?php echo($not_metatags_model_status == true ? 'active' : 'notActive'); ?>" data-toggle="metatags_model_status" data-title="0"><?php echo TEXT_NO; ?></a>
                </div>
                <?php echo zen_draw_hidden_field('metatags_model_status', ($is_metatags_model_status == true ? '1' : '0'), 'class="metatags_model_status"'); ?>
              </div>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_METATAGS_PRICE_STATUS, 'metatags_price_status', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
              <div class="input-group">
                <div class="radioBtn btn-group">
                  <a class="btn btn-info <?php echo($is_metatags_price_status == true ? 'active' : 'notActive'); ?>" data-toggle="metatags_price_status" data-title="1"><?php echo TEXT_YES; ?></a>
                  <a class="btn btn-info <?php echo($not_metatags_price_status == true ? 'active' : 'notActive'); ?>" data-toggle="metatags_price_status" data-title="0"><?php echo TEXT_NO; ?></a>
                </div>
                <?php echo zen_draw_hidden_field('metatags_price_status', ($is_metatags_price_status == true ? '1' : '0'), 'class="metatags_price_status"'); ?>
              </div>
            </div>
          </div>
          <div class="form-group">
            <?php echo zen_draw_label(TEXT_PRODUCTS_METATAGS_TITLE_TAGLINE_STATUS, 'metatags_title_tagline_status', 'class="col-sm-3 control-label"'); ?> <i class="fa fa-lg fa-question-circle" data-toggle="tooltip" data-placement="top" title="<?php echo TEXT_INFO_META_TAGS_USAGE; ?>"></i>
            <div class="col-sm-9">
              <div class="input-group">
                <div class="radioBtn btn-group">
                  <a class="btn btn-info <?php echo($is_metatags_title_tagline_status == true ? 'active' : 'notActive'); ?>" data-toggle="metatags_title_tagline_status" data-title="1"><?php echo TEXT_YES; ?></a>
                  <a class="btn btn-info <?php echo($not_metatags_title_tagline_status == true ? 'active' : 'notActive'); ?>" data-toggle="metatags_title_tagline_status" data-title="0"><?php echo TEXT_NO; ?></a>
                </div>
                <?php echo zen_draw_hidden_field('metatags_title_tagline_status', ($is_metatags_title_tagline_status == true ? '1' : '0'), 'class="metatags_title_tagline_status"'); ?>
              </div>
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
              <div class="input-group">
                <div class="radioBtn btn-group">
                  <a class="btn btn-info <?php echo($in_status == true ? 'active' : 'notActive'); ?>" data-toggle="products_status" data-title="1"><?php echo TEXT_PRODUCT_AVAILABLE; ?></a>
                  <a class="btn btn-info <?php echo($out_status == true ? 'active' : 'notActive'); ?>" data-toggle="products_status" data-title="0"><?php echo TEXT_PRODUCT_NOT_AVAILABLE; ?></a>
                  <?php echo (zen_get_categories_status($current_category_id) == '0' ? TEXT_CATEGORIES_STATUS_INFO_OFF : '') . ($out_status == true ? ' ' . TEXT_PRODUCTS_STATUS_INFO_OFF : ''); ?>
                </div>
                <?php echo zen_draw_hidden_field('products_status', ($in_status == true ? '1' : '0'), 'class="products_status"'); ?>
              </div>
            </div>
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
              <div class="date" id="datepicker">
                <?php echo zen_draw_input_field('products_date_available', $pInfo->products_date_available, 'class="form-control"'); ?><i class="fa fa-calendar fa-lg"></i>
              </div>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCT_IS_FREE, 'product_is_free', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
              <div class="input-group">
                <div class="radioBtn btn-group">
                  <a class="btn btn-info <?php echo($in_product_is_free == true ? 'active' : 'notActive'); ?>" data-toggle="product_is_free" data-title="1"><?php echo TEXT_YES; ?></a>
                  <a class="btn btn-info <?php echo($out_product_is_free == true ? 'active' : 'notActive'); ?>" data-toggle="product_is_free" data-title="0"><?php echo TEXT_NO; ?></a>
                </div>
                <?php echo zen_draw_hidden_field('product_is_free', ($in_product_is_free == true ? '1' : '0'), 'class="product_is_free"'); ?>
              </div>
              <?php echo ($pInfo->product_is_free == 1 ? '<span class="errorText">' . TEXT_PRODUCTS_IS_FREE_EDIT . '</span>' : ''); ?>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCT_IS_CALL, 'product_is_call', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
              <div class="input-group">
                <div class="radioBtn btn-group">
                  <a class="btn btn-info <?php echo($in_product_is_call == true ? 'active' : 'notActive'); ?>" data-toggle="product_is_call" data-title="1"><?php echo TEXT_YES; ?></a>
                  <a class="btn btn-info <?php echo($out_product_is_call == true ? 'active' : 'notActive'); ?>" data-toggle="product_is_call" data-title="0"><?php echo TEXT_NO; ?></a>
                </div>
                <?php echo zen_draw_hidden_field('product_is_call', ($in_product_is_call == true ? '1' : '0'), 'class="product_is_call"'); ?>
              </div>
              <?php echo ($pInfo->product_is_call == 1 ? '<span class="errorText">' . TEXT_PRODUCTS_IS_CALL_EDIT . '</span>' : ''); ?>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_PRICED_BY_ATTRIBUTES, 'products_priced_by_attribute', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
              <div class="input-group">
                <div class="radioBtn btn-group">
                  <a class="btn btn-info <?php echo($is_products_priced_by_attribute == true ? 'active' : 'notActive'); ?>" data-toggle="products_priced_by_attribute" data-title="1"><?php echo TEXT_PRODUCT_IS_PRICED_BY_ATTRIBUTE; ?></a>
                  <a class="btn btn-info <?php echo($not_products_priced_by_attribute == true ? 'active' : 'notActive'); ?>" data-toggle="products_priced_by_attribute" data-title="0"><?php echo TEXT_PRODUCT_NOT_PRICED_BY_ATTRIBUTE; ?></a>
                </div>
                <?php echo zen_draw_hidden_field('products_priced_by_attribute', ($is_products_priced_by_attribute == true ? '1' : '0'), 'class="products_priced_by_attribute"'); ?>
              </div>
              <?php echo ($pInfo->products_priced_by_attribute == 1 ? '<span class="errorText">' . TEXT_PRODUCTS_PRICED_BY_ATTRIBUTES_EDIT . '</span>' : ''); ?>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_VIRTUAL, 'products_virtual', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
              <div class="input-group">
                <div class="radioBtn btn-group">
                  <a class="btn btn-info <?php echo($is_virtual == true ? 'active' : 'notActive'); ?>" data-toggle="products_virtual" data-title="1"><?php echo TEXT_PRODUCT_IS_VIRTUAL; ?></a>
                  <a class="btn btn-info <?php echo($not_virtual == true ? 'active' : 'notActive'); ?>" data-toggle="products_virtual" data-title="0"><?php echo TEXT_PRODUCT_NOT_VIRTUAL; ?></a>
                </div>
                <?php echo zen_draw_hidden_field('products_virtual', ($is_virtual == true ? '1' : '0'), 'class="products_virtual"'); ?>
              </div>
              <?php echo ($pInfo->products_virtual == 1 ? '<span class="errorText">' . TEXT_VIRTUAL_EDIT . '</span>' : ''); ?>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_IS_ALWAYS_FREE_SHIPPING, 'product_is_always_free_shipping', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
              <div class="btn-group" data-toggle="buttons">
                <label class="btn <?php echo ($is_product_is_always_free_shipping == true ? 'active' : '') ?>"><?php echo zen_draw_radio_field('product_is_always_free_shipping', '1', $is_product_is_always_free_shipping); ?>
                  <i class="fa fa-circle-o fa-lg"></i>
                  <i class="fa fa-dot-circle-o fa-lg"></i>
                  <span><?php echo TEXT_PRODUCT_IS_ALWAYS_FREE_SHIPPING; ?></span>
                </label>
                <label class="btn <?php echo ($not_product_is_always_free_shipping == true ? 'active' : '') ?>">
                    <?php echo zen_draw_radio_field('product_is_always_free_shipping', '0', $not_product_is_always_free_shipping); ?>
                  <i class="fa fa-circle-o fa-lg"></i>
                  <i class="fa fa-dot-circle-o fa-lg"></i>
                  <span><?php echo TEXT_PRODUCT_NOT_ALWAYS_FREE_SHIPPING; ?></span>
                </label>
                <label class="btn <?php echo ($special_product_is_always_free_shipping == true ? 'active' : '') ?>">
                    <?php echo zen_draw_radio_field('product_is_always_free_shipping', '2', $special_product_is_always_free_shipping); ?>
                  <i class="fa fa-circle-o fa-lg"></i>
                  <i class="fa fa-dot-circle-o fa-lg"></i>
                  <span><?php echo TEXT_PRODUCT_SPECIAL_ALWAYS_FREE_SHIPPING; ?></span>
                  <?php echo ($pInfo->product_is_always_free_shipping == 1 ? '<span class="errorText">' . TEXT_FREE_SHIPPING_EDIT . '</span>' : ''); ?>
              </div>
            </div>
          </div>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_PRODUCTS_QTY_BOX_STATUS, 'products_qty_box_status', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9">
              <div class="input-group">
                <div class="radioBtn btn-group">
                  <a class="btn btn-info <?php echo($is_products_qty_box_status == true ? 'active' : 'notActive'); ?>" data-toggle="products_qty_box_status" data-title="1"><?php echo TEXT_PRODUCTS_QTY_BOX_STATUS_ON; ?></a>
                  <a class="btn btn-info <?php echo($not_products_qty_box_status == true ? 'active' : 'notActive'); ?>" data-toggle="products_qty_box_status" data-title="0"><?php echo TEXT_PRODUCTS_QTY_BOX_STATUS_OFF; ?></a>
                </div>
                <?php echo zen_draw_hidden_field('products_qty_box_status', ($is_products_qty_box_status == true ? '1' : '0'), 'class="products_qty_box_status"'); ?>
              </div>
              <?php echo ($pInfo->products_qty_box_status == 0 ? '<span class="errorText">' . TEXT_PRODUCTS_QTY_BOX_STATUS_EDIT . '</span>' : ''); ?>
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
              <div class="input-group">
                <div class="radioBtn btn-group">
                  <a class="btn btn-info <?php echo($in_products_quantity_mixed == true ? 'active' : 'notActive'); ?>" data-toggle="products_quantity_mixed" data-title="1"><?php echo TEXT_YES; ?></a>
                  <a class="btn btn-info <?php echo($out_products_quantity_mixed == true ? 'active' : 'notActive'); ?>" data-toggle="products_quantity_mixed" data-title="0"><?php echo TEXT_NO; ?></a>
                </div>
                <?php echo zen_draw_hidden_field('products_quantity_mixed', ($in_products_quantity_mixed == true ? '1' : '0'), 'class="products_quantity_mixed"'); ?>
              </div>
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
                  <?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name'], '', '', 'class="img-thumbnail"') . zen_draw_input_field('products_url[' . $languages[$i]['id'] . ']', htmlspecialchars(isset($products_url[$languages[$i]['id']]) ? $products_url[$languages[$i]['id']] : zen_get_products_url($pInfo->products_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), zen_set_field_length(TABLE_PRODUCTS_DESCRIPTION, 'products_url') . 'class="form-control"'); ?>
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
                  <td class="text-center" id="mainImage">
                    <span style="cursor: pointer;">
                        <?php echo zen_image(DIR_WS_CATALOG_IMAGES . $pInfo->products_image, '', SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'class="img-thumbnail" id="mainImage" data-toggle="modal" data-target="#imagePreviewModal"'); ?>
                      <br/>
                      <?php echo TEXT_CLICK_TO_ENLARGE; ?>
                    </span>
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
                          <?php echo zen_draw_radio_field('image_delete', '0', $off_image_delete) . '&nbsp;' . TABLE_HEADING_NO . ' ' . zen_draw_radio_field('image_delete', '1', $on_image_delete) . '&nbsp;' . TABLE_HEADING_YES; ?>
                      </div>
                    </div>
                    <div class="form-group">
                        <?php echo zen_draw_label(TEXT_IMAGES_OVERWRITE, 'overwrite', 'class="col-sm-3 control-label"'); ?>
                      <div class="col-sm-9">
                          <?php echo zen_draw_radio_field('overwrite', '0', $off_overwrite) . '&nbsp;' . TABLE_HEADING_NO . ' ' . zen_draw_radio_field('overwrite', '1', $on_overwrite) . '&nbsp;' . TABLE_HEADING_YES; ?>
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
        </div>
        <?php
        $tabContentsNeedle = 'tab_contents_';
        $j = 4;
        if (isset($extraTabsFiles) && $extraTabsFiles != '') {
          foreach ($extraTabsFiles as $tabContent) {
            if (strpos($tabContent, $tabContentsNeedle) !== false) {
              ?>
              <div <?php echo 'id="productTabs' . ($j + 1) . '"'; ?> class="tab-pane fade">
                  <?php include(DIR_WS_MODULES . 'extra_tabs/' . $tabContent); ?>
              </div>
              <?php
              $j++;
            }
          }
        }
        ?>
      </div>

      <span>
          <?php
// hidden fields not changeable on products page
          if (!array_search('categories', $extraTabsFiles) && $_GET['pID'] > 0) {
            echo zen_draw_hidden_field('master_categories_id', $pInfo->master_categories_id);
          }
          if (!array_search('discounts', $extraTabsFiles)) {
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
<?php
list($width, $height) = getimagesize(DIR_FS_CATALOG_IMAGES . $pInfo->products_image);
if ($width > MEDIUM_IMAGE_WIDTH) {
  $width = MEDIUM_IMAGE_WIDTH;
}
if ($height > MEDIUM_IMAGE_HEIGHT) {
  $height = MEDIUM_IMAGE_HEIGHT;
}
?>
<!-- Product main image preview modal-->
<div id="imagePreviewModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="imageModalLabel">Image preview</h4>
      </div>
      <div class="modal-body text-center">
          <?php echo zen_image(DIR_WS_CATALOG_IMAGES . $pInfo->products_image, '', $width, $height) ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Product preview modal-->
<?php //  include DIR_WS_MODULES . 'product/preview_modal.php'; ?>
<!-- Autoload Additional Modals -->
<?php
$modalNeedle = 'modal_';
if (isset($extraTabsFiles) && $extraTabsFiles != '') {
  foreach ($extraTabsFiles as $modalFile) {
    if (strpos($modalFile, $modalNeedle) !== false) {
      include DIR_WS_MODULES . 'extra_tabs/' . $modalFile;
    }
  }
}
?>
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
<!-- script for tooltips -->
<script>
  $(document).ready(function () {
      $('[data-toggle="tooltip"]').tooltip();
  });</script>
<!-- script for preview popup -->
<script>
  $('#previewPopUp').on('click', function () {
      $('#previewmodal').modal('show');
  });</script>
<!-- script for sliding checkbox -->
<script>
  $('.container-fluid').on('click', '.radioBtn a', function () {
      var sel = $(this).data('title');
      var tog = $(this).data('toggle');
      $(this).parent().next('.' + tog).prop('value', sel);
      $(this).parent().find('a[data-toggle="' + tog + '"]').not('[data-title="' + sel + '"]').removeClass('active').addClass('notActive');
      $(this).parent().find('a[data-toggle="' + tog + '"][data-title="' + sel + '"]').removeClass('notActive').addClass('active');
  });
  $('#mainImageEditModal').on('click', '.radioBtn a', function () {
      var sel = $(this).data('title');
      var tog = $(this).data('toggle');
      $(this).parent().next('.' + tog).prop('value', sel);
      $(this).parent().find('a[data-toggle="' + tog + '"]').not('[data-title="' + sel + '"]').removeClass('active').addClass('notActive');
      $(this).parent().find('a[data-toggle="' + tog + '"][data-title="' + sel + '"]').removeClass('notActive').addClass('active');
  });
</script>
<!-- Autoload Additional javascripts -->
<?php
$jscriptNeedle = 'jscript_';
if (isset($extraTabsFiles) && $extraTabsFiles != '') {
  foreach ($extraTabsFiles as $jscriptFile) {
    if (strpos($jscriptFile, $jscriptNeedle) !== false) {
      include (DIR_WS_MODULES . 'extra_tabs/' . $jscriptFile);
    }
  }
}