<?php
/**
 * @package admin
 * @copyright (c) 2008-2017, Zen4All
 * @copyright Copyright 2003-2016 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Author: Zen4All
 */
require('includes/application_top.php');
$languages = zen_get_languages();

require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

$extraTabsPath = DIR_WS_MODULES . 'extra_tabs';
$extraTabsFiles = recursiveDirList($extraTabsPath);

$parameters = array(
  'products_name' => '',
  'products_description' => '',
  'products_url' => '',
  'products_id' => '',
  'products_quantity' => '0',
  'products_model' => '',
  'products_image' => '',
  'products_price' => '0.0000',
  'products_virtual' => DEFAULT_PRODUCT_PRODUCTS_VIRTUAL,
  'products_weight' => '0',
  'products_date_added' => '',
  'products_last_modified' => '',
  'products_date_available' => '',
  'products_status' => '1',
  'products_tax_class_id' => DEFAULT_PRODUCT_TAX_CLASS_ID,
  'manufacturers_id' => '',
  'products_quantity_order_min' => '1',
  'products_quantity_order_units' => '1',
  'products_priced_by_attribute' => '0',
  'product_is_free' => '0',
  'product_is_call' => '0',
  'products_quantity_mixed' => '1',
  'product_is_always_free_shipping' => DEFAULT_PRODUCT_PRODUCTS_IS_ALWAYS_FREE_SHIPPING,
  'products_qty_box_status' => PRODUCTS_QTY_BOX_STATUS,
  'products_quantity_order_max' => '0',
  'products_sort_order' => '0',
  'products_discount_type' => '0',
  'products_discount_type_from' => '0',
  'products_price_sorter' => '0',
  'master_categories_id' => '',
  'metatags_title_status' => '1',
  'metatags_products_name_status' => '1',
  'metatags_model_status' => '1',
  'metatags_price_status' => '1',
  'metatags_title_tagline_status' => '1',
  'metatags_title' => '',
  'metatags_keywords' => '',
  'metatags_description' => ''
);

$pInfo = new objectInfo($parameters);

