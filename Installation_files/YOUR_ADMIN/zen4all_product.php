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

$productType = (isset($_GET['product_type']) ? $_GET['product_type'] : '');

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

$productInfo = array_merge_recursive($fieldsAvailable, $parameters);

$extraTabsPath = DIR_WS_MODULES . 'extra_tabs';
$extraTabsFiles = recursiveDirList($extraTabsPath);

$productId = (isset($_GET['pID']) ? (int)$_GET['pID'] : '');

if ($productId != '') {

  zen4allCheckProductTables($productId);

  $product = $db->Execute("SELECT p.products_id, p.products_date_added, p.products_last_modified, p.master_categories_id" . $fields . "
                           FROM " . TABLE_PRODUCTS . " p,
                                " . TABLE_PRODUCTS_DESCRIPTION . " pd
                           LEFT JOIN  " . TABLE_META_TAGS_PRODUCTS_DESCRIPTION . " mtpd ON pd.products_id = mtpd.products_id
                             AND mtpd.language_id = pd.language_id
                           " . $tables . "
                           WHERE p.products_id = " . $productId . "
                           AND p.products_id = pd.products_id
                           AND pd.language_id = " . (int)$_SESSION['languages_id']);
  foreach ($product->fields as $fieldName => $value) {
    $productInfo[$fieldName]['value'] = $value;
  }
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
  $cInfo = new objectInfo([]);
}
?>

<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
  <head>
    <meta charset="<?php echo CHARSET; ?>">
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" href="includes/stylesheet.css">
    <link rel="stylesheet" href="includes/css/collect_info.css">
    <link rel="stylesheet" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
    <script src="includes/menu.js"></script>
    <script src="includes/general.js"></script>
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
      <?php
      if ($editor_handler != '') {
        include $editor_handler;
      }
      ?>
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
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
    <!-- header_eof //-->
    <div class="container-fluid">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="col-sm-11"><?php echo sprintf(TEXT_NEW_PRODUCT, zen_output_generated_category_path($current_category_id)); ?></h3>
          <?php echo zen_info_image($cInfo->categories_image, $cInfo->categories_name, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?>
        </div>
        <div class="panel-body">
            <?php
            ?>
          <form name="productInfo" enctype="multipart/form-data" id="productInfo" class="form-horizontal">
              <?php
              echo zen_draw_hidden_field('securityToken', $_SESSION['securityToken']);
              echo zen_draw_hidden_field('cPath', $cPath);
              echo zen_draw_hidden_field('product_type', $productType);
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
                          <?php include PRODUCT_FIELDS_INCLUDES_HTML_OUTPUT_FOLDER . $key . '.php'; ?>
                      </div>
                      <?php
                    }
                  }
                  ?>
                </div>
                <?php
                $tabSortContent = $tab['sortOrder'];
              }
              ?>


              <?php
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
                if (!array_search('categories', $extraTabsFiles) && $productId > 0) {
                  echo zen_draw_hidden_field('master_categories_id', $productInfo['master_categories_id']['value']);
                }
                if (!array_search('discounts', $extraTabsFiles)) {
                  echo zen_draw_hidden_field('products_discount_type', $productInfo['products_discount_type']['value']);
                  echo zen_draw_hidden_field('products_discount_type_from', $productInfo['products_discount_type_from']['value']);
                }
                echo zen_draw_hidden_field('products_price_sorter', $productInfo['products_price_sorter']['value']);
                echo zen_draw_hidden_field('products_date_added', (zen_not_null($productInfo['products_date_added']['value']) ? $productInfo['products_date_added']['value'] : date('Y-m-d')));
                ?>

            </span>

            <div class="btn-group">
              <a id="previewPopUp" class="btn btn-default" name="btnpreview" href="#" role="button">
                <i class="fa fa-tv"></i> <?php echo IMAGE_PREVIEW; ?>
              </a>
              <?php if ($productId != '') { ?>
                <button name="insertButton" id="btnsubmit" class="btn btn-primary" onclick="saveProduct()" type="submit" >
                  <i class="fa fa-save"></i> <?php echo IMAGE_SAVE; ?>
                </button>
              <?php } else { ?>
                <button name="updateButton" id="btnsubmit" class="btn btn-primary" onclick="saveProduct()" type="submit" >
                  <i class="fa fa-save"></i> <?php echo IMAGE_INSERT; ?>
                </button>
              <?php } ?>
              <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . (!empty($productId) ? '&pID=' . $productId : '') . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')); ?>" class="btn btn-warning" id="btncancel" name="btncancel"><i class="fa fa-undo"></i> Back </a>
            </div>
          </form>
        </div>
        <div class="panel-footer text-center">
          <strong>Cittins is developed by <a href="https:zen4all.nl" title="Zen4All">Zen4All</a>.</strong> - Version: <a href="https://www.zen-cart.com/downloads.php?do=file&id=2171"><?php echo MODULE_ZEN4ALL_CITTINS_VERSION; ?></a> - <a href="https://github.com/Zen4All-nl/Zen-Cart-Collect-Info-Through-Tabs-In-New-Style/releases/latest"><i class="fa fa-github fa-lg"></i> Github</a>
        </div>
      </div>
      <!-- footer //-->
      <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
      <!-- footer_eof //-->
    </div>
    <!-- Creates the bootstrap modal where the image will appear -->
    <?php
    list($imageWidth, $imageHeight) = getimagesize(DIR_FS_CATALOG_IMAGES . $pInfo->products_image);
    $mediumWith = (int)MEDIUM_IMAGE_WIDTH;
    if ($imageWidth > $mediumWith) {
      $width = $mediumWith;
    } else {
      $width = $imageWidth;
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
              <?php echo zen_image(DIR_WS_CATALOG_IMAGES . $pInfo->products_image, '', $width) ?>
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
    <?php
    $dir_info = zen_build_subdirectories_array(DIR_FS_CATALOG_IMAGES);
    $default_directory = substr($pInfo->products_image, 0, strpos($pInfo->products_image, DIRECTORY_SEPARATOR) + 1);
    ?>
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
                <?php echo zen_draw_hidden_field('productId', $productId); ?>
                <?php echo zen_draw_hidden_field('current_category_id', $current_category_id); ?>
              <button type="submit" class="btn btn-primary" onclick="saveMainImage();"><i class="fa fa-save"></i></button>
              <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> <?php echo TEXT_CLOSE; ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Product preview modal-->
    <?php include DIR_WS_MODULES . 'product/preview_modal.php'; ?>
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
    ?>
  </body>
</html>
<?php
require(DIR_WS_INCLUDES . 'application_bottom.php');
