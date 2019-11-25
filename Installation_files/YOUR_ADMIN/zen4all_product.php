<?php
/**
 * @package admin
 * @copyright (c) 2008-2018, Zen4All
 * @copyright Copyright 2003-2018 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Author: Zen4All  Aug 2018  Modified in v1.5.6 $
 */
require('includes/application_top.php');
$languages = zen_get_languages();

require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

$productType = (isset($_POST['product_type']) ? $_POST['product_type'] : (isset($_GET['pID']) ? zen_get_products_type($_GET['pID']) : 1));

$parameters = [
  'products_id' => ['value' => ''],
  'products_date_added' => ['value' => ''],
  'products_last_modified' => ['value' => ''],
  'master_categories_id' => ['value' => '']];

$sqlParametersFileList = dirListProductFields(PRODUCT_FIELDS_INCLUDES_SQL_FOLDER);
$fields = '';
$tables = '';

$productTypeFieldsQuery = "SELECT product_type, field_name, sort_order, tab_id
                           FROM " . TABLE_PRODUCT_FIELDS_TO_TYPE . "
                           WHERE product_type = " . (int)$productType . "
                           ORDER BY tab_id,sort_order";
$productTypeFields = $db->Execute($productTypeFieldsQuery);
$fieldsAvailable = [];
foreach ($productTypeFields as $productFields) {
  $fieldsAvailable[$productFields['field_name']] = [
    'sortOrder' => $productFields['sort_order'],
    'tabId' => $productFields['tab_id']
  ];
  include PRODUCT_FIELDS_INCLUDES_SQL_FOLDER . $productFields['field_name'] . '.php';
}
if (isset($additionalTable) && $additionalTable != '') {
  foreach ($additionalTable as $item) {
    $tables .= $item;
  }
}
$productInfo = array_merge_recursive($fieldsAvailable, $parameters);

$extraTabsPath = DIR_WS_MODULES . 'extra_tabs';
$extraTabsFiles = recursiveDirList($extraTabsPath);

$productId = (isset($_GET['pID']) ? (int)$_GET['pID'] : '');