if (isset($_GET['pID']) && $_GET['pID'] != '') {

  $product = $db->Execute("SELECT p.*, pe.*
                           FROM " . TABLE_PRODUCTS . " p
                           LEFT JOIN " . TABLE_PRODUCTS_EXTRA . " pe ON p.products_id = pe.products_id
                           WHERE p.products_id = " . (int)$_GET['pID']);
  $pInfo->updateObjectInfo($product->fields);

  $productLanguage = $db->Execute("SELECT pd.*, pde.*, mtpd.*
                                FROM " . TABLE_PRODUCTS_DESCRIPTION . " pd
                                LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION_EXTRA . " pde ON pd.products_id = pde.products_id
                                  AND pde.language_id = pd.language_id
                                LEFT JOIN  " . TABLE_META_TAGS_PRODUCTS_DESCRIPTION . " mtpd ON pd.products_id = mtpd.products_id
                                  AND mtpd.language_id = pd.language_id
                                WHERE pd.products_id = " . (int)$_GET['pID']);

  $productLanguageArray = [];
  foreach ($productLanguage as $item) {
    $productLanguageArray[$item['language_id']] = $item;
  }
  $pInfoLanguage = new objectInfo($productLanguageArray);
}
$category_lookup = $db->Execute("SELECT c.categories_image, cd.categories_name
                                 FROM " . TABLE_CATEGORIES . " c,
                                      " . TABLE_CATEGORIES_DESCRIPTION . " cd
                                 WHERE c.categories_id = " . (int)$current_category_id . "
                                 AND c.categories_id = cd.categories_id
                                 AND cd.language_id = " . (int)$_SESSION['languages_id']);
if (!$category_lookup->EOF) {
  $cInfo = new objectInfo($category_lookup->fields);
} else {
  $cInfo = new objectInfo(array());
}

$manufacturers_array = [];
$manufacturers_array[] = [
  'id' => '',
  'text' => TEXT_NONE];
$manufacturers = $db->Execute("SELECT manufacturers_id, manufacturers_name
                               FROM " . TABLE_MANUFACTURERS . "
                               ORDER BY manufacturers_name");
foreach ($manufacturers as $manufacturer) {
  $manufacturers_array[] = [
    'id' => $manufacturer['manufacturers_id'],
    'text' => $manufacturer['manufacturers_name']
  ];
}

$tax_class_array = [];
$tax_class_array[] = [
  'id' => '0',
  'text' => TEXT_NONE];
$tax_class = $db->Execute("SELECT tax_class_id, tax_class_title
                           FROM " . TABLE_TAX_CLASS . "
                           ORDER BY tax_class_title");
foreach ($tax_class as $item) {
  $tax_class_array[] = [
    'id' => $item['tax_class_id'],
    'text' => $item['tax_class_title']];
}

// set to out of stock if categories_status is off and new product or existing products_status is off
if (zen_get_categories_status($current_category_id) == '0' && $pInfo->products_status != '1') {
  $pInfo->products_status = 0;
}

// metatags_products_name_status shows
if (empty($pInfo->metatags_keywords) && empty($pInfo->metatags_description)) {
  $pInfo->metatags_products_name_status = zen_get_show_product_switch($_GET['pID'], 'metatags_products_name_status');
}

// metatags_title_status shows
if (empty($pInfo->metatags_keywords) && empty($pInfo->metatags_description)) {
  $pInfo->metatags_title_status = zen_get_show_product_switch($_GET['pID'], 'metatags_title_status');
}

// metatags_model_status shows
if (empty($pInfo->metatags_keywords) && empty($pInfo->metatags_description)) {
  $pInfo->metatags_model_status = zen_get_show_product_switch($_GET['pID'], 'metatags_model_status');
}

// metatags_price_status shows
if (empty($pInfo->metatags_keywords) && empty($pInfo->metatags_description)) {
  $pInfo->metatags_price_status = zen_get_show_product_switch($_GET['pID'], 'metatags_price_status');
}

// metatags_title_tagline_status shows TITLE and TAGLINE in metatags_header.php
if (empty($pInfo->metatags_keywords) && empty($pInfo->metatags_description)) {
  $pInfo->metatags_title_tagline_status = zen_get_show_product_switch($_GET['pID'], 'metatags_title_tagline_status');
}

// set image overwrite
$on_overwrite = true;
$off_overwrite = false;
// set image delete
$on_image_delete = false;
$off_image_delete = true;
?>

<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
  <head>
    <meta charset="<?php echo CHARSET; ?>">
    <title><?php echo TITLE; ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
    <!-- <link rel="stylesheet" href="includes/template/css/bootstrap.min.css" id="bootstrapCSS"> -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" id="fontAwesomeCSS">
    <link rel="stylesheet" href="includes/stylesheet.css" id="stylesheetCSS">
    <link rel="stylesheet" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
    <link rel="stylesheet" href="includes/stylesheet_print.css" media="print" id="printCSS">
    <link rel="stylesheet" href="includes/css/collect_info.css">
    <link rel="stylesheet" href="includes/css/daterangepicker.css">
    <?php /* CDN for jQuery core */ ?>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="includes/template/javascript/jquery-2.2.4.min.js"><\/script>');</script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <!-- <script src="includes/template/javascript/bootstrap.min.js"></script> -->
    <script src="includes/menu.js"></script>
    <script src="includes/general.js"></script>
    <?php /* CDN for jQuery UI components */ ?>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-ui-touch-punch-c@1.4.0/jquery.ui.touch-punch.min.js" integrity="sha256-gGj3FfxkKbWCLsDZ/LXAqVmckxEYkbljkHPeMrMs94U=" crossorigin="anonymous"></script>
    <script>
      // init datepicker defaults with localization
      $(function () {
          $.datepicker.setDefaults($.extend({}, $.datepicker.regional["<?php echo $_SESSION['languages_code'] == 'en' ? '' : $_SESSION['languages_code']; ?>"], {
              showOn: "both",
              buttonImage: "images/calendar.gif",
              dateFormat: "<?php echo DATE_FORMAT_DATEPICKER_ADMIN; ?>",
              changeMonth: true,
              changeYear: true
          }));
      });
    </script>
    <script>
      function init() {
          cssjsmenu('navbar');
          if (document.getElementById) {
              var kill = document.getElementById('hoverJS');
              kill.disabled = true;
          }
      }
    </script>
  </head>
  <body onload="init()">
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
    <?php include ($editor_handler); ?>
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <div class="container-fluid">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="col-sm-11"><?php echo sprintf(TEXT_NEW_PRODUCT, zen_output_generated_category_path($current_category_id)); ?></h3>
          <?php echo zen_info_image($cInfo->categories_image, $cInfo->categories_name, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?>
        </div>
        <div class="panel-body">
            <?php
//  echo $type_admin_handler;
            ?>
          <form name="productInfo" enctype="multipart/form-data" id="productInfo" class="form-horizontal">
              <?php
              echo zen_draw_hidden_field('securityToken', $_SESSION['securityToken']);
              echo zen_draw_hidden_field('cPath', $cPath);
              $product_type = (isset($_GET['product_type']) ? $_GET['product_type'] : '');
              echo zen_draw_hidden_field('product_type', $product_type);
              $productId = (isset($_GET['pID']) ? $_GET['pID'] : '');
              echo zen_draw_hidden_field('productId', $productId);
              echo zen_draw_hidden_field('current_category_id', $current_category_id);
              echo zen_draw_hidden_field('view', 'saveProduct');

              $dir_info = zen_build_subdirectories_array(DIR_FS_CATALOG_IMAGES);
              $default_directory = substr($pInfo->products_image, 0, strpos($pInfo->products_image, DIRECTORY_SEPARATOR) + 1);

              $availableTabsArray = getTabs($product_type);
              ?>
            <ul class="nav nav-tabs" data-tabs="tabs">
                <?php
                $tabSort = '';
                foreach ($availableTabsArray as $tab) {
                  ?>
                <li<?php echo ($tab['sortOrder'] == '1' ? ' class="active"' : ''); ?>>
                  <a data-toggle="tab" href="#productTabs<?php echo $tab['sortOrder']; ?>"><?php echo (defined($tab['define']) && constant($tab['define']) != '' ? constant($tab['define']) : $tab['define']); ?></a>
                </li>
                <?php
                $tabSort = $tab['sortOrder'];
              }
              ?>
              <?php
              $tabTitleNeedle = 'tab_title_';
              if (isset($extraTabsFiles) && $extraTabsFiles != '') {
                $tabSort++;
                foreach ($extraTabsFiles as $tabTitle) {
                  if (strpos($tabTitle, $tabTitleNeedle) !== false) {
                    ?>
                    <li>
                      <a data-toggle="tab" href="#productTabs<?php echo $tabSort; ?>"><?php include(DIR_WS_MODULES . 'extra_tabs/' . $tabTitle); ?></a>
                    </li>
                    <?php
                    $tabSort++;
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
                    <li<?php echo($i == 0 ? ' class="active"' : ''); ?>>
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
                    <div class="tab-pane fade in<?php echo ($i == 0 ? ' active' : ''); ?>" <?php echo 'id="productNameTabs' . ($i + 1) . '"'; ?>>
                      <div class="form-group">
                          <?php echo zen_draw_label(TEXT_PRODUCTS_NAME, 'products_name[' . $languages[$i]['id'] . ']', 'class="col-sm-3 control-label"'); ?>
                        <div class="col-sm-9 col-md-6">
                            <?php echo zen_draw_input_field('products_name[' . $languages[$i]['id'] . ']', htmlspecialchars($pInfoLanguage->{$languages[$i]['id']}['products_name'], ENT_COMPAT, CHARSET, TRUE), zen_set_field_length(TABLE_PRODUCTS_DESCRIPTION, 'products_name') . ' class="form-control"'); ?>
                        </div>
                      </div>
                      <div class="form-group">
                          <?php echo zen_draw_label(TEXT_PRODUCTS_DESCRIPTION, 'products_description[' . $languages[$i]['id'] . ']', 'class="col-sm-3 control-label"'); ?>
                        <div class="col-sm-9 col-md-6">
                    <?php echo zen_draw_textarea_field('products_description[' . $languages[$i]['id'] . ']', 'soft', '100%', '30', htmlspecialchars($pInfoLanguage->{$languages[$i]['id']}['products_description'], ENT_COMPAT, CHARSET, TRUE), 'class="editorHook form-control"'); ?>
                        </div>
                      </div>
                      <div class="form-group">
                          <?php echo zen_draw_label(TEXT_META_TAGS_TITLE, 'metatags_title[' . $languages[$i]['id'] . ']', 'class="col-sm-3 control-label"'); ?>
                        <div class="col-sm-9 col-md-6">
                    <?php echo zen_draw_input_field('metatags_title[' . $languages[$i]['id'] . ']', htmlspecialchars($pInfoLanguage->{$languages[$i]['id']}['metatags_title'], ENT_COMPAT, CHARSET, TRUE), zen_set_field_length(TABLE_META_TAGS_PRODUCTS_DESCRIPTION, 'metatags_title', '150', false) . 'class="form-control"'); ?>
                        </div>
                      </div>
                      <div class="form-group">
                          <?php echo zen_draw_label(TEXT_META_TAGS_KEYWORDS, 'metatags_keywords', 'class="col-sm-3 control-label"'); ?>
                        <div class="col-sm-9 col-md-6">
                            <?php echo zen_draw_textarea_field('metatags_keywords[' . $languages[$i]['id'] . ']', 'soft', '100%', '10', htmlspecialchars($pInfoLanguage->{$languages[$i]['id']}['metatags_keywords'], ENT_COMPAT, CHARSET, TRUE), 'class="noEditor form-control"'); ?>
                        </div>
                      </div>
                      <div class="form-group">
                          <?php echo zen_draw_label(TEXT_META_TAGS_DESCRIPTION, 'metatags_description', 'class="col-sm-3 control-label"'); ?>
                        <div class="col-sm-9 col-md-6">
                            <?php echo zen_draw_textarea_field('metatags_description[' . $languages[$i]['id'] . ']', 'soft', '100%', '10', htmlspecialchars($pInfoLanguage->{$languages[$i]['id']}['metatags_description'], ENT_COMPAT, CHARSET, TRUE), 'class="noEditor form-control"'); ?>
                        </div>
                      </div>
                    </div>
                    <?php
                  }
                  ?>
                </div>
                <div class="form-group">
                  <label class="control-label col-sm-4">
                      <?php echo TEXT_META_TAG_TITLE_INCLUDES; ?>
                  </label>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_METATAGS_PRODUCTS_NAME_STATUS, 'metatags_products_name_status', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                    <div class="input-group">
                      <div class="radioBtn btn-group">
                        <a class="btn btn-info <?php echo($pInfo->metatags_products_name_status == true ? 'active' : 'notActive'); ?>" data-toggle="metatags_products_name_status" data-title="1"><?php echo TEXT_YES; ?></a>
                        <a class="btn btn-info <?php echo($pInfo->metatags_products_name_status == false ? 'active' : 'notActive'); ?>" data-toggle="metatags_products_name_status" data-title="0"><?php echo TEXT_NO; ?></a>
                      </div>
                      <?php echo zen_draw_hidden_field('metatags_products_name_status', ($pInfo->metatags_products_name_status == true ? '1' : '0'), 'class="metatags_products_name_status"'); ?>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_METATAGS_TITLE_STATUS, 'metatags_title_status', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                    <div class="input-group">
                      <div class="radioBtn btn-group">
                        <a class="btn btn-info <?php echo($pInfo->metatags_title_status == true ? 'active' : 'notActive'); ?>" data-toggle="metatags_title_status" data-title="1"><?php echo TEXT_YES; ?></a>
                        <a class="btn btn-info <?php echo($pInfo->metatags_title_status == false ? 'active' : 'notActive'); ?>" data-toggle="metatags_title_status" data-title="0"><?php echo TEXT_NO; ?></a>
                      </div>
                      <?php echo zen_draw_hidden_field('metatags_title_status', ($pInfo->metatags_title_status == true ? '1' : '0'), 'class="metatags_title_status"'); ?>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_METATAGS_MODEL_STATUS, 'metatags_model_status', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                    <div class="input-group">
                      <div class="radioBtn btn-group">
                        <a class="btn btn-info <?php echo($pInfo->metatags_model_status == true ? 'active' : 'notActive'); ?>" data-toggle="metatags_model_status" data-title="1"><?php echo TEXT_YES; ?></a>
                        <a class="btn btn-info <?php echo($pInfo->metatags_model_status == false ? 'active' : 'notActive'); ?>" data-toggle="metatags_model_status" data-title="0"><?php echo TEXT_NO; ?></a>
                      </div>
                      <?php echo zen_draw_hidden_field('metatags_model_status', ($pInfo->metatags_model_status == true ? '1' : '0'), 'class="metatags_model_status"'); ?>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_METATAGS_PRICE_STATUS, 'metatags_price_status', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                    <div class="input-group">
                      <div class="radioBtn btn-group">
                        <a class="btn btn-info <?php echo($pInfo->metatags_price_status == true ? 'active' : 'notActive'); ?>" data-toggle="metatags_price_status" data-title="1"><?php echo TEXT_YES; ?></a>
                        <a class="btn btn-info <?php echo($pInfo->metatags_price_status == false ? 'active' : 'notActive'); ?>" data-toggle="metatags_price_status" data-title="0"><?php echo TEXT_NO; ?></a>
                      </div>
                      <?php echo zen_draw_hidden_field('metatags_price_status', ($pInfo->metatags_price_status == true ? '1' : '0'), 'class="metatags_price_status"'); ?>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <?php echo zen_draw_label(TEXT_PRODUCTS_METATAGS_TITLE_TAGLINE_STATUS, 'metatags_title_tagline_status', 'class="col-sm-3 control-label"'); ?> <i class="fa fa-lg fa-question-circle" data-toggle="tooltip" data-placement="top" title="<?php echo TEXT_INFO_META_TAGS_USAGE; ?>"></i>
                  <div class="col-sm-9 col-md-6">
                    <div class="input-group">
                      <div class="radioBtn btn-group">
                        <a class="btn btn-info <?php echo($pInfo->metatags_title_tagline_status == true ? 'active' : 'notActive'); ?>" data-toggle="metatags_title_tagline_status" data-title="1"><?php echo TEXT_YES; ?></a>
                        <a class="btn btn-info <?php echo($pInfo->metatags_title_tagline_status == false ? 'active' : 'notActive'); ?>" data-toggle="metatags_title_tagline_status" data-title="0"><?php echo TEXT_NO; ?></a>
                      </div>
                      <?php echo zen_draw_hidden_field('metatags_title_tagline_status', ($pInfo->metatags_title_tagline_status == true ? '1' : '0'), 'class="metatags_title_tagline_status"'); ?>
                    </div>
                  </div>
                </div>
              </div>
              <div id="productTabs2" class="tab-pane fade">
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_MODEL, 'products_model', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_input_field('products_model', htmlspecialchars(stripslashes($pInfo->products_model), ENT_COMPAT, CHARSET, TRUE), zen_set_field_length(TABLE_PRODUCTS, 'products_model') . 'class="form-control"'); ?>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_TAX_CLASS, 'products_tax_class_id', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_pull_down_menu('products_tax_class_id', $tax_class_array, $pInfo->products_tax_class_id, 'onchange="updateGross()" class="form-control"'); ?>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_PRICE_NET, 'products_price', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_input_field('products_price', $pInfo->products_price, 'onkeyup="updateGross()" class="form-control"'); ?>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_PRICE_GROSS, 'products_price_gross', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_input_field('products_price_gross', $pInfo->products_price, 'onkeyup="updateNet()" class="form-control"'); ?>
                  </div>
                </div>
                <script>updateGross();</script>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_STATUS, 'products_status', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                    <div class="input-group">
                      <div class="radioBtn btn-group">
                        <a class="btn btn-info <?php echo($pInfo->products_status == '1' ? 'active' : 'notActive'); ?>" data-toggle="products_status" data-title="1"><?php echo TEXT_PRODUCT_AVAILABLE; ?></a>
                        <a class="btn btn-info <?php echo($pInfo->products_status == '0' ? 'active' : 'notActive'); ?>" data-toggle="products_status" data-title="0"><?php echo TEXT_PRODUCT_NOT_AVAILABLE; ?></a>
                        <?php echo (zen_get_categories_status($current_category_id) == '0' ? TEXT_CATEGORIES_STATUS_INFO_OFF : '') . ($pInfo->products_status == '0' ? ' ' . TEXT_PRODUCTS_STATUS_INFO_OFF : ''); ?>
                      </div>
                      <?php echo zen_draw_hidden_field('products_status', $pInfo->products_status, 'class="products_status"'); ?>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_QUANTITY, 'products_quantity', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_input_field('products_quantity', $pInfo->products_quantity, 'class="form-control"'); ?>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_DATE_AVAILABLE, 'products_date_available', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-3">
                    <div class="date" id="datepicker">
                      <div class="input-group">
                        <?php echo zen_draw_input_field('products_date_available', $pInfo->products_date_available, 'class="form-control"'); ?>
                        <span class="input-group-addon">
                          <i class="fa fa-calendar fa-lg"></i>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCT_IS_FREE, 'product_is_free', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                    <div class="input-group">
                      <div class="radioBtn btn-group">
                        <a class="btn btn-info <?php echo($pInfo->product_is_free == '1' ? 'active' : 'notActive'); ?>" data-toggle="product_is_free" data-title="1"><?php echo TEXT_YES; ?></a>
                        <a class="btn btn-info <?php echo($pInfo->product_is_free == '0' ? 'active' : 'notActive'); ?>" data-toggle="product_is_free" data-title="0"><?php echo TEXT_NO; ?></a>
                        <?php echo ($pInfo->product_is_free == '1' ? '<span class="alert">' . TEXT_PRODUCTS_IS_FREE_EDIT . '</span>' : ''); ?>
                      </div>
                      <?php echo zen_draw_hidden_field('product_is_free', $pInfo->product_is_free, 'class="product_is_free"'); ?>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCT_IS_CALL, 'product_is_call', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                    <div class="input-group">
                      <div class="radioBtn btn-group">
                        <a class="btn btn-info <?php echo($pInfo->product_is_call == '1' ? 'active' : 'notActive'); ?>" data-toggle="product_is_call" data-title="1"><?php echo TEXT_YES; ?></a>
                        <a class="btn btn-info <?php echo($pInfo->product_is_call == '0' ? 'active' : 'notActive'); ?>" data-toggle="product_is_call" data-title="0"><?php echo TEXT_NO; ?></a>
                        <?php echo ($pInfo->product_is_call == '1' ? '<span class="alert">' . TEXT_PRODUCTS_IS_CALL_EDIT . '</span>' : ''); ?>
                      </div>
                      <?php echo zen_draw_hidden_field('product_is_call', $pInfo->product_is_call, 'class="product_is_call"'); ?>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_PRICED_BY_ATTRIBUTES, 'products_priced_by_attribute', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                    <div class="input-group">
                      <div class="radioBtn btn-group">
                        <a class="btn btn-info <?php echo($pInfo->products_priced_by_attribute == '1' ? 'active' : 'notActive'); ?>" data-toggle="products_priced_by_attribute" data-title="1"><?php echo TEXT_PRODUCT_IS_PRICED_BY_ATTRIBUTE; ?></a>
                        <a class="btn btn-info <?php echo($pInfo->products_priced_by_attribute == '0' ? 'active' : 'notActive'); ?>" data-toggle="products_priced_by_attribute" data-title="0"><?php echo TEXT_PRODUCT_NOT_PRICED_BY_ATTRIBUTE; ?></a>
                        <?php echo ($pInfo->products_priced_by_attribute == '1' ? '<span class="alert">' . TEXT_PRODUCTS_PRICED_BY_ATTRIBUTES_EDIT . '</span>' : ''); ?>
                      </div>
                      <?php echo zen_draw_hidden_field('products_priced_by_attribute', $pInfo->products_priced_by_attribute, 'class="products_priced_by_attribute"'); ?>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_VIRTUAL, 'products_virtual', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                    <div class="input-group">
                      <div class="radioBtn btn-group">
                        <a class="btn btn-info <?php echo($pInfo->products_virtual == '1' ? 'active' : 'notActive'); ?>" data-toggle="products_virtual" data-title="1"><?php echo TEXT_PRODUCT_IS_VIRTUAL; ?></a>
                        <a class="btn btn-info <?php echo($pInfo->products_virtual == '0' ? 'active' : 'notActive'); ?>" data-toggle="products_virtual" data-title="0"><?php echo TEXT_PRODUCT_NOT_VIRTUAL; ?></a>
                        <?php echo ($pInfo->products_virtual == '1' ? '<span class="alert">' . TEXT_VIRTUAL_EDIT . '</span>' : ''); ?>
                      </div>
                      <?php echo zen_draw_hidden_field('products_virtual', $pInfo->products_virtual, 'class="products_virtual"'); ?>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_IS_ALWAYS_FREE_SHIPPING, 'product_is_always_free_shipping', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                    <div class="input-group">
                      <div class="radioBtn btn-group">
                        <a class="btn btn-info btn-sm <?php echo($pInfo->product_is_always_free_shipping == '1' ? 'active' : 'notActive'); ?>" data-toggle="product_is_always_free_shipping" data-title="1"><?php echo TEXT_PRODUCT_IS_ALWAYS_FREE_SHIPPING; ?></a>
                        <a class="btn btn-info btn-sm <?php echo($pInfo->product_is_always_free_shipping == '0' ? 'active' : 'notActive'); ?>" data-toggle="product_is_always_free_shipping" data-title="0"><?php echo TEXT_PRODUCT_NOT_ALWAYS_FREE_SHIPPING; ?></a>
                        <a class="btn btn-info btn-sm <?php echo($pInfo->product_is_always_free_shipping == '2' ? 'active' : 'notActive'); ?>" data-toggle="product_is_always_free_shipping" data-title="2"><?php echo TEXT_PRODUCT_SPECIAL_ALWAYS_FREE_SHIPPING; ?></a>
                      </div>
                      <?php echo zen_draw_hidden_field('product_is_always_free_shipping', $pInfo->product_is_always_free_shipping, 'class="product_is_always_free_shipping"'); ?>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_QTY_BOX_STATUS, 'products_qty_box_status', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                    <div class="input-group">
                      <div class="radioBtn btn-group">
                        <a class="btn btn-info <?php echo($pInfo->products_qty_box_status == '1' ? 'active' : 'notActive'); ?>" data-toggle="products_qty_box_status" data-title="1"><?php echo TEXT_PRODUCTS_QTY_BOX_STATUS_ON; ?></a>
                        <a class="btn btn-info <?php echo($pInfo->products_qty_box_status == '0' ? 'active' : 'notActive'); ?>" data-toggle="products_qty_box_status" data-title="0"><?php echo TEXT_PRODUCTS_QTY_BOX_STATUS_OFF; ?></a>
                        <?php echo ($pInfo->products_qty_box_status == '0' ? '<span class="alert">' . TEXT_PRODUCTS_QTY_BOX_STATUS_EDIT . '</span>' : ''); ?>
                      </div>
                      <?php echo zen_draw_hidden_field('products_qty_box_status', $pInfo->products_qty_box_status, 'class="products_qty_box_status"'); ?>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_QUANTITY_MIN_RETAIL, 'products_quantity_order_min', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_input_field('products_quantity_order_min', $pInfo->products_quantity_order_min, 'class="form-control"'); ?>
                  </div>
                </div>
                <div class="form-group">
                  <?php echo zen_draw_label(TEXT_PRODUCTS_QUANTITY_MAX_RETAIL, 'products_quantity_order_max', 'class="col-sm-3 control-label"'); ?> <i class="fa fa-lg fa-question-circle" data-toggle="tooltip" data-placement="top" title="<?php echo TEXT_PRODUCTS_QUANTITY_MAX_RETAIL_EDIT; ?>"></i>
                  <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_input_field('products_quantity_order_max', $pInfo->products_quantity_order_max, 'class="form-control"'); ?>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_QUANTITY_UNITS_RETAIL, 'products_quantity_order_units', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_input_field('products_quantity_order_units', $pInfo->products_quantity_order_units, 'class="form-control"'); ?>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_MIXED, 'products_quantity_mixed', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                    <div class="input-group">
                      <div class="radioBtn btn-group">
                        <a class="btn btn-info <?php echo($pInfo->products_quantity_mixed == '1' ? 'active' : 'notActive'); ?>" data-toggle="products_quantity_mixed" data-title="1"><?php echo TEXT_YES; ?></a>
                        <a class="btn btn-info <?php echo($pInfo->products_quantity_mixed == '0' ? 'active' : 'notActive'); ?>" data-toggle="products_quantity_mixed" data-title="0"><?php echo TEXT_NO; ?></a>
                      </div>
                      <?php echo zen_draw_hidden_field('products_quantity_mixed', $pInfo->products_quantity_mixed, 'class="products_quantity_mixed"'); ?>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_WEIGHT, 'products_weight', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_input_field('products_weight', $pInfo->products_weight, 'class="form-control"'); ?>
                  </div>
                </div>
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_SORT_ORDER, 'products_sort_order', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_input_field('products_sort_order', $pInfo->products_sort_order, 'class="form-control"'); ?>
                  </div>
                </div>
              </div>
              <div id="productTabs3" class="tab-pane fade">
                <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_MANUFACTURER, 'manufacturers_id', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_pull_down_menu('manufacturers_id', $manufacturers_array, $pInfo->manufacturers_id, 'class="form-control"'); ?>
                  </div>
                </div>
                <ul class="nav nav-tabs" data-tabs="tabs">
                    <?php
                    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                      ?>
                    <li<?php echo($i == 0 ? ' class="active"' : ''); ?>>
                      <a data-toggle="tab" href="#productManufacturersUrlTabs<?php echo $i + 1; ?>">
                          <?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . $languages[$i]['name']; ?>
                      </a>
                    </li>
                  <?php } ?>
                </ul>
                <div class="tab-content">
                    <?php
                    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                      ?>
                    <div class="tab-pane fade in<?php echo ($i == 0 ? ' active' : ''); ?>" <?php echo 'id="productManufacturersUrlTabs' . ($i + 1) . '"'; ?>>
                      <div class="form-group">
                        <?php echo zen_draw_label(TEXT_PRODUCTS_URL, 'products_url[' . $languages[$i]['id'] . ']', 'class="col-sm-3 control-label"'); ?> <i class="fa fa-lg fa-question-circle" data-toggle="tooltip" data-placement="top" title="<?php echo TEXT_PRODUCTS_URL_WITHOUT_HTTP; ?>"></i>
                        <div class="col-sm-9 col-md-6">
                            <?php echo zen_draw_input_field('products_url[' . $languages[$i]['id'] . ']', htmlspecialchars($pInfoLanguage->{$languages[$i]['id']}['products_url'], ENT_COMPAT, CHARSET, TRUE), zen_set_field_length(TABLE_PRODUCTS_DESCRIPTION, 'products_url') . 'class="form-control"'); ?>
                        </div>
                      </div>
                    </div>
                    <?php
                  }
                  ?>
                </div>
              </div>
              <div id="productTabs4" class="tab-pane fade">
                <div class="row">
                  <div class="col-sm-4 text-center" id="mainImage">
                    <div class="panel panel-info">
                      <div class="panel-heading"><?php echo TEXT_PRODUCTS_IMAGE; ?></div>
                      <div class="panel-body">
                        <div class="col-sm-8">
                          <div id="mainImageThumb" data-toggle="modal" data-target="#imagePreviewModal" role="button">
                              <?php if ($pInfo->products_image != '') { ?>
                                <?php echo zen_image(DIR_WS_CATALOG_IMAGES . $pInfo->products_image, '', SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'class="img-thumbnail" id="mainImage"'); ?>
                              <br/>
                              <?php echo TEXT_CLICK_TO_ENLARGE; ?>
                            <?php } else { ?>
                              <?php echo NONE; ?>
                            <?php } ?>
                          </div>
                          <?php echo zen_draw_hidden_field('products_image', $pInfo->products_image, 'id="mainProductImage"'); ?>
                          <?php echo zen_draw_hidden_field('products_previous_image', $pInfo->products_image); ?>
                          <div id="mainImagePath">
                            <?php echo ($pInfo->products_image != '' ? $pInfo->products_image : NONE); ?></div>
                        </div>
                        <div class="col-sm-4">
                          <div role="group">
                              <?php if ($pInfo->products_image != '') { ?>
                              <button type="button" id="button-edit-main-image" class="btn btn-primary" data-original-title="<?php echo TEXT_CHANGE_IMAGE; ?>" data-toggle="modal" data-target="#mainImageEditModal"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                              <button type="button" id="button-delete-main-image-1" class="btn btn-danger" data-original-title="<?php ?>" data-toggle="modal" data-target="#mainImageDeleteModal"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                            <?php } else { ?>
                              <button type="button" id="button-add-main-image" class="btn btn-primary" data-original-title="<?php echo TEXT_ADD_IMAGE; ?>" data-toggle="modal" data-target="#mainImageEditModal"><i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                            <?php } ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /* BOF future code */ -->
                <?php
                if ($pInfo->products_image != '' && $pInfo->products_id != '') {
                  $additionalImages = getAdditionalImages($pInfo->products_id, $pInfo->products_image);
                } // if products_image
                ?>
                <div class="panel panel-primary" id="additionalImages"<?php echo (($pInfo->products_image != '' && $pInfo->products_id != '') ? '' : ' style="display:none"') ?>>
                  <div class="panel-heading"><?php echo TEXT_ADDITIONAL_IMAGES; ?></div>
                  <div class="panel-body">
                      <?php
                      if (is_array($additionalImages['images'])) {
                        foreach ($additionalImages['images'] as $image) {
                          if (isset($image['count'])) {
                            ?>
                          <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 text-center">
                            <div class="panel panel-info">
                              <div class="panel-body">
                                <div class="col-sm-8">
                                  <div <?php echo 'id="additionalImageThumb-' . $image['suffix_number'] . '"'; ?> data-toggle="modal" <?php echo 'data-target="#imagePreviewModal-' . $image['suffix_number'] . '"'; ?> role="button">
                                      <?php echo zen_image($image['filepath'], '', SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'class="img-thumbnail" id="additionalImage-' . $image['suffix_number'] . '"'); ?>
                                    <br/>
                                    <?php echo TEXT_CLICK_TO_ENLARGE; ?>
                                  </div>
                                  <div <?php echo 'id="additionalImagePath-' . $image['suffix_number'] . '"'; ?>>
                                    <?php echo $image['filename']; ?></div>
                                </div>
                                <div class="col-sm-4">
                                  <div role="group">
                                    <button type="button" <?php echo 'id="button-edit-additional-image-' . $image['suffix_number'] . '"'; ?> class="btn btn-primary" data-original-title="<?php echo TEXT_CHANGE_IMAGE; ?>"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                                    <button type="button" <?php echo 'id="button-delete-additional-image-' . $image['suffix_number'] . '"'; ?> class="btn btn-danger" data-original-title="<?php ?>"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <?php
                        }
                      }
                    }
                    ?>
                  </div>
                  <div class="panel-footer">
                    <button type="button" id="button-add-additional-image-1" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button>
                  </div>
                </div>
              </div>
              <!-- /* EOF future code */ -->

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
                ?>

            </span>

            <div class="btn-group">
              <a id="previewPopUp" class="btn btn-default" name="btnpreview" href="#" role="button">
                <i class="fa fa-tv"></i> <?php echo IMAGE_PREVIEW; ?>
              </a>
              <?php if (isset($_GET['pID']) && $_GET['pID'] != '') { ?>
                <button id="btnsubmit" class="btn btn-primary" onclick="saveProduct()" type="submit" >
                  <i class="fa fa-save"></i> <?php echo IMAGE_SAVE; ?>
                </button>
              <?php } else { ?>
                <button id="btnsubmit" class="btn btn-primary" onclick="saveProduct()" type="submit" >
                  <i class="fa fa-save"></i> <?php echo IMAGE_INSERT; ?>
                </button>
              <?php } ?>
              <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '') . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')); ?>" class="btn btn-warning" id="btncancel" name="btncancel"><i class="fa fa-undo"></i> Back </a>
            </div>
          </form>
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
    <!-- Message Stack modal-->
    <div id="collectInfoMessageStack" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              <i class="fa fa-times" aria-hidden="true"></i>
              <span class="sr-only"><?php echo TEXT_CLOSE; ?></span>
            </button>
          </div>
          <div class="modal-body" id="collectInfoMessageStackText">
            <!-- content is entered using AJAX -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">
              <i class="fa fa-close"></i> <?php echo TEXT_CLOSE; ?>
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- Product main image preview modal-->
    <div id="imagePreviewModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i><span class="sr-only"><?php echo TEXT_CLOSE; ?></span></button>
            <h4 class="modal-title" id="imagePreviewModalLabel"><?php echo IMAGE_PREVIEW; ?></h4>
          </div>
          <div class="modal-body text-center" id="mainImageLarger">
              <?php echo zen_image(DIR_WS_CATALOG_IMAGES . $pInfo->products_image, '', $width, $height) ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo TEXT_CLOSE; ?></button>
          </div>
        </div>
      </div>
    </div>
    <!-- Product main image delete modal-->
    <div id="mainImageDeleteModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i><span class="sr-only"><?php echo TEXT_CLOSE; ?></span></button>
            <h4 class="modal-title" id="mainImageDeleteModalLabel"><?php echo IMAGE_DELETE; ?></h4>
          </div>
          <div class="modal-body">

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo TEXT_CLOSE; ?></button>
          </div>
        </div>
      </div>
    </div>
    <!-- Product main image add/edit modal-->
    <div id="mainImageEditModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              <i class="fa fa-times" aria-hidden="true"></i>
              <span class="sr-only"><?php echo TEXT_CLOSE; ?></span>
            </button>
            <h4 class="modal-title" id="imageModalLabel">Image Edit</h4>
          </div>
          <form name="mainImageSelect" method="post" enctype="multipart/form-data" id="mainImageSelect">
            <div class="modal-body">

              <div class="form-group">
                  <?php echo zen_draw_file_field('products_image', '', 'id="fileField" class="form-control" name="image" accept="image/*"'); ?>
              </div>
              <div class="form-group">
                  <?php echo zen_draw_label(TEXT_PRODUCTS_IMAGE_DIR, 'img_dir', 'class="control-label"'); ?>
                  <?php echo zen_draw_pull_down_menu('img_dir', $dir_info, $default_directory, 'id="image_dir" class="form-control"'); ?>
              </div>
              <div class="form-group">
                  <?php echo zen_draw_label(TEXT_IMAGES_OVERWRITE, 'overwrite', 'class="control-label"'); ?>
                <div class="input-group">
                  <div class="radioBtn btn-group">
                    <a class="btn btn-info notActive" data-toggle="overwrite" data-title="0"><?php echo TABLE_HEADING_NO; ?></a>
                    <a class="btn btn-info active" data-toggle="overwrite" data-title="1"><?php echo TABLE_HEADING_YES; ?></a>
                  </div>
                  <?php echo zen_draw_hidden_field('overwrite', '1', 'class="overwrite"'); ?>
                </div>
              </div>
              <?php if ($pInfo->products_image != '') { ?>
              <div class="form-group">
                  <?php echo zen_draw_label(TEXT_RENAME_ADDITIONAL_IMAGES, 'rename', 'class="control-label"'); ?>
                <div class="input-group">
                  <div class="radioBtn btn-group">
                    <a class="btn btn-info notActive" data-toggle="rename" data-title="0"><?php echo TABLE_HEADING_NO; ?></a>
                    <a class="btn btn-info active" data-toggle="rename" data-title="1"><?php echo TABLE_HEADING_YES; ?></a>
                  </div>
                  <?php echo zen_draw_hidden_field('rename', '1', 'class="rename"'); ?>
                </div>
              </div>
              <?php } ?>
              <hr style="border-top: 1px solid #8c8b8b">
              <div class="form-group">
                  <?php echo zen_draw_label(TEXT_PRODUCTS_IMAGE_MANUAL, 'products_image_manual', 'class="control-label"'); ?>
                  <?php echo zen_draw_input_field('products_image_manual', '', 'class="form-control"'); ?>
              </div>
            </div>
            <div class="modal-footer">
                <?php echo zen_draw_hidden_field('products_previous_image', $pInfo->products_image); ?>
                <?php echo zen_draw_hidden_field('view', 'setImage'); ?>
              <button type="submit" class="btn btn-primary" onclick="saveMainImage();"><i class="fa fa-save"></i></button>
              <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> <?php echo TEXT_CLOSE; ?></button>
            </div>
          </form>'
        </div>
      </div>
    </div>
    <!-- Product preview modal-->
    <?php // include DIR_WS_MODULES . 'product/preview_modal.php'; ?>
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
    <script>
      // script for tooltips
      $(document).ready(function () {
          $('[data-toggle="tooltip"]').tooltip();
      });
      // script for preview popup
      $('#previewPopUp').on('click', function () {
          $('#previewmodal').modal('show');
      });
      // script for sliding checkbox
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
    <!-- load main javascript for collect_info -->
    <?php require_once 'includes/javascript/z4a_jscriptCollectInfo.php'; ?>
    <!-- Autoload Additional JavaScripts -->
    <?php
    $jscriptNeedle = 'jscript_';
    if (isset($extraTabsFiles) && $extraTabsFiles != '') {
      foreach ($extraTabsFiles as $jscriptFile) {
        if (strpos($jscriptFile, $jscriptNeedle) !== false) {
          include (DIR_WS_MODULES . 'extra_tabs/' . $jscriptFile);
        }
      }
    }