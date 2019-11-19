<?php
/**
 * @package admin
 * @copyright (c) 2008-2019, Zen4All
 * @copyright Copyright 2003-2018 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Author: Zen4All
 */
require('includes/application_top.php');
$languages = zen_get_languages();
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

$product_type = (isset($_POST['products_id']) ? zen_get_products_type($_POST['products_id']) : (isset($_GET['product_type']) ? $_GET['product_type'] : 1));

$type_admin_handler = $zc_products->get_admin_handler($product_type);

$action = (isset($_GET['action']) ? $_GET['action'] : '');
$search_result = isset($_GET['search']) && zen_not_null($_GET['search']) ? true : false;
if (isset($_GET['page'])) {
  $_GET['page'] = (int)$_GET['page'];
}
if (isset($_GET['product_type'])) {
  $_GET['product_type'] = (int)$_GET['product_type'];
}
if (isset($_GET['cID'])) {
  $_GET['cID'] = (int)$_GET['cID'];
}


$getDefaultColumnsQuery = "SELECT configuration_key, configuration_value
                           FROM " . TABLE_CONFIGURATION . "
                           WHERE configuration_key LIKE '%ZEN4ALL_CITTINS_COLUMN_%'";
$getDefaultColumns = $db->Execute($getDefaultColumnsQuery);
if (!isset($_SESSION['columnVisibility']) || empty($_SESSION['columnVisibility'])) {
  $_SESSION['columnVisibility'] = [];
  foreach ($getDefaultColumns as $item) {
    $strToLower = strtolower(substr($item['configuration_key'], strlen('ZEN4ALL_CITTINS_')));
    $splitArray = explode('_', $strToLower);
    $stringArray = [];
    foreach ($splitArray as $split) {
      $stringArray[] = ucfirst($split);
    }
    $str = implode('', $stringArray);
    $_SESSION['columnVisibility'][$str] = $item['configuration_value'];
  }
}
$columnVisibility = $_SESSION['columnVisibility'];

$zco_notifier->notify('NOTIFY_BEGIN_ADMIN_CATEGORIES', $action);

if (!isset($_SESSION['cittinsCategoriesProductsSortOrder']) || empty($_SESSION['cittinsCategoriesProductsSortOrder'])) {
  $_SESSION['cittinsCategoriesProductsSortOrder'] = ZEN4ALL_CITTINS_DEFAULT_LISTING_SORTORDER;
} elseif (isset($_GET['list_order'])) {
  $_SESSION['cittinsCategoriesProductsSortOrder'] = (int)$_GET['list_order'];
}

if (zen_not_null($action)) {
  switch ($action) {
    case 'set_editor':
      // Reset will be done by init_html_editor.php. Now we simply redirect to refresh page properly.
      $action = '';
      zen_redirect(zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $_GET['cPath'] . ((isset($_GET['pID']) && !empty($_GET['pID'])) ? '&pID=' . $_GET['pID'] : '') . ((isset($_GET['page']) && !empty($_GET['page'])) ? '&page=' . $_GET['page'] : '')));
      break;
    case 'move_product_confirm':
      require(DIR_WS_MODULES . 'zen4all_move_product_confirm.php');
      break;
    case 'copy_product_confirm':
      require(DIR_WS_MODULES . 'zen4all_copy_product_confirm.php');
      break;
    case 'move_product':
    case 'copy_product':
    case 'attribute_features':
    case 'attribute_features_copy_to_product':
    case 'attribute_features_copy_to_category':
      break;
    default:
      $action = $_GET['action'] = '';
      break;
  }
}

// check if the catalog image directory exists
if (is_dir(DIR_FS_CATALOG_IMAGES)) {
  if (!is_writeable(DIR_FS_CATALOG_IMAGES)) {
    $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE, 'error');
  }
} else {
  $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST, 'error');
}
$selectActions = array(
  ['id' => '', 'text' => PLEASE_SELECT],
  ['id' => 'move', 'text' => ACTION_MOVE],
  ['id' => 'delete', 'text' => ACTION_DELETE],
  ['id' => 'copy', 'text' => ACTION_COPY]
);

// check for which buttons to show for categories and products
$check_categories = zen_has_category_subcategories($current_category_id);
$check_products = zen_products_in_category_count($current_category_id, true, false, 1);

$zc_skip_products = false;
$zc_skip_categories = false;