if ($productId != '') {

  zen4allCheckProductTables($productId);

  $product = $db->Execute("SELECT p.products_id, p.products_date_added, p.products_last_modified, p.master_categories_id" . $fields . "
                           FROM " . TABLE_PRODUCTS . " p
                           LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON pd.products_id = p.products_id
                             AND pd.language_id = " . (int)$_SESSION['languages_id'] . "
                           LEFT JOIN " . TABLE_META_TAGS_PRODUCTS_DESCRIPTION . " mtpd ON mtpd.products_id = p.products_id
                             AND mtpd.language_id = pd.language_id
                           " . $tables . "
                           WHERE p.products_id = " . $productId);

  foreach ($product->fields as $fieldName => $value) {
    $productInfo[$fieldName]['value'] = $value;
  }
}
$category_lookup = $db->Execute("SELECT c.categories_image, cd.categories_name
                                 FROM " . TABLE_CATEGORIES . " c
                                 LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON cd.categories_id = c.categories_id
                                   AND cd.language_id = " . (int)$_SESSION['languages_id'] . "
                                 WHERE c.categories_id = " . (int)$current_category_id);
if (!$category_lookup->EOF) {
  $cInfo = new objectInfo($category_lookup->fields);
} else {
  $cInfo = new objectInfo([]);
}
?>

<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
  <head>
    <meta charset="<?php echo CHARSET; ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=1">
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" href="includes/stylesheet.css">
    <link rel="stylesheet" href="includes/css/collect_info.css">
    <link rel="stylesheet" href="includes/css/daterangepicker.css">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> -->
    <script src="includes/general.js"></script>
  </head>
  <body>
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-ui-touch-punch@0.2.3/jquery.ui.touch-punch.min.js" integrity="sha256-AAhU14J4Gv8bFupUUcHaPQfvrdNauRHMt+S4UVcaJb0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!-- header_eof //-->
    <div class="container-fluid">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title col-sm-10"><?php echo sprintf(TEXT_NEW_PRODUCT, zen_output_generated_category_path($current_category_id)); ?></h3>
          <div class="col-sm-2"><?php echo zen_info_image($cInfo->categories_image, $cInfo->categories_name, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></div>
        </div>
        <div class="panel-body">
          <form name="productInfo" enctype="multipart/form-data" id="productInfo" class="form-horizontal">
            <?php
            echo zen_draw_hidden_field('securityToken', $_SESSION['securityToken']);
            echo zen_draw_hidden_field('cPath', $cPath);
            echo zen_draw_hidden_field('products_type', $productType);
            echo zen_draw_hidden_field('productId', $productId);
            echo zen_draw_hidden_field('current_category_id', $current_category_id);

            $availableTabsArray = getTabsInType($productType);
            ?>
            <ul class="nav nav-tabs" data-tabs="tabs">
              <?php
              $tabSort = '';
              foreach ($availableTabsArray as $tab) {
                ?>
                <li<?php echo ($tab['sortOrder'] == '1' ? ' class="active"' : ''); ?>>
                  <a data-toggle="tab" href="#productTabs<?php echo $tab['sortOrder']; ?>"><?php echo $tab['tabName']; ?></a>
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
              <?php
              $tabSortContent = '';
              foreach ($availableTabsArray as $tab) {
                ?>
                <div id="productTabs<?php echo $tab['sortOrder']; ?>" class="tab-pane fade in <?php echo ($tab['sortOrder'] == '1' ? 'active' : ''); ?>">
                  <?php
                  foreach ($productInfo as $key => $infoField) {
                    if ($infoField['tabId'] == $tab['id']) {
                      ?>
                      <div class="form-group">
                        <?php include INCLUDES_HTML_OUTPUT_FOLDER . $key . '.php'; ?>
                      </div>
                      <?php
                    }
                  }
                  ?>
                </div>
                <?php
                $tabSortContent = $tab['sortOrder'];
              }
              $tabContentsNeedle = 'tab_contents_';
              if (isset($extraTabsFiles) && $extraTabsFiles != '') {
                $tabSortContent++;
                foreach ($extraTabsFiles as $tabContent) {
                  if (strpos($tabContent, $tabContentsNeedle) !== false) {
                    ?>
                    <div id="productTabs<?php echo $tabSortContent; ?>" class="tab-pane fade">
                      <?php include(DIR_WS_MODULES . 'extra_tabs/' . $tabContent); ?>
                    </div>
                    <?php
                    $tabSortContent++;
                  }
                }
              }
              ?>
            </div>
            <span>
              <?php
// hidden fields not changeable on products page
              if (!array_search('category/tab_title_collect_info.php', $extraTabsFiles) && $productId > 0) {
                echo zen_draw_hidden_field('master_categories_id', $productInfo['master_categories_id']['value']);
              }
              if (!array_search('quantity_discounts/tab_title_collect_info.php', $extraTabsFiles)) {
                echo zen_draw_hidden_field('products_discount_type', (isset($productInfo['products_discount_type']['value']) && $productInfo['products_discount_type']['value'] != '' ? $productInfo['products_discount_type']['value'] : '0'));
                echo zen_draw_hidden_field('products_discount_type_from', (isset($productInfo['products_discount_type_from']['value']) && $productInfo['products_discount_type']['value'] != '' ? $productInfo['products_discount_type_from']['value'] : '0'));
              }
              echo zen_draw_hidden_field('products_price_sorter', (isset($productInfo['products_price_sorter']['value']) && $productInfo['products_price_sorter']['value'] != '' ? $productInfo['products_price_sorter']['value'] : '0.0000'));
              echo zen_draw_hidden_field('products_date_added', (zen_not_null($productInfo['products_date_added']['value']) ? $productInfo['products_date_added']['value'] : date('Y-m-d')));
              ?>
            </span>
            <div class="btn-group">
              <a id="previewPopUp" class="btn btn-default" name="btnpreview" href="#" role="button">
                <i class="fa fa-tv"></i> <?php echo IMAGE_PREVIEW; ?>
              </a>
              <button name="<?php echo ($productId != '' ? 'insertButton' : 'updateButton'); ?>" id="btnsubmit" class="btn btn-success" onclick="saveProduct()" type="submit">
                <i class="fa fa-save"></i> <?php echo ($productId != '' ? IMAGE_SAVE : IMAGE_INSERT); ?>
              </button> <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . (!empty($productId) ? '&pID=' . $productId : '') . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')); ?>" class="btn btn-default" id="btncancel" name="btncancel"><i class="fa fa-undo"></i> Back </a>
            </div>
          </form>
        </div>
        <div class="panel-footer text-center">
          <strong>Cittins is developed by <a href="https:zen4all.nl" title="Zen4All" target="_blank">Zen4All</a>.</strong> - Version: <a href="https://www.zen-cart.com/downloads.php?do=file&id=2171" target="_blank"><?php echo ZEN4ALL_CITTINS_VERSION; ?></a> - <a href="https://github.com/Zen4All-nl/Zen-Cart-Collect-Info-Through-Tabs-In-New-Style/releases/latest" target="_blank"><i class="fa fa-github fa-lg"></i> Github</a><br>
          <img src="images/zen4all_logo_small.png" alt="Zen4All Logo" title="Zen4All Logo"> Copyright  &COPY; 2008-<?php echo date("Y"); ?> Zen4All
        </div>
      </div>
      <!-- footer //-->
      <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
      <!-- footer_eof //-->
    </div>

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

    <!-- Autoload Product modals -->
    <?php
    foreach (glob(DIR_WS_MODALS . 'product/*.php') as $filename) {
      include $filename;
    }
    ?>
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
    <script>
      // script for tooltips
      $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
      });
      // script for preview popup
      $('#previewPopUp').on('click', (function (e) {
        e.preventDefault();
        $('#previewmodal').modal('show');
      }));
      // script for sliding checkbox
      $('body').on('click', '.radioBtn a', function () {
        var sel = $(this).data('title');
        var tog = $(this).data('toggle');
        $(this).parent().next('.' + tog).prop('value', sel);
        $(this).parent().find('a[data-toggle="' + tog + '"]').not('[data-title="' + sel + '"]').removeClass('active').addClass('notActive');
        $(this).parent().find('a[data-toggle="' + tog + '"][data-title="' + sel + '"]').removeClass('notActive').addClass('active');
      });
    </script>
    <script>
      $('#productInfo').change(function () {
        $('#btnsubmit').removeClass('btn-success').addClass('btn-warning');
      });
      $('#productInfo .radioBtn a').on('click', (function (e) {
        e.preventDefault();
        $('#btnsubmit').removeClass('btn-success').addClass('btn-warning');
      }));
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
    if ($editor_handler != '') {
      include $editor_handler;
    }
    ?>
  </body>
</html>
<?php
require(DIR_WS_INCLUDES . 'application_bottom.php');
