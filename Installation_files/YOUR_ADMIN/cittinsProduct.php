<?php
/**
 * @package admin
 * @copyright (c) 2008-2018, Zen4All
 * @copyright Copyright 2003-2019 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Author: Zen4All  Aug 2018  Modified in v1.5.6 $
 */
require 'includes/application_top.php';
$languages = zen_get_languages();

require DIR_WS_CLASSES . 'currencies.php';
$currencies = new currencies();

$productType = (isset($_POST['product_type']) ? $_POST['product_type'] : (isset($_GET['pID']) ? zen_get_products_type($_GET['pID']) : 1));

$parameters = [
  'products_id' => ['value' => ''],
  'products_date_added' => ['value' => ''],
  'products_last_modified' => ['value' => ''],
  'master_categories_id' => ['value' => '']];

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
$productInformation = new objectInfo($productInfo);

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

  $productInformation->updateObjectInfo($productInfo);
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
    <?php require DIR_WS_INCLUDES . 'admin_html_head.php'; ?>
  </head>
  <body>
    <!-- header //-->
    <?php require DIR_WS_INCLUDES . 'header.php'; ?>
    <!-- header_eof //-->
    <div class="container-fluid">
      <div class="panel panel-default">
        <div class="panel-heading">
          <div class="row">
            <h3 class="panel-title col-sm-10"><?php echo sprintf(TEXT_NEW_PRODUCT, zen_output_generated_category_path($current_category_id)); ?></h3>
            <div class="col-sm-2"><?php echo (!empty($cInfo->categories_image) ? zen_info_image($cInfo->categories_image, $cInfo->categories_name, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT) : ''); ?></div>
          </div>
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
                      <a data-toggle="tab" href="#productTabs<?php echo $tabSort; ?>"><?php include DIR_WS_MODULES . 'extra_tabs/' . $tabTitle; ?></a>
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
                  foreach ($productInformation as $key => $infoField) {
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
                      <?php include DIR_WS_MODULES . 'extra_tabs/' . $tabContent; ?>
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
                echo zen_draw_hidden_field('master_categories_id', $productInformation->master_categories_id['value']);
              }
              if (!array_search('quantity_discounts/tab_title_collect_info.php', $extraTabsFiles)) {
                echo zen_draw_hidden_field('products_discount_type', (isset($productInformation->products_discount_type['value']) && $productInformation->products_discount_type['value'] != '' ? $productInformation->products_discount_type['value'] : '0'));
                echo zen_draw_hidden_field('products_discount_type_from', (isset($productInformation->products_discount_type_from['value']) && $productInformation->products_discount_type['value'] != '' ? $productInformation->products_discount_type_from['value'] : '0'));
              }
              echo zen_draw_hidden_field('products_price_sorter', (isset($productInformation->products_price_sorter['value']) && $productInformation->products_price_sorter['value'] != '' ? $productInformation->products_price_sorter['value'] : '0.0000'));
              echo zen_draw_hidden_field('products_date_added', (zen_not_null($productInformation->products_date_added['value']) ? $productInformation->products_date_added['value'] : date('Y-m-d')));
              ?>
            </span>
            <div class="btn-group">
              <a id="previewPopUp" class="btn btn-default" href="#" role="button">
                <i class="fa fa-tv"></i> <?php echo IMAGE_PREVIEW; ?>
              </a>
              <button name="<?php echo ($productId != '' ? 'insertButton' : 'updateButton'); ?>" id="btnsubmit" class="btn btn-success" onclick="saveProduct()" type="submit">
                <i class="fa fa-save"></i> <?php echo ($productId != '' ? IMAGE_SAVE : IMAGE_INSERT); ?>
              </button> <a href="<?php echo zen_href_link(FILENAME_CITTINS_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . (!empty($productId) ? '&pID=' . $productId : '') . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')); ?>" class="btn btn-default" id="btncancel" role="button"><i class="fa fa-undo"></i> Back </a>
            </div>
          </form>
        </div>
        <?php require 'cittinsFooter.php'; ?>
      </div>
      <!-- footer //-->
      <?php require DIR_WS_INCLUDES . 'footer.php'; ?>
      <!-- footer_eof //-->
    </div>
    <?php
    require_once DIR_WS_MODALS . 'messageStackModal.php';
    /* Autoload Product modals */
    foreach (glob(DIR_WS_MODALS . 'cittins/product/*.php') as $filename) {
      include $filename;
    }
    /* Autoload Additional Modals */
    $modalNeedle = 'modal_';
    if (isset($extraTabsFiles) && $extraTabsFiles != '') {
      foreach ($extraTabsFiles as $modalFile) {
        if (strpos($modalFile, $modalNeedle) !== false) {
          include DIR_WS_MODULES . 'extra_tabs/' . $modalFile;
        }
      }
    }
    /* Autoload Additional JavaScript */
    $jscriptNeedle = 'jscript_';
    if (isset($extraTabsFiles) && $extraTabsFiles != '') {
      foreach ($extraTabsFiles as $jscriptFile) {
        if (strpos($jscriptFile, $jscriptNeedle) !== false) {
          include DIR_WS_MODULES . 'extra_tabs/' . $jscriptFile;
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
require DIR_WS_INCLUDES . 'application_bottom.php';