if ($check_products == 0) {
  $zc_skip_products = false;
  $zc_skip_categories = false;
}
if ($check_categories == true) {
  $zc_skip_products = true;
  $zc_skip_categories = false;
}
if ($check_products > 0) {
  $zc_skip_products = false;
  $zc_skip_categories = true;
}
?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
  <head>
    <meta charset="<?php echo CHARSET; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" href="includes/stylesheet.css">
    <link rel="stylesheet" href="includes/css/z4a_categories_product_listing.css">
    <script src="includes/general.js"></script>
  </head>
  <body>
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <!-- body //-->
    <div class="container-fluid">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h1 class="panel-title"><?php echo HEADING_TITLE; ?>&nbsp;-&nbsp;<?php echo zen_output_generated_category_path($current_category_id); ?></h1>
        </div>
        <div class="panel-body">
          <div class="col-md-4">
            <table class="table-condensed">
              <thead>
                <tr>
                  <th class="smallText"><?php echo TEXT_LEGEND; ?></th>
                  <th class="text-center smallText"><?php echo TEXT_LEGEND_STATUS_OFF; ?></th>
                  <th class="text-center smallText"><?php echo TEXT_LEGEND_STATUS_ON; ?></th>
                  <th class="text-center smallText"><?php echo TEXT_LEGEND_LINKED; ?></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td></td>
                  <td class="text-center"><span class="btn btn-xs btn-danger" title="<?php echo IMAGE_ICON_STATUS_OFF; ?>">&nbsp;</span></td>
                  <td class="text-center"><span class="btn btn-xs btn-success" title="<?php echo IMAGE_ICON_STATUS_ON; ?>">&nbsp;</span></td>
                  <td class="text-center"><span class="btn btn-xs btn-warning" title="<?php echo IMAGE_ICON_LINKED; ?>">&nbsp;</span></td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="col-md-4">
            <div>
              <?php
              // toggle switch for editor
              echo zen_draw_form('set_editor_form', FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, '', 'get', 'class="form-horizontal"');
              echo zen_draw_label(TEXT_EDITOR_INFO, 'reset_editor', 'class="col-sm-6 col-md-4 control-label"');
              ?>
              <div class="col-sm-6 col-md-8"><?php echo zen_draw_pull_down_menu('reset_editor', $editors_pulldown, $current_editor_key, 'onchange="this.form.submit();" class="form-control"'); ?></div>
              <?php
              echo zen_hide_session_id();
              echo zen_draw_hidden_field('cID', $cPath);
              echo zen_draw_hidden_field('cPath', $cPath);
              echo (isset($_GET['pID']) ? zen_draw_hidden_field('pID', $_GET['pID']) : '');
              echo (isset($_GET['page']) ? zen_draw_hidden_field('page', $_GET['page']) : '');
              echo zen_draw_hidden_field('action', 'set_editor');
              echo '</form>';
              ?>
            </div>
            <?php
            if (!isset($_GET['page'])) {
              $_GET['page'] = '';
            }
            ?>
            <div class="row"><?php echo zen_draw_separator('pixel_trans.gif', '100%', '1'); ?></div>
            <label class="control-label col-sm-6 col-md-4">View Settings</label>
            <div class="dropdown" id="columnDropDown">
              <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <i class="fa fa-cog"></i>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu checkbox-menu allow-focus" aria-labelledby="dropdownMenu1">
                <li <?php echo ($columnVisibility['ColumnName'] == 'true' ? 'class="active"' : ''); ?>>
                  <label>
                    <input type="checkbox" id="chkBoxColumnName" name="ColumnName" <?php echo ($columnVisibility['ColumnName'] == 'true' ? 'checked' : ''); ?>> Name
                  </label>
                </li>
                <li <?php echo ($columnVisibility['ColumnModel'] == 'true' ? 'class="active"' : ''); ?>>
                  <label>
                    <input type="checkbox" id="chkBoxColumnModel" name="ColumnModel" <?php echo ($columnVisibility['ColumnModel'] == 'true' ? 'checked' : ''); ?>> Model
                  </label>
                </li>
                <li <?php echo ($columnVisibility['ColumnPrice'] == 'true' ? 'class="active"' : ''); ?>>
                  <label>
                    <input type="checkbox" id="chkBoxColumnPrice" name="ColumnPrice" <?php echo ($columnVisibility['ColumnPrice'] == 'true' ? 'checked' : ''); ?>> Price
                  </label>
                </li>
                <li <?php echo ($columnVisibility['ColumnQuantity'] == 'true' ? 'class="active"' : ''); ?>>
                  <label>
                    <input type="checkbox" id="chkBoxColumnQuantity" name="ColumnQuantity" <?php echo ($columnVisibility['ColumnQuantity'] == 'true' ? 'checked' : ''); ?>> Quantity
                  </label>
                </li>
                <li <?php echo ($columnVisibility['ColumnStatus'] == 'true' ? 'class="active"' : ''); ?>>
                  <label>
                    <input type="checkbox" id="chkBoxColumnStatus" name="ColumnStatus" <?php echo ($columnVisibility['ColumnStatus'] == 'true' ? 'checked' : ''); ?>> Status
                  </label>
                </li>
                <li <?php echo ($columnVisibility['ColumnSort'] == 'true' ? 'class="active"' : ''); ?>>
                  <label>
                    <input type="checkbox" id="chkBoxColumnSort" name="ColumnSort" <?php echo ($columnVisibility['ColumnSort'] == 'true' ? 'checked' : ''); ?>> Sort order
                  </label>
                </li>
                <li <?php echo ($columnVisibility['ColumnImage'] == 'true' ? 'class="active"' : ''); ?>>
                  <label>
                    <input type="checkbox" id="chkBoxColumnImage" name="ColumnImage" <?php echo ($columnVisibility['ColumnImage'] == 'true' ? 'checked' : ''); ?>> Image
                  </label>
                </li>
              </ul>
            </div>
          </div>
          <div class="col-md-4">
            <div>
              <?php echo zen_draw_form('searchForm', FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, '', 'get', 'class="form-horizontal"'); ?>
              <?php echo zen_draw_label(HEADING_TITLE_SEARCH_DETAIL, 'search', 'class="col-sm-6 col-md-4 control-label"'); ?>
              <div class="col-sm-6 col-md-8"><?php echo zen_draw_input_field('search', '', ($action == '' ? 'autofocus="autofocus"' : '') . 'class="form-control"'); ?></div>
              <div class="row"><?php echo zen_draw_separator('pixel_trans.gif', '100%', '1'); ?></div>
              <?php
              echo zen_hide_session_id();
              if ($search_result) {
                $keywords = zen_db_input(zen_db_prepare_input($_GET['search']));
                ?>
                <div class="col-sm-6 col-md-4 control-label"><?php echo TEXT_INFO_SEARCH_DETAIL_FILTER; ?></div>
                <div class="col-sm-6 col-md-8">
                  <strong>"<?php echo zen_output_string_protected($_GET['search']); ?>"</strong>
                  <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING); ?>" class="btn btn-default" role="button"><?php echo IMAGE_RESET; ?></a>
                </div>
                <?php
              }
              echo '</form>';
              ?>
            </div>
            <div>
              <div class="col-sm-6 col-md-4 text-right">
                <?php echo zen_draw_label(HEADING_TITLE_GOTO, 'cPath', 'class="control-label"'); ?>
              </div>
              <div class="col-sm-6 col-md-8">
                <?php echo zen_draw_form('goto', FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, '', 'get', 'class="form-horizontal"'); ?>
                <?php echo zen_hide_session_id(); ?>
                <?php echo zen_draw_pull_down_menu('cPath', zen_get_category_tree(), $current_category_id, 'onchange="this.form.submit();" class="form-control"'); ?>
                <?php echo '</form>'; ?>
              </div>
            </div>
          </div>
          <div class="row"><?php echo zen_draw_separator('pixel_black.gif', '100%', '1px'); ?></div>
          <?php //echo zen_draw_form('listing', FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, zen_get_all_get_params(), 'post', 'class="form-horizontal"'); ?>
          <form name="listing" class="form-horizontal">
            <table class="table table-striped" id="categoriesProductListing">
              <thead>
                <tr valign="middle">
                  <th><?php echo zen_draw_checkbox_field('', '', false, '', 'id="select_all"'); ?></th>
                  <th class="text-right shrink">
                    <?php echo (($_GET['list_order'] == '1' || $_GET['list_order_'] == '2') ? '<span class="SortOrderHeader">' . TABLE_HEADING_ID . '</span>' : TABLE_HEADING_ID); ?>&nbsp;<a href="<?php echo zen_href_link(basename($PHP_SELF), zen_get_all_get_params(array('list_order', 'page')) . 'list_order=1'); ?>"><?php echo ($_GET['list_order'] == '1' ? '<i class="fa fa-caret-down fa-2x SortOrderHeader"></i>' : '<i class="fa fa-caret-down fa-2x SortOrderHeaderLink"></i>'); ?></a>&nbsp;<a href="<?php echo zen_href_link(basename($PHP_SELF), zen_get_all_get_params(array('list_order', 'page')) . 'list_order=2'); ?>"><?php echo ($_GET['list_order'] == '2' ? '<i class="fa fa-caret-up fa-2x SortOrderHeader"></i>' : '<i class="fa fa-caret-up fa-2x SortOrderHeaderLink"></i>'); ?></a>
                  </th>
                  <th class="ColumnName noWrap">
                    <?php echo (($_GET['list_order'] == '3' || $_GET['list_order_'] == '4') ? '<span class="SortOrderHeader">' . TABLE_HEADING_CATEGORIES_PRODUCTS . '</span>' : TABLE_HEADING_CATEGORIES_PRODUCTS); ?>&nbsp;<a href="<?php echo zen_href_link(basename($PHP_SELF), zen_get_all_get_params(array('list_order', 'page')) . 'list_order=3'); ?>"><?php echo ($_GET['list_order'] == '3' ? '<i class="fa fa-caret-down fa-2x SortOrderHeader"></i>' : '<i class="fa fa-caret-down fa-2x SortOrderHeaderLink"></i>'); ?></a>&nbsp;<a href="<?php echo zen_href_link(basename($PHP_SELF), zen_get_all_get_params(array('list_order', 'page')) . 'list_order=4'); ?>"><?php echo ($_GET['list_order'] == '4' ? '<i class="fa fa-caret-up fa-2x SortOrderHeader"></i>' : '<i class="fa fa-caret-up fa-2x SortOrderHeaderLink"></i>'); ?></a>
                  </th>
                  <th class="ColumnModel hidden-sm hidden-xs noWrap">
                    <?php echo (($_GET['list_order'] == '5' || $_GET['list_order_'] == '6') ? '<span class="SortOrderHeader">' . TABLE_HEADING_MODEL . '</span>' : TABLE_HEADING_MODEL); ?>&nbsp;<a href="<?php echo zen_href_link(basename($PHP_SELF), zen_get_all_get_params(array('list_order', 'page')) . 'list_order=5'); ?>"><?php echo ($_GET['list_order'] == '5' ? '<i class="fa fa-caret-down fa-2x SortOrderHeader"></i>' : '<i class="fa fa-caret-down fa-2x SortOrderHeaderLink"></i>'); ?></a>&nbsp;<a href="<?php echo zen_href_link(basename($PHP_SELF), zen_get_all_get_params(array('list_order', 'page')) . 'list_order=6'); ?>"><?php echo ($_GET['list_order'] == '6' ? '<i class="fa fa-caret-up fa-2x SortOrderHeader"></i>' : '<i class="fa fa-caret-up fa-2x SortOrderHeaderLink"></i>'); ?></a>
                  </th>
                  <th class="ColumnPrice text-right hidden-sm hidden-xs noWrap">
                    <?php echo (($_GET['list_order'] == '7' || $_GET['list_order_'] == '8') ? '<span class="SortOrderHeader">' . TABLE_HEADING_PRICE . '</span>' : TABLE_HEADING_PRICE); ?>&nbsp;<a href="<?php echo zen_href_link(basename($PHP_SELF), zen_get_all_get_params(array('list_order', 'page')) . 'list_order=7'); ?>"><?php echo ($_GET['list_order'] == '7' ? '<i class="fa fa-caret-down fa-2x SortOrderHeader"></i>' : '<i class="fa fa-caret-down fa-2x SortOrderHeaderLink"></i>'); ?></a>&nbsp;<a href="<?php echo zen_href_link(basename($PHP_SELF), zen_get_all_get_params(array('list_order', 'page')) . 'list_order=8'); ?>"><?php echo ($_GET['list_order'] == '8' ? '<i class="fa fa-caret-up fa-2x SortOrderHeader"></i>' : '<i class="fa fa-caret-up fa-2x SortOrderHeaderLink"></i>'); ?></a>
                  </th>
                  <th class="hidden"></th>
                  <th class="ColumnQuantity text-right hidden-sm hidden-xs noWrap">
                    <?php echo (($_GET['list_order'] == '9' || $_GET['list_order_'] == '10') ? '<span class="SortOrderHeader">' . TABLE_HEADING_QUANTITY . '</span>' : TABLE_HEADING_QUANTITY); ?>&nbsp;<a href="<?php echo zen_href_link(basename($PHP_SELF), zen_get_all_get_params(array('list_order', 'page')) . 'list_order=9'); ?>"><?php echo ($_GET['list_order'] == '9' ? '<i class="fa fa-caret-down fa-2x SortOrderHeader"></i>' : '<i class="fa fa-caret-down fa-2x SortOrderHeaderLink"></i>'); ?></a>&nbsp;<a href="<?php echo zen_href_link(basename($PHP_SELF), zen_get_all_get_params(array('list_order', 'page')) . 'list_order=10'); ?>"><?php echo ($_GET['list_order'] == '10' ? '<i class="fa fa-caret-up fa-2x SortOrderHeader"></i>' : '<i class="fa fa-caret-up fa-2x SortOrderHeaderLink"></i>'); ?></a>
                  </th>
                  <th class="ColumnStatus text-right hidden-sm hidden-xs"><?php echo TABLE_HEADING_STATUS; ?></th>
                  <th class="ColumnSort text-right hidden-sm hidden-xs noWrap">
                    <?php echo (($_GET['list_order'] == '11' || $_GET['list_order'] == '12') ? '<span class="SortOrderHeader">' . TABLE_HEADING_CATEGORIES_SORT_ORDER . '</span>' : TABLE_HEADING_CATEGORIES_SORT_ORDER); ?>&nbsp;<a href="<?php echo zen_href_link(basename($PHP_SELF), zen_get_all_get_params(array('list_order', 'page')) . 'list_order=11'); ?>"><?php echo ($_GET['list_order'] == '11' ? '<i class="fa fa-caret-down fa-2x SortOrderHeader"></i>' : '<i class="fa fa-caret-down fa-2x SortOrderHeaderLink"></i>'); ?></a>&nbsp;<a href="<?php echo zen_href_link(basename($PHP_SELF), zen_get_all_get_params(array('list_order', 'page')) . 'list_order=12'); ?>"><?php echo ($_GET['list_order'] == '12' ? '<i class="fa fa-caret-up fa-2x SortOrderHeader"></i>' : '<i class="fa fa-caret-up fa-2x SortOrderHeaderLink"></i>'); ?></a>
                  </th>
                  <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
                </tr>
              </thead>
              <tbody>
                <?php
                switch ($_SESSION['cittinsCategoriesProductsSortOrder']) {
                  case '1' :
                    $order_by = "c.categories_id ASC, cd.categories_name ASC";
                    break;
                  case '2' :
                    $order_by = "c.categories_id DESC, cd.categories_name DESC";
                    break;
                  case '3' :
                    $order_by = "cd.categories_name ASC";
                    break;
                  case '4' :
                    $order_by = "cd.categories_name DESC";
                    break;
                  case '11' :
                    $order_by = "c.sort_order ASC, cd.categories_name ASC";
                    break;
                  case '12' :
                    $order_by = "c.sort_order DESC, cd.categories_name DESC";
                    break;
                }

                $categories_count = 0;
                $rows = 0;
                if (isset($_GET['search'])) {
                  $search = zen_db_prepare_input($_GET['search']);

                  $categories = $db->Execute("SELECT c.*, cd.categories_name, cd.categories_description
                                              FROM " . TABLE_CATEGORIES . " c
                                              LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON cd.categories_id = c.categories_id
                                                AND cd.language_id = " . (int)$_SESSION['languages_id'] . "
                                              WHERE cd.categories_name LIKE '%" . zen_db_input($search) . "%'
                                              ORDER BY " . $order_by);
                } else {
                  $categories = $db->Execute("SELECT c.*, cd.categories_name, cd.categories_description
                                              FROM " . TABLE_CATEGORIES . " c
                                              LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON cd.categories_id = c.categories_id
                                                AND cd.language_id = " . (int)$_SESSION['languages_id'] . "
                                              WHERE c.parent_id = " . (int)$current_category_id . "
                                              ORDER BY " . $order_by);
                }
                foreach ($categories as $category) {
                  $categories_count++;
                  $rows++;

                  if (isset($_GET['search'])) {
                    $cPath = $category['parent_id'];
                  }
                  ?>
                  <tr id="cID_<?php echo $category['categories_id']; ?>">
                    <td><?php echo zen_draw_checkbox_field('selected_categories[]', $category['categories_id']); ?></td>
                    <td class="text-right"><?php echo $category['categories_id']; ?></td>
                    <td class="ColumnName">
                      <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, zen_get_path($category['categories_id'])); ?>" class="folder">
                        <i class="fa fa-lg fa-folder"></i>&nbsp;<strong><?php echo $category['categories_name']; ?></strong></a>
                    </td>
                    <td class="ColumnModel text-center hidden-sm hidden-xs">&nbsp;</td>
                    <td class="ColumnPrice text-right hidden-sm hidden-xs"><?php echo zen_get_products_sale_discount('', $category['categories_id'], true); ?></td>
                    <td class="hidden"></td>
                    <td class="ColumnQuantity text-right hidden-sm hidden-xs">
                      <?php
                      if (SHOW_COUNTS_ADMIN == 'false') {
                        // don't show counts
                      } else {
                        // show counts
                        $total_products = zen_get_products_to_categories($category['categories_id'], true);
                        $total_products_on = zen_get_products_to_categories($category['categories_id'], false);
                        echo $total_products_on . TEXT_PRODUCTS_STATUS_ON_OF . $total_products . TEXT_PRODUCTS_STATUS_ACTIVE;
                      }
                      ?>
                    </td>
                    <td class="ColumnStatus text-right hidden-sm hidden-xs">
                      <?php if (SHOW_CATEGORY_PRODUCTS_LINKED_STATUS == 'true' && zen_get_products_to_categories($category['categories_id'], true, 'products_active') == 'true') { ?>
                        <span class="btn btn-xs btn-warning" title="<?php echo IMAGE_ICON_LINKED; ?>">&nbsp;</span>
                      <?php } ?>
                      <?php if ($category['categories_status'] == '1') { ?>
                        <button type="button" data-toggle="modal" id="cFlag_<?php echo $category['categories_id']; ?>" title="<?php echo IMAGE_ICON_STATUS_ON; ?>" onclick="setCategoryFlag('<?php echo $category['categories_id']; ?>', '<?php echo $cPath; ?>', '1')" class="btn btn-xs btn-success" data-original-title="<?php echo IMAGE_ICON_STATUS_ON; ?>" data-target="#setCategoryFlagModal">&nbsp;</button>
                      <?php } else { ?>
                        <button type="button" data-toggle="modal" id="cFlag_<?php echo $category['categories_id']; ?>" title="<?php echo IMAGE_ICON_STATUS_OFF; ?>" onclick="setCategoryFlag('<?php echo $category['categories_id']; ?>', '<?php echo $cPath; ?>', '0')" class="btn btn-xs btn-danger" data-original-title="<?php echo IMAGE_ICON_STATUS_OFF; ?>" data-target="#setCategoryFlagModal">&nbsp;</button>
                      <?php } ?>
                    </td>
                    <td class="ColumnSort text-right hidden-sm hidden-xs"><?php echo $category['sort_order']; ?></td>
                    <td class="text-right">
                      <div class="btn-group">
                        <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $category['categories_id'] . ((isset($_GET['search']) && !empty($_GET['search'])) ? '&search=' . $_GET['search'] : '')); ?>" title="<?php echo TEXT_LISTING_EDIT; ?>" class="btn btn-sm btn-info" role="button">
                          <i class="fa fa-pencil fa-lg" aria-hidden="true"></i>
                        </a>
                        <button type="button" data-toggle="modal" title="<?php echo TEXT_LISTING_DELETE; ?>" class="btn btn-sm btn-info" onclick="deleteCategory('<?php echo $category['categories_id']; ?>', '<?php echo $cPath; ?>');" data-original-title="<?php echo TEXT_LISTING_DELETE; ?>" data-target="#deleteCategoryModal">
                          <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
                        </button>
                        <button type="button" data-toggle="modal" title="<?php echo TEXT_LISTING_MOVE; ?>" class="btn btn-sm btn-info" onclick="moveCategory('<?php echo $category['categories_id']; ?>', '<?php echo $cPath; ?>');" data-original-title="<?php echo TEXT_LISTING_MOVE; ?>" data-target="#moveCategoryModal">
                          <i class="fa fa-arrows fa-lg" aria-hidden="true"></i>
                        </button>
                        <?php if (zen_get_category_metatags_keywords($category['categories_id'], (int)$_SESSION['languages_id']) or zen_get_category_metatags_description($category['categories_id'], (int)$_SESSION['languages_id'])) { ?>
                          <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $category['categories_id'] . '&action=edit_category_meta_tags' . '&activeTab=categoryTabs4'); ?>" title="<?php echo TEXT_LISTING_EDIT_META_TAGS; ?>" class="btn btn-sm btn-info" role="button">
                            <i class="fa fa-asterisk fa-lg" aria-hidden="true"></i>
                          </a>
                        <?php } else { ?>
                          <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $category['categories_id'] . '&action=edit_category_meta_tags' . '&activeTab=categoryTabs4'); ?>" title="<?php echo TEXT_LISTING_EDIT_META_TAGS; ?>" class="btn btn-sm btn-info" role="button">
                            <i class="fa fa-asterisk fa-lg" aria-hidden="true"></i>
                          </a>
                        <?php } ?>
                      </div>
                    </td>
                  </tr>
                  <?php
                }

                switch ($_SESSION['cittinsCategoriesProductsSortOrder']) {
                  case '1':
                    $order_by = "p.products_id ASC, pd.products_name ASC";
                    break;
                  case '2':
                    $order_by = "p.products_id DESC, pd.products_name DESC";
                    break;
                  case '3':
                    $order_by = "pd.products_name ASC";
                    break;
                  case '4':
                    $order_by = "pd.products_name DESC";
                    break;
                  case '5':
                    $order_by = "p.products_model ASC";
                    break;
                  case '6':
                    $order_by = "p.products_model DESC";
                    break;
                  case '7':
                    $order_by = "p.products_price_sorter ASC";
                    break;
                  case '8':
                    $order_by = "p.products_price_sorter DESC";
                    break;
                  case '9':
                    $order_by = "p.products_quantity ASC";
                    break;
                  case '10':
                    $order_by = "p.products_quantity DESC";
                    break;
                  case '11':
                    $order_by = "p.products_sort_order ASC, pd.products_name ASC";
                    break;
                  case '12':
                    $order_by = "p.products_sort_order DESC, pd.products_name DESC";
                    break;
                }

                $products_count = 0;
                if ($search_result && $action != 'edit_category') {
                  $products_query_raw = ("SELECT p.*, pd.products_name, p2c.categories_id
                                          FROM " . TABLE_PRODUCTS . " p
                                          LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON pd.products_id = p.products_id
                                            AND pd.language_id = " . (int)$_SESSION['languages_id'] . "
                                          LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON p2c.products_id = p.products_id
                                          WHERE p2c.categories_id = p.master_categories_id
                                          AND (pd.products_name LIKE '%" . zen_db_input($_GET['search']) . "%'
                                            OR pd.products_description LIKE '%" . zen_db_input($_GET['search']) . "%'
                                            OR p.products_id = '" . zen_db_input($_GET['search']) . "'
                                            OR p.products_model like '%" . zen_db_input($_GET['search']) . "%'
                                            )
                                          ORDER BY " . $order_by);
                } else {
                  $products_query_raw = ("SELECT p.*, pd.products_name
                                          FROM " . TABLE_PRODUCTS . " p
                                          LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON pd.products_id = p.products_id
                                            AND pd.language_id = " . (int)$_SESSION['languages_id'] . "
                                          LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON p2c.products_id = p.products_id
                                          WHERE p2c.categories_id = " . (int)$current_category_id . "
                                          ORDER BY " . $order_by);
                }
// Split Page
// reset page when page is unknown
                if ((isset($_GET['page']) && ($_GET['page'] == '1' || $_GET['page'] == '')) && isset($_GET['pID']) && $_GET['pID'] != '') {
                  $old_page = $_GET['page'];
                  $check_page = $db->Execute($products_query_raw);
                  if ($check_page->RecordCount() > MAX_DISPLAY_RESULTS_CATEGORIES) {
                    $check_count = 1;
                    foreach ($check_page as $item) {
                      if ($item['products_id'] == $_GET['pID']) {
                        break;
                      }
                      $check_count++;
                    }
                    $_GET['page'] = round((($check_count / MAX_DISPLAY_RESULTS_CATEGORIES) + (fmod_round($check_count, MAX_DISPLAY_RESULTS_CATEGORIES) != 0 ? .5 : 0)), 0);
                    $page = $_GET['page'];
                    if ($old_page != $_GET['page']) {
//      zen_redirect(zen_href_link(FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&pID=' . $_GET['pID'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
                    }
                  } else {
                    $_GET['page'] = 1;
                  }
                }
                $products_split = new splitPageResults($_GET['page'], MAX_DISPLAY_RESULTS_CATEGORIES, $products_query_raw, $products_query_numrows);
                $products = $db->Execute($products_query_raw);
// Split Page

                foreach ($products as $product) {
                  $products_count++;
                  $rows++;

// Get categories_id for product if search
                  if (isset($_GET['search'])) {
                    $cPath = $product['categories_id'];
                  }

                  $type_handler = $zc_products->get_handler($product['products_type']);
                  ?>
                  <tr id="pID_<?php echo $product['products_id']; ?>">
                    <td><?php echo zen_draw_checkbox_field('selected_products[]', $product['products_id']); ?></td>
                    <td class="text-right"><?php echo $product['products_id']; ?></td>
                    <td class="ColumnName"><a href="<?php echo zen_catalog_href_link($type_handler . '_info', 'cPath=' . $cPath . '&products_id=' . $product['products_id'] . '&language=' . $_SESSION['languages_code'] . '&product_type=' . $product['products_type']); ?>" target="_blank"><?php echo zen_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW); ?></a>&nbsp;<?php echo $product['products_name']; ?></td>
                    <td class="ColumnModel hidden-sm hidden-xs"><?php echo $product['products_model']; ?></td>
                    <td class="ColumnPrice text-right hidden-sm hidden-xs"><?php echo zen_get_products_display_price($product['products_id']); ?></td>
                    <td class="hidden"></td>
                    <td class="ColumnQuantity text-right hidden-sm hidden-xs"><?php echo $product['products_quantity']; ?></td>
                    <td class="ColumnStatus text-right hidden-sm hidden-xs text-nowrap">
                      <?php if (zen_get_product_is_linked($product['products_id']) == 'true') { ?>
                        <span class="btn btn-xs btn-warning" title="<?php echo IMAGE_ICON_LINKED; ?>">&nbsp;</span>&nbsp;
                      <?php } ?>
                      <?php if ($product['products_status'] == '1') { ?>
                        <button type="button" id="flag_<?php echo $product['products_id']; ?>" title="<?php echo IMAGE_ICON_STATUS_ON; ?>" onclick="setProductFlag('<?php echo $product['products_id']; ?>', '0')" class="btn btn-xs btn-success">&nbsp;</button>
                      <?php } else { ?>
                        <button type="button" id="flag_<?php echo $product['products_id']; ?>" title="<?php echo IMAGE_ICON_STATUS_OFF; ?>" onclick="setProductFlag('<?php echo $product['products_id']; ?>', '1')" class="btn btn-xs btn-danger">&nbsp;</button>
                      <?php } ?>
                    </td>
                    <td class="ColumnSort text-right hidden-sm hidden-xs"><?php echo $product['products_sort_order']; ?></td>
                    <td class="text-right">
                      <div class="btn-group">
                        <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_PRODUCT, 'cPath=' . $cPath . '&product_type=' . $product['products_type'] . '&pID=' . $product['products_id'] . '&action=new_product' . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '')); ?>" title="<?php echo TEXT_LISTING_EDIT; ?>" class="btn btn-sm btn-info" role="button">
                          <i class="fa fa-pencil fa-lg" aria-hidden="true"></i>
                        </a>
                        <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&product_type=' . $product['products_type'] . '&pID=' . $product['products_id'] . '&action=delete_product'); ?>" title="<?php echo TEXT_LISTING_DELETE; ?>" class="btn btn-sm btn-info" role="button">
                          <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
                        </a>
                        <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&product_type=' . $product['products_type'] . '&pID=' . $product['products_id'] . '&action=move_product'); ?>" title="<?php echo TEXT_LISTING_MOVE; ?>" class="btn btn-sm btn-info" role="button">
                          <i class="fa fa-arrows fa-lg" aria-hidden="true"></i>
                        </a>
                        <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&product_type=' . $product['products_type'] . '&pID=' . $product['products_id'] . '&action=copy_product'); ?>" title="<?php echo TEXT_LISTING_COPY; ?>" class="btn btn-sm btn-info" role="button">
                          <i class="fa fa-copy fa-lg" aria-hidden="true"></i>
                        </a>

                        <?php if (defined('FILENAME_IMAGE_HANDLER') && file_exists(DIR_FS_ADMIN . FILENAME_IMAGE_HANDLER . '.php')) { ?>
                          <a href="<?php echo zen_href_link(FILENAME_IMAGE_HANDLER, 'products_filter=' . $product['products_id'] . '&current_category_id=' . $current_category_id); ?>" title="<?php echo TEXT_LISTING_IMAGE_HANDLER; ?>" class="btn btn-sm btn-info" role="button">
                            <i class="fa fa-lg fa-image" aria-hidden="true"></i>
                          </a>
                        <?php } ?>
                        <?php if (zen_has_product_attributes($product['products_id'], 'false')) { ?>
                          <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $product['products_id'] . '&action=attribute_features' . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')); ?>" title="<?php echo TEXT_LISTING_ATTRIBUTES; ?>" class="btn btn-sm btn-info" role="button">
                            <i class="fa fa-list fa-lg attributes-on" aria-hidden="true"></i>
                          </a>
                        <?php } else { ?>
                          <a href="<?php echo zen_href_link(FILENAME_ATTRIBUTES_CONTROLLER, 'products_filter=' . $product['products_id'] . '&current_category_id=' . $current_category_id); ?>" title="<?php echo TEXT_LISTING_ATTRIBUTES; ?>" class="btn btn-sm btn-info" role="button">
                            <i class="fa fa-list fa-lg" aria-hidden="true"></i>
                          </a>
                        <?php } ?>
                        <?php if ($zc_products->get_allow_add_to_cart($product['products_id']) == 'Y') { ?>
                          <a href="<?php echo zen_href_link(FILENAME_PRODUCTS_PRICE_MANAGER, 'products_filter=' . $product['products_id'] . '&current_category_id=' . $current_category_id); ?>" title="<?php echo TEXT_LISTING_PRICE_MANAGER; ?>" class="btn btn-sm btn-info" role="button">
                            <i class="fa fa-dollar fa-lg pricemanager-on" aria-hidden="true"></i>
                          </a>
                        <?php } else { ?>
                          <a href="#" class="btn btn-sm btn-info" role="button">
                            <i class="fa fa-dollar fa-lg" aria-hidden="true"></i>
                          </a>
                          <?php
                        }
                        if (zen_get_metatags_keywords($product['products_id'], (int)$_SESSION['languages_id']) || zen_get_metatags_description($product['products_id'], (int)$_SESSION['languages_id'])) {
                          ?>
                          <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_PRODUCT, 'page=' . $_GET['page'] . '&product_type=' . $product['products_type'] . '&cPath=' . $cPath . '&pID=' . $product['products_id']); ?>" title="<?php echo TEXT_LISTING_EDIT_META_TAGS; ?>" class="btn btn-sm btn-info" role="button">
                            <i class="fa fa-asterisk fa-lg metatags-on" aria-hidden="true"></i>
                          </a>
                        <?php } else { ?>
                          <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_PRODUCT, 'page=' . $_GET['page'] . '&product_type=' . $product['products_type'] . '&cPath=' . $cPath . '&pID=' . $product['products_id']); ?>" title="<?php echo TEXT_LISTING_EDIT_META_TAGS; ?>" class="btn btn-sm btn-info" role="button">
                            <i class="fa fa-asterisk fa-lg" aria-hidden="true"></i>
                          </a>
                        <?php } ?>
                      </div>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="10">
                    <?php echo zen_draw_label(WITH_SELECTED, 'action_select', 'class="col-xs-2 col-sm-2 control-label"'); ?>
                    <div class="col-xs-3 col-sm-3"><?php echo zen_draw_pull_down_menu('action_select', $selectActions, '', 'class="form-control"'); ?></div>
                    <?php echo zen_draw_hidden_field('cPath', $cPath); ?>
                  </td>
                </tr>
              </tfoot>
            </table>
          </form>
          <?php
          $heading = [];
          $contents = [];
          switch ($action) {
            case 'delete_product':
              if (file_exists(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/zen4all_delete_product.php')) {
                require(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/zen4all_delete_product.php');
              } else {
                require(DIR_WS_MODULES . 'zen4all_delete_product.php');
              }
              break;
            case 'move_product':
              if (file_exists(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/zen4all_move_product.php')) {
                require(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/zen4all_move_product.php');
              } else {
                require(DIR_WS_MODULES . 'zen4all_move_product.php');
              }
              break;
            case 'copy_product':
              if (file_exists(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/zen4all_copy_product.php')) {
                require(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/zen4all_copy_product.php');
              } else {
                require(DIR_WS_MODULES . 'zen4all_copy_product.php');
              }
              break;
          }
          if ((zen_not_null($heading)) && (zen_not_null($contents))) {
            $box = new box;
            echo '<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 configurationColumnRight">';
            echo $box->infoBox($heading, $contents);
            echo '</div>';
          }

          $cPathBackRaw = '';
          if (sizeof($cPath_array) > 0) {
            for ($i = 0, $n = sizeof($cPath_array) - 1; $i < $n; $i++) {
              if (empty($cPathBackRaw)) {
                $cPathBackRaw .= $cPath_array[$i];
              } else {
                $cPathBackRaw .= '_' . $cPath_array[$i];
              }
            }
          }

          $cPath_back = (zen_not_null($cPathBackRaw)) ? 'cPath=' . $cPathBackRaw . '&' : '';
          ?>
          <table class="table">
            <tr>
              <?php if (sizeof($cPath_array) > 0) { ?>
                <td class="text-right">
                  <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, $cPath_back . 'cID=' . $current_category_id); ?>" class="btn btn-default" role="button"><?php echo IMAGE_BACK; ?></a>
                </td>
                <?php
              }
              if (!isset($_GET['search']) && !$zc_skip_categories) {
                ?>
                <td class="text-right">
                  <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES, 'cPath=' . $cPath . '&action=new_category'); ?>" class="btn btn-primary" role="button"><?php echo IMAGE_NEW_CATEGORY; ?></a>
                </td>
                <?php
              }
              if ($zc_skip_products == false) {
                echo zen_draw_form('newproduct', FILENAME_ZEN4ALL_PRODUCT, 'cPath=' . $cPath . '&product_type=' . $product['products_type'] . '&action=new_product' . (isset($_GET['search']) ? '&search=' . $_GET['search'] : ''), 'post', 'class="form-horizontal"');
                if (empty($_GET['search'])) {
                  ?>
                  <td class="text-right"><button type="submit" class="btn btn-primary"><?php echo IMAGE_NEW_PRODUCT; ?></button></td>
                  <?php
                }
                // Query product types based on the ones this category is restricted to
                $sql = "SELECT ptc.product_type_id AS type_id, pt.type_name
                        FROM " . TABLE_PRODUCT_TYPES_TO_CATEGORY . " ptc,
                             " . TABLE_PRODUCT_TYPES . " pt
                        WHERE ptc.category_id = " . (int)$current_category_id . "
                        AND pt.type_id = ptc.product_type_id";
                $product_types = $db->Execute($sql);

                if ($product_types->RecordCount() == 0) {
                  // There are no restricted product types so make we offer all types instead
                  $sql = "SELECT * FROM " . TABLE_PRODUCT_TYPES;
                  $product_types = $db->Execute($sql);
                }

                $product_restrict_types_array = [];

                foreach ($product_types as $restrict_type) {
                  $product_restrict_types_array[] = [
                    'id' => $restrict_type['type_id'],
                    'text' => $restrict_type['type_name'],
                  ];
                }
                ?>
                <td><?php echo zen_draw_pull_down_menu('product_type', $product_restrict_types_array, '', 'class="form-control"'); ?></td>
                <?php
                echo zen_hide_session_id();
                echo zen_draw_hidden_field('cPath', $cPath);
                echo zen_draw_hidden_field('action', 'new_product');
                echo '</form>';
              } else {
                ?>
                <td><?php echo CATEGORY_HAS_SUBCATEGORIES; ?></td>
                <?php
              } // hide has cats
              ?>
            </tr>
          </table>
        </div>
        <div class="panel-footer">
          <table class="table">
            <tr>
              <td colspan="2"><?php echo TEXT_CATEGORIES; ?>&nbsp;<?php echo $categories_count; ?><br><?php echo TEXT_PRODUCTS; ?>&nbsp;<?php echo $products_count; ?></td>
            </tr>
            <?php
            // warning if products are in top level categories
            $check_products_top_categories = $db->Execute("SELECT COUNT(*) AS products_errors
                                                           FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                                                           WHERE categories_id = 0");
            if ($check_products_top_categories->fields['products_errors'] > 0) {
              ?>
              <tr>
                <td colspan="2" class="text-center alert">
                  <?php echo WARNING_PRODUCTS_IN_TOP_INFO . $check_products_top_categories->fields['products_errors']; ?>
                </td>
              </tr>
              <?php
            }
            if ($products_query_numrows > 0) {
              ?>
              <tr>
                <td><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_RESULTS_CATEGORIES, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
                <td class="text-right"><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_RESULTS_CATEGORIES, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params(array('page', 'info', 'x', 'y', 'pID'))); ?></td>
              </tr>
            <?php } ?>
          </table>
        </div>
      </div>
    </div>
    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
    <?php require_once DIR_WS_MODALS . 'categoriesProductListing/modalDeleteCategory.php'; ?>
    <?php require_once DIR_WS_MODALS . 'categoriesProductListing/modalMoveCategory.php'; ?>
    <?php require_once DIR_WS_MODALS . 'categoriesProductListing/modalSetFlagCategories.php'; ?>
    <?php require_once DIR_WS_MODALS . 'messageStackModal.php'; ?>
    <?php require_once 'includes/javascript/zen4all_jscript_CategoriesProductListing.php'; ?>
    <?php
    if ($action != 'edit_category_meta_tags') { // bof: categories meta tags
      if ($editor_handler != '') {
        include ($editor_handler);
      }
    } // meta tags disable editor eof: categories meta tags
    ?>
  </body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>