<?php
/**
 * @package admin
 * @copyright (c) 2008-2018, Zen4All
 * @copyright Copyright 2003-2018 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Author: Zen4All
 */
require('includes/application_top.php');
$languages = zen_get_languages();
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

$product_type = (isset($_POST['products_id']) ? zen_get_products_type($_POST['products_id']) : isset($_GET['product_type']) ? $_GET['product_type'] : 1);

$type_admin_handler = $zc_products->get_admin_handler($product_type);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if (isset($_GET['page'])) {
  $_GET['page'] = (int)$_GET['page'];
}
if (isset($_GET['product_type'])) {
  $_GET['product_type'] = (int)$_GET['product_type'];
}
if (isset($_GET['cID'])) {
  $_GET['cID'] = (int)$_GET['cID'];
}

$zco_notifier->notify('NOTIFY_BEGIN_ADMIN_CATEGORIES', $action);

if (!isset($_SESSION['categories_products_sort_order'])) {
  $_SESSION['categories_products_sort_order'] = CATEGORIES_PRODUCTS_SORT_ORDER;
}

if (!isset($_GET['reset_categories_products_sort_order'])) {
  $reset_categories_products_sort_order = $_SESSION['categories_products_sort_order'];
}

if (zen_not_null($action)) {
  switch ($action) {
    case 'set_categories_products_sort_order':
      $_SESSION['categories_products_sort_order'] = $_GET['reset_categories_products_sort_order'];
      $action = '';
      zen_redirect(zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $_GET['cPath'] . ((isset($_GET['pID']) and ! empty($_GET['pID'])) ? '&pID=' . $_GET['pID'] : '') . ((isset($_GET['page']) and ! empty($_GET['page'])) ? '&page=' . $_GET['page'] : '')));
      break;
    case 'set_editor':
      // Reset will be done by init_html_editor.php. Now we simply redirect to refresh page properly.
      $action = '';
      zen_redirect(zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $_GET['cPath'] . ((isset($_GET['pID']) and ! empty($_GET['pID'])) ? '&pID=' . $_GET['pID'] : '') . ((isset($_GET['page']) and ! empty($_GET['page'])) ? '&page=' . $_GET['page'] : '')));
      break;
    case 'update_category_status':
      // disable category and products including subcategories
      if (isset($_POST['categories_id'])) {
        $categories_id = zen_db_prepare_input($_POST['categories_id']);

        $categories = zen_get_category_tree($categories_id, '', '0', '', true);

        for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {
          $product_ids = $db->Execute("SELECT products_id
                                       FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                                       WHERE categories_id = " . (int)$categories[$i]['id']);

          foreach ($product_ids as $product_id) {
            $products[$product_id['products_id']]['categories'][] = $categories[$i]['id'];
          }
        }

        // change the status of categories and products
        zen_set_time_limit(600);
        for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {
          if ($_POST['categories_status'] == '1') {
            $categories_status = '0';
            $products_status = '0';
          } else {
            $categories_status = '1';
            $products_status = '1';
          }

          $sql = "UPDATE " . TABLE_CATEGORIES . "
                  SET categories_status = " . (int)$categories_status . "
                  WHERE categories_id = " . (int)$categories[$i]['id'];
          $db->Execute($sql);

          // set products_status based on selection
          if ($_POST['set_products_status'] == 'set_products_status_nochange') {
            // do not change current product status
          } else {
            if ($_POST['set_products_status'] == 'set_products_status_on') {
              $products_status = '1';
            } else {
              $products_status = '0';
            }

            $sql = "SELECT products_id
                    FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                    WHERE categories_id = " . (int)$categories[$i]['id'];
            $category_products = $db->Execute($sql);

            foreach ($category_products as $category_product) {
              $sql = "UPDATE " . TABLE_PRODUCTS . "
                      SET products_status = " . (int)$products_status . "
                      WHERE products_id = " . (int)$category_product['products_id'];
              $db->Execute($sql);
            }
          }
        } // for
      }
      zen_redirect(zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $_GET['cPath'] . '&cID=' . $_GET['cID'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . ((isset($_GET['search']) && !empty($_GET['search'])) ? '&search=' . $_GET['search'] : '')));
      break;
    case 'remove_type':
      if (isset($_POST['type_id'])) {
        $sql = "DELETE FROM " . TABLE_PRODUCT_TYPES_TO_CATEGORY . "
                WHERE category_id = " . (int)zen_db_prepare_input($_GET['cID']) . "
                AND product_type_id = " . (int)zen_db_prepare_input($_POST['type_id']);

        $db->Execute($sql);
        zen_remove_restrict_sub_categories($_GET['cID'], (int)$_POST['type_id']);
        $action = 'edit';
        zen_redirect(zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'action=edit_category&cPath=' . $_GET['cPath'] . '&cID=' . zen_db_prepare_input($_GET['cID'])));
      }
      break;
    case 'setflag':

      if (isset($_POST['flag']) && ($_POST['flag'] == '0') || ($_POST['flag'] == '1')) {
        if (isset($_GET['pID'])) {
          zen_set_product_status($_GET['pID'], $_POST['flag']);
        }
      }

      zen_redirect(zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $_GET['cPath'] . '&pID=' . $_GET['pID'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . ((isset($_GET['search']) && !empty($_GET['search'])) ? '&search=' . $_GET['search'] : '')));
      break;
    case 'delete_category_confirm':

      // future cat specific deletion
      $delete_linked = 'true';
      if (isset($_POST['delete_linked']) && $_POST['delete_linked'] != '') {
        $delete_linked = $_POST['delete_linked'];
      }

      // delete category and products
      if (isset($_POST['categories_id']) && $_POST['categories_id'] != '' && is_numeric($_POST['categories_id']) && $_POST['categories_id'] != 0) {
        $categories_id = zen_db_prepare_input($_POST['categories_id']);

        // create list of any subcategories in the selected category,
        $categories = zen_get_category_tree($categories_id, '', '0', '', true);

        zen_set_time_limit(600);

        // loop through this cat and subcats for delete-processing.
        for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {
          $sql = "SELECT products_id
                  FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                  WHERE categories_id = " . $categories[$i]['id'];
          $category_products = $db->Execute($sql);

          foreach ($category_products as $category_product) {
            $cascaded_prod_id_for_delete = $category_product['products_id'];
            $cascaded_prod_cat_for_delete = [];
            $cascaded_prod_cat_for_delete[] = $categories[$i]['id'];
            // determine product-type-specific override script for this product
            $product_type = zen_get_products_type($category_product['products_id']);
            // now loop thru the delete_product_confirm script for each product in the current category
            // NOTE: Debug code left in to help with creating additional product type delete-scripts

            $do_delete_flag = false;
            if (isset($_POST['products_id']) && isset($_POST['product_categories']) && is_array($_POST['product_categories'])) {
              $product_id = zen_db_prepare_input($_POST['products_id']);
              $product_categories = $_POST['product_categories'];
              $do_delete_flag = true;
            }

            if (zen_not_null($cascaded_prod_id_for_delete) && zen_not_null($cascaded_prod_cat_for_delete)) {
              $product_id = $cascaded_prod_id_for_delete;
              $product_categories = $cascaded_prod_cat_for_delete;
              $do_delete_flag = true;
            }

            if ($do_delete_flag) {
              //--------------PRODUCT_TYPE_SPECIFIC_INSTRUCTIONS_GO__BELOW_HERE--------------------------------------------------------
              if (file_exists(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/z4a_delete_product_confirm.php')) {
                require(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/z4a_delete_product_confirm.php');
              }
              //--------------PRODUCT_TYPE_SPECIFIC_INSTRUCTIONS_GO__ABOVE__HERE--------------------------------------------------------
              // now do regular non-type-specific delete:
              // remove product from all its categories:
              for ($k = 0, $m = sizeof($product_categories); $k < $m; $k++) {
                $db->Execute("DELETE FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                              WHERE products_id = " . (int)$product_id . "
                              AND categories_id = " . (int)$product_categories[$k]);
              }
              // confirm that product is no longer linked to any categories
              $count_categories = $db->Execute("SELECT COUNT(categories_id) AS total
                                                FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                                                WHERE products_id = " . (int)$product_id);
              // echo 'count of category links for this product=' . $count_categories->fields['total'] . '<br />';
              // if not linked to any categories, do delete:
              if ($count_categories->fields['total'] == '0') {
                zen_remove_product($product_id, $delete_linked);
              }
            } // endif $do_delete_flag
            // if this is a single-product delete, redirect to categories page
            // if not, then this file was called by the cascading delete initiated by the category-delete process
            if ($action == 'delete_product_confirm') {
              zen_redirect(zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath));
            }
          }

          zen_remove_category($categories[$i]['id']);
        } // end for loop
      }
      zen_redirect(zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath));
      break;
    case 'move_category_confirm':
      if (isset($_POST['categories_id']) && ($_POST['categories_id'] != $_POST['move_to_category_id'])) {
        $categories_id = zen_db_prepare_input($_POST['categories_id']);
        $new_parent_id = zen_db_prepare_input($_POST['move_to_category_id']);

        $path = explode('_', zen_get_generated_category_path_ids($new_parent_id));

        if (in_array($categories_id, $path)) {
          $messageStack->add_session(ERROR_CANNOT_MOVE_CATEGORY_TO_PARENT, 'error');

          zen_redirect(zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath));
        } else {

          $sql = "SELECT COUNT(*) AS count
                  FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                  WHERE categories_id = " . (int)$new_parent_id;
          $zc_count_products = $db->Execute($sql);

          if ($zc_count_products->fields['count'] > 0) {
            $messageStack->add_session(ERROR_CATEGORY_HAS_PRODUCTS, 'error');
          } else {
            $messageStack->add_session(SUCCESS_CATEGORY_MOVED, 'success');
          }

          $db->Execute("UPDATE " . TABLE_CATEGORIES . "
                        SET parent_id = " . (int)$new_parent_id . ", last_modified = now()
                        WHERE categories_id = " . (int)$categories_id);

          // fix here - if this is a category with subcats it needs to know to loop through
          // reset all products_price_sorter for moved category products
          $reset_price_sorter = $db->Execute("SELECT products_id
                                              FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                                              WHERE categories_id = " . (int)$categories_id);
          foreach ($reset_price_sorter as $item) {
            zen_update_products_price_sorter($item['products_id']);
          }

          zen_redirect(zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $new_parent_id));
        }
      } else {
        $messageStack->add_session(ERROR_CANNOT_MOVE_CATEGORY_TO_CATEGORY_SELF . $cPath, 'error');
        zen_redirect(zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath));
      }
      break;
    case 'delete_product_confirm':
      $delete_linked = 'true';
      if ($_POST['delete_linked'] == 'delete_linked_no') {
        $delete_linked = 'false';
      } else {
        $delete_linked = 'true';
      }
      if (file_exists(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/z4a_delete_product_confirm.php')) {
        require(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/delete_product_confirm.php');
      } else {
        require(DIR_WS_MODULES . 'z4a_delete_product_confirm.php');
      }
      break;
    case 'move_product_confirm':
      if (file_exists(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/z4a_move_product_confirm.php')) {
        require(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/move_product_confirm.php');
      } else {
        require(DIR_WS_MODULES . 'z4a_move_product_confirm.php');
      }
      break;
    case 'copy_product_confirm':
      if (file_exists(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/z4a_copy_product_confirm.php')) {
        require(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/copy_product_confirm.php');
      } else {
        require(DIR_WS_MODULES . 'z4a_copy_product_confirm.php');
      }
      break;
    case 'delete_attributes':
      zen_delete_products_attributes($_GET['products_id']);
      $messageStack->add_session(SUCCESS_ATTRIBUTES_DELETED . ' ID#' . $_GET['products_id'], 'success');
      $action = '';

      // reset products_price_sorter for searches etc.
      zen_update_products_price_sorter($_GET['products_id']);

      zen_redirect(zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $_GET['products_id'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
      break;
    case 'update_attributes_sort_order':
      zen_update_attributes_products_option_values_sort_order($_GET['products_id']);
      $messageStack->add_session(SUCCESS_ATTRIBUTES_UPDATE . ' ID#' . $_GET['products_id'], 'success');
      $action = '';
      zen_redirect(zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $_GET['products_id'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
      break;

    // attributes copy to product
    case 'update_attributes_copy_to_product':
      $copy_attributes_delete_first = ($_POST['copy_attributes'] == 'copy_attributes_delete' ? '1' : '0');
      $copy_attributes_duplicates_skipped = ($_POST['copy_attributes'] == 'copy_attributes_ignore' ? '1' : '0');
      $copy_attributes_duplicates_overwrite = ($_POST['copy_attributes'] == 'copy_attributes_update' ? '1' : '0');
      zen_copy_products_attributes($_POST['products_id'], $_POST['products_update_id']);
      //      die('I would copy Product ID#' . $_POST['products_id'] . ' to a Product ID#' . $_POST['products_update_id'] . ' - Existing attributes ' . $_POST['copy_attributes']);
      $_GET['action'] = '';
      zen_redirect(zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $_GET['products_id'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
      break;

    // attributes copy to category
    case 'update_attributes_copy_to_category':
      $copy_attributes_delete_first = ($_POST['copy_attributes'] == 'copy_attributes_delete' ? '1' : '0');
      $copy_attributes_duplicates_skipped = ($_POST['copy_attributes'] == 'copy_attributes_ignore' ? '1' : '0');
      $copy_attributes_duplicates_overwrite = ($_POST['copy_attributes'] == 'copy_attributes_update' ? '1' : '0');
      $copy_to_category = $db->Execute("SELECT products_id
                                        FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                                        WHERE categories_id = " . (int)$_POST['categories_update_id']);
      foreach ($copy_to_category as $item) {
        zen_copy_products_attributes($_POST['products_id'], $item['products_id']);
      }
      //      die('CATEGORIES - I would copy Product ID#' . $_POST['products_id'] . ' to a Category ID#' . $_POST['categories_update_id']  . ' - Existing attributes ' . $_POST['copy_attributes'] . ' Total Products ' . $copy_to_category->RecordCount());

      $_GET['action'] = '';
      zen_redirect(zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $_GET['products_id'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
      break;
    case 'setflag_categories':
    case 'delete_category':
    case 'move_category':
    case 'delete_product':
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
?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
  <head>
    <meta charset="<?php echo CHARSET; ?>">
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
    <link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
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
    <?php
    if ($action != 'edit_category_meta_tags') { // bof: categories meta tags
      if ($editor_handler != '') {
        include ($editor_handler);
      }
    } // meta tags disable editor eof: categories meta tags
    ?>
  </head>
  <body onload="init();">
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <!-- body //-->
    <div class="container-fluid">
      <h1><?php echo HEADING_TITLE; ?>&nbsp;-&nbsp;<?php echo zen_output_generated_category_path($current_category_id); ?></h1>
      <?php if ($action == '') { ?>
        <div class="col-md-4">
          <table class="table-condensed">
            <thead>
              <tr>
                <th><?php echo TEXT_LEGEND; ?></th>
                <th class="text-center"><?php echo TEXT_LEGEND_STATUS_OFF; ?></th>
                <th class="text-center"><?php echo TEXT_LEGEND_STATUS_ON; ?></th>
                <th class="text-center"><?php echo TEXT_LEGEND_LINKED; ?></th>
                <th class="text-center"><?php echo TEXT_LEGEND_META_TAGS . '<br>' . TEXT_YES . '&nbsp;' . TEXT_NO; ?></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td></td>
                <td class="text-center"><?php echo zen_image(DIR_WS_IMAGES . 'icon_red_on.gif', IMAGE_ICON_STATUS_OFF); ?></td>
                <td class="text-center"><?php echo zen_image(DIR_WS_IMAGES . 'icon_green_on.gif', IMAGE_ICON_STATUS_ON); ?></td>
                <td class="text-center"><?php echo zen_image(DIR_WS_IMAGES . 'icon_yellow_on.gif', IMAGE_ICON_LINKED); ?></td>
                <td class="text-center">
                  <div class="fa fa-stack">
                    <i class="fa fa-circle fa-stack-2x" style="color: #000"></i>
                    <i class="fa fa-asterisk fa-stack-1x" aria-hidden="true" style="color: #ffa500"></i>
                  </div>
                  &nbsp;
                  <div class="fa fa-stack">
                    <i class="fa fa-circle fa-stack-2x" style="color: #000"></i>
                    <i class="fa fa-asterisk fa-stack-1x" aria-hidden="true" style="color: #fff"></i>
                  </div>
                </td>
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
              echo '<div class="col-sm-6 col-md-8">' . zen_draw_pull_down_menu('reset_editor', $editors_pulldown, $current_editor_key, 'onchange="this.form.submit();" class="form-control"') . '</div>';
              echo zen_hide_session_id();
              echo zen_draw_hidden_field('cID', $cPath);
              echo zen_draw_hidden_field('cPath', $cPath);
              echo (isset($_GET['pID']) ? zen_draw_hidden_field('pID', $_GET['pID']) : '');
              echo (isset($_GET['page']) ? zen_draw_hidden_field('page', $_GET['page']) : '');
              echo zen_draw_hidden_field('action', 'set_editor');
              echo '</form>';
              ?>
          </div>
          <div>
              <?php echo zen_draw_separator('pixel_trans.gif', '100%', '2'); ?>
          </div>
          <div>
              <?php
              // check for which buttons to show for categories and products
              $check_categories = zen_has_category_subcategories($current_category_id);
              $check_products = zen_products_in_category_count($current_category_id, false, false, 1);

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

              if ($zc_skip_products == true) {
                // toggle switch for display sort order
                $categories_products_sort_order_array = array(array('id' => '0', 'text' => TEXT_SORT_CATEGORIES_SORT_ORDER_PRODUCTS_NAME),
                  array('id' => '1', 'text' => TEXT_SORT_CATEGORIES_NAME)
                );
              } else {
                // toggle switch for display sort order
                $categories_products_sort_order_array = array(
                  array('id' => '0', 'text' => TEXT_SORT_PRODUCTS_SORT_ORDER_PRODUCTS_NAME),
                  array('id' => '1', 'text' => TEXT_SORT_PRODUCTS_NAME),
                  array('id' => '2', 'text' => TEXT_SORT_PRODUCTS_MODEL),
                  array('id' => '3', 'text' => TEXT_SORT_PRODUCTS_QUANTITY),
                  array('id' => '4', 'text' => TEXT_SORT_PRODUCTS_QUANTITY_DESC),
                  array('id' => '5', 'text' => TEXT_SORT_PRODUCTS_PRICE),
                  array('id' => '6', 'text' => TEXT_SORT_PRODUCTS_PRICE_DESC)
                );
              }
              echo zen_draw_form('set_categories_products_sort_order_form', FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, '', 'get', 'class="form-horizontal"');
              echo zen_draw_label(TEXT_CATEGORIES_PRODUCTS_SORT_ORDER_INFO, 'reset_categories_products_sort_order', 'class="col-sm-6 col-md-4 control-label"');
              echo '<div class="col-sm-6 col-md-8">' . zen_draw_pull_down_menu('reset_categories_products_sort_order', $categories_products_sort_order_array, $reset_categories_products_sort_order, 'onchange="this.form.submit();" class="form-control"') . '</div>';
              echo zen_hide_session_id();
              echo zen_draw_hidden_field('cID', $cPath);
              echo zen_draw_hidden_field('cPath', $cPath);
              echo (isset($_GET['pID']) ? zen_draw_hidden_field('pID', $_GET['pID']) : '');
              echo (isset($_GET['page']) ? zen_draw_hidden_field('page', $_GET['page']) : '');
              echo zen_draw_hidden_field('action', 'set_categories_products_sort_order');
              echo '</form>';
              ?>
          </div>
          <?php
          if (!isset($_GET['page'])) {
            $_GET['page'] = '';
          }
          if (isset($_GET['set_display_categories_dropdown'])) {
            $_SESSION['display_categories_dropdown'] = $_GET['set_display_categories_dropdown'];
          }
          if (!isset($_SESSION['display_categories_dropdown'])) {
            $_SESSION['display_categories_dropdown'] = 0;
          }
          ?>
          <div>
              <?php echo zen_draw_separator('pixel_trans.gif', '100%', '5'); ?>
          </div>
        </div>
        <div class="col-md-4">

          <div>
              <?php
              echo zen_draw_form('search', FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, '', 'get', 'class="form-horizontal"');
// show reset search
            ?>
            <div class="col-sm-6 col-md-4 control-label">
                <?php
              echo zen_draw_label(HEADING_TITLE_SEARCH_DETAIL, 'search');
              ?>
            </div>
            <div class="col-sm-6 col-md-8"><?php echo zen_draw_input_field('search', '', ($action == '' ? 'autofocus="autofocus"' : '') . 'class="form-control"'); ?></div>
            <div class="col"><?php echo zen_draw_separator('pixel_trans.gif', '100%', '1'); ?></div>
            <?php
            echo zen_hide_session_id();
            if (isset($_GET['search']) && zen_not_null($_GET['search'])) {
              $keywords = zen_db_input(zen_db_prepare_input($_GET['search']));
              ?>
              <div class="col-sm-6 col-md-4 control-label"><?php echo TEXT_INFO_SEARCH_DETAIL_FILTER; ?></div>
              <div class="col-sm-6 col-md-8">
                <?php echo zen_output_string_protected($_GET['search']); ?>
                <?php
                if (isset($_GET['search']) && zen_not_null($_GET['search'])) {
                  ?>
                <a href="<?php echo zen_href_link(FILENAME_CATEGORY_PRODUCT_LISTING); ?>" class="btn btn-default" role="button"><?php echo IMAGE_RESET; ?></a>&nbsp;&nbsp;
                <?php
              }
                ?>
              </div>
              <?php
            }
            echo '</form>';
            ?>
          </div>
          <div class="row">
              <?php echo zen_draw_separator('pixel_trans.gif', '100%', '5'); ?>
          </div>
          <div>
              <?php
              if ($_SESSION['display_categories_dropdown'] == 0) {
                echo '<div class="col-sm-6 col-md-4 text-right">';
                echo '<a href="' . zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'set_display_categories_dropdown=1' . (isset($_GET['cID']) ? '&cID=' . (int)$_GET['cID'] : '') . '&cPath=' . $cPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '">' . zen_image(DIR_WS_ICONS . 'cross.gif', IMAGE_ICON_STATUS_OFF) . '</a>';
                echo zen_draw_label(HEADING_TITLE_GOTO, 'cPath', 'class="control-label"');
                echo '</div>';
                echo '<div class="col-sm-6 col-md-8">';
                echo zen_draw_form('goto', FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, '', 'get', 'class="form-horizontal"');
                echo zen_hide_session_id();
                echo zen_draw_pull_down_menu('cPath', zen_get_category_tree(), $current_category_id, 'onchange="this.form.submit();" class="form-control"');
                echo '</form>';
                echo '</div>';
              } else {
                echo '<div class="col-sm-6 col-md-4 text-right">';
               echo '<a href="' . zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'set_display_categories_dropdown=0' . (isset($_GET['cID']) ? '&cID=' . (int)$_GET['cID'] : '') . '&cPath=' . $cPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '">' . zen_image(DIR_WS_ICONS . 'tick.gif', IMAGE_ICON_STATUS_ON) . '</a>&nbsp;&nbsp;';
                echo '<strong>' . HEADING_TITLE_GOTO . '</strong>';
                echo '</div>';
              }
              ?>
          </div>
        </div>

      <?php } ?>
      <div class="row"><?php echo zen_draw_separator('pixel_black.gif', '100%', '1px'); ?></div>
      <div class="row">
          <?php
          if ($action != '') {
            ?>
          <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 configurationColumnLeft">
              <?php
            } else {
              ?>
            <div>
                <?php
              }
              ?>
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th class="text-right"><?php echo TABLE_HEADING_ID; ?></th>
                  <th><?php echo TABLE_HEADING_CATEGORIES_PRODUCTS; ?></th>
                  <th><?php echo TABLE_HEADING_MODEL; ?></th>
                  <th class="text-right"><?php echo TABLE_HEADING_PRICE; ?></th>
                  <th class="text-right">&nbsp;</th>
                  <th class="text-right"><?php echo TABLE_HEADING_QUANTITY; ?></th>
                  <th class="text-right"><?php echo TABLE_HEADING_STATUS; ?></th>
                  <?php
                  if ($action == '') {
                    ?>
                    <th class="text-right"><?php echo TABLE_HEADING_CATEGORIES_SORT_ORDER; ?></th>
                    <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
                    <?php
                  }
                  ?>
                </tr>
              </thead>
              <?php
              switch ($_SESSION['categories_products_sort_order']) {
                case (0):
                  $order_by = " ORDER BY c.sort_order, cd.categories_name";
                  break;
                case (1):
                  $order_by = " ORDER BY cd.categories_name";
                case (2);
                case (3);
                case (4);
                case (5);
                case (6);
              }

              $categories_count = 0;
              $rows = 0;
              if (isset($_GET['search'])) {
                $search = zen_db_prepare_input($_GET['search']);

                $categories = $db->Execute("SELECT c.categories_id, cd.categories_name, cd.categories_description, c.categories_image,
                                                   c.parent_id, c.sort_order, c.date_added, c.last_modified, c.categories_status
                                            FROM " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd
                                            WHERE c.categories_id = cd.categories_id
                                            AND cd.language_id = " . (int)$_SESSION['languages_id'] . "
                                            AND cd.categories_name like '%" . zen_db_input($search) . "%'" .
                                            $order_by);
              } else {
                $categories = $db->Execute("SELECT c.categories_id, cd.categories_name, cd.categories_description, c.categories_image,
                                                   c.parent_id, c.sort_order, c.date_added, c.last_modified, c.categories_status
                                            FROM " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd
                                            WHERE c.parent_id = " . (int)$current_category_id . "
                                            AND c.categories_id = cd.categories_id
                                            AND cd.language_id = " . (int)$_SESSION['languages_id'] .
                                            $order_by);
              }

              foreach ($categories as $category) {
                $categories_count++;
                $rows++;

// Get parent_id for subcategories if search
                if (isset($_GET['search'])) {
                  $cPath = $category['parent_id'];
                }

                if ((!isset($_GET['cID']) && !isset($_GET['pID']) || (isset($_GET['cID']) && ($_GET['cID'] == $category['categories_id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
                  //$category_childs = array('childs_count' => zen_childs_in_category_count($category['categories_id']));
                  //$category_products = array('products_count' => zen_products_in_category_count($category['categories_id']));
                  //$cInfo_array = array_merge($category, $category_childs, $category_products);
                  $cInfo = new objectInfo($category);
                }
                ?>
                <tr onclick="document.location.href = '<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, zen_get_path($category['categories_id'])); ?>'" role="button">
                  <td class="text-right"><?php echo $category['categories_id']; ?></td>
                  <td><?php echo zen_image(DIR_WS_ICONS . 'folder.gif', ICON_FOLDER); ?>&nbsp;<strong><?php echo $category['categories_name']; ?></strong></td>
                  <td class="text-center">&nbsp;</td>
                  <td class="text-right"><?php echo zen_get_products_sale_discount('', $category['categories_id'], true); ?></td>
                  <td class="text-center">&nbsp;</td>
                  <td class="text-right">
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
                  <td class="text-right">
                      <?php
                      if (SHOW_CATEGORY_PRODUCTS_LINKED_STATUS == 'true' && zen_get_products_to_categories($category['categories_id'], true, 'products_active') == 'true') {
                        echo zen_image(DIR_WS_IMAGES . 'icon_yellow_on.gif', IMAGE_ICON_LINKED, '', '', 'style="margin:2px;"');
                      }
                      ?>
                      <?php if ($category['categories_status'] == '1') { ?>
                      <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'action=setflag_categories&flag=0&cID=' . $category['categories_id'] . '&cPath=' . $cPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . ((isset($_GET['search']) && !empty($_GET['search'])) ? '&search=' . $_GET['search'] : '')); ?>"><?php echo zen_image(DIR_WS_IMAGES . 'icon_green_on.gif', IMAGE_ICON_STATUS_ON, '', '', 'style="margin:2px;"'); ?></a>
                    <?php } else { ?>
                      <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'action=setflag_categories&flag=1&cID=' . $category['categories_id'] . '&cPath=' . $cPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . ((isset($_GET['search']) && !empty($_GET['search'])) ? '&search=' . $_GET['search'] : '')); ?>"><?php echo zen_image(DIR_WS_IMAGES . 'icon_red_on.gif', IMAGE_ICON_STATUS_OFF, '', '', 'style="margin:2px;"'); ?></a>
                      <?php
                    }
                    ?>
                  </td>
                  <?php
                  if ($action == '') {
                    ?>
                    <td class="text-right"><?php echo $category['sort_order']; ?></td>
                    <td class="text-right">
                      <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $category['categories_id'] . ((isset($_GET['search']) && !empty($_GET['search'])) ? '&search=' . $_GET['search'] : '')); ?>" style="text-decoration: none">
                        <div class="fa-stack fa-lg edit">
                          <i class="fa fa-circle fa-stack-2x base"></i>
                          <i class="fa fa-pencil fa-stack-1x overlay" aria-hidden="true"></i>
                        </div>
                      </a>
                      <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&cID=' . $category['categories_id'] . '&action=delete_category'); ?>" style="text-decoration: none">
                        <div class="fa-stack fa-lg delete">
                          <i class="fa fa-circle fa-stack-2x base"></i>
                          <i class="fa fa-trash-o fa-stack-1x overlay" aria-hidden="true"></i>
                        </div>
                      </a>
                      <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&cID=' . $category['categories_id'] . '&action=move_category'); ?>" style="text-decoration: none">
                        <div class="fa-stack fa-lg move">
                          <i class="fa fa-circle fa-stack-2x base"></i>
                          <i class="fa fa-stack-1x overlay" aria-hidden="true"><strong>M</strong></i>
                        </div>
                      </a>
                      <?php
// bof: categories meta tags
                      if (zen_get_category_metatags_keywords($category['categories_id'], (int)$_SESSION['languages_id']) or zen_get_category_metatags_description($category['categories_id'], (int)$_SESSION['languages_id'])) {
                        ?>
                        <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $category['categories_id'] . '&action=edit_category_meta_tags' . '&activeTab=categoryTabs4'); ?>" style="text-decoration: none">
                          <div class="fa-stack fa-lg metatags-on">
                            <i class="fa fa-circle fa-stack-2x base"></i>
                            <i class="fa fa-asterisk fa-stack-1x overlay" aria-hidden="true"></i>
                          </div>
                        </a>
                      <?php } else { ?>
                        <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $category['categories_id'] . '&action=edit_category_meta_tags' . '&activeTab=categoryTabs4'); ?>" style="text-decoration: none">
                          <div class="fa-stack fa-lg metatags-off">
                            <i class="fa fa-circle fa-stack-2x base"></i>
                            <i class="fa fa-asterisk fa-stack-1x overlay" aria-hidden="true"></i>
                          </div>
                        </a>
                        <?php
                      } // eof: categories meta tags
                      ?>
                    </td>
                    <?php
                  }
                  ?>
                </tr>
                <?php
              }


              switch ($_SESSION['categories_products_sort_order']) {
                case (0):
                  $order_by = " ORDER BY p.products_sort_order, pd.products_name";
                  break;
                case (1):
                  $order_by = " ORDER BY pd.products_name";
                  break;
                case (2);
                  $order_by = " ORDER BY p.products_model";
                  break;
                case (3);
                  $order_by = " ORDER BY p.products_quantity, pd.products_name";
                  break;
                case (4);
                  $order_by = " ORDER BY p.products_quantity DESC, pd.products_name";
                  break;
                case (5);
                  $order_by = " ORDER BY p.products_price_sorter, pd.products_name";
                  break;
                case (6);
                  $order_by = " ORDER BY p.products_price_sorter DESC, pd.products_name";
                  break;
              }

              $products_count = 0;
              if (isset($_GET['search']) && !empty($_GET['search']) && $action != 'edit_category') {
// fix duplicates and force search to use master_categories_id
                /*
                  $products_query_raw = ("select p.products_type, p.products_id, pd.products_name, p.products_quantity,
                  p.products_image, p.products_price, p.products_date_added,
                  p.products_last_modified, p.products_date_available,
                  p.products_status, p2c.categories_id,
                  p.products_model,
                  p.products_quantity_order_min, p.products_quantity_order_units, p.products_priced_by_attribute,
                  p.product_is_free, p.product_is_call, p.products_quantity_mixed, p.product_is_always_free_shipping,
                  p.products_quantity_order_max, p.products_sort_order
                  from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, "
                  . TABLE_PRODUCTS_TO_CATEGORIES . " p2c
                  where p.products_id = pd.products_id
                  and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                  and p.products_id = p2c.products_id
                  and (
                  pd.products_name like '%" . zen_db_input($_GET['search']) . "%'
                  or pd.products_description like '%" . zen_db_input($_GET['search']) . "%'
                  or p.products_model like '%" . zen_db_input($_GET['search']) . "%')" .
                  $order_by);
                 */
                $products_query_raw = ("SELECT p.products_type, p.products_id, pd.products_name, p.products_quantity, p.products_image, p.products_price,
                                               p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p2c.categories_id,
                                               p.products_model, p.products_quantity_order_min, p.products_quantity_order_units, p.products_priced_by_attribute,
                                               p.product_is_free, p.product_is_call, p.products_quantity_mixed, p.product_is_always_free_shipping,
                                               p.products_quantity_order_max, p.products_sort_order, p.master_categories_id
                                        FROM " . TABLE_PRODUCTS . " p,
                                             " . TABLE_PRODUCTS_DESCRIPTION . " pd,
                                             " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c
                                        WHERE p.products_id = pd.products_id
                                        AND pd.language_id = " . (int)$_SESSION['languages_id'] . "
                                        AND (p.products_id = p2c.products_id
                                          AND p.master_categories_id = p2c.categories_id)
                                        AND (pd.products_name LIKE '%" . zen_db_input($_GET['search']) . "%'
                                          OR pd.products_description LIKE '%" . zen_db_input($_GET['search']) . "%'
                                          OR p.products_id = '" . zen_db_input($_GET['search']) . "'
                                          OR p.products_model like '%" . zen_db_input($_GET['search']) . "%')
                                        " . $order_by);
              } else {
                $products_query_raw = ("SELECT p.products_type, p.products_id, pd.products_name, p.products_quantity, p.products_image, p.products_price,
                                               p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p.products_model,
                                               p.products_quantity_order_min, p.products_quantity_order_units, p.products_priced_by_attribute, p.product_is_free,
                                               p.product_is_call, p.products_quantity_mixed, p.product_is_always_free_shipping, p.products_quantity_order_max,
                                               p.products_sort_order
                                        FROM " . TABLE_PRODUCTS . " p,
                                             " . TABLE_PRODUCTS_DESCRIPTION . " pd,
                                             " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c
                                        WHERE p.products_id = pd.products_id
                                        AND pd.language_id = " . (int)$_SESSION['languages_id'] . "
                                        AND p.products_id = p2c.products_id
                                        AND p2c.categories_id = " . (int)$current_category_id .
                                        $order_by);
              }
// Split Page
// reset page when page is unknown
              if (($_GET['page'] == '1' || $_GET['page'] == '') && isset($_GET['pID']) && $_GET['pID'] != '') {
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

                if ((!isset($_GET['pID']) && !isset($_GET['cID']) || (isset($_GET['pID']) && ($_GET['pID'] == $product['products_id']))) && !isset($pInfo) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
                  $pInfo = new objectInfo($product);
                }

                $type_handler = $zc_products->get_handler($product['products_type']);
                ?>
                <tr>
                  <td class="text-right"><?php echo $product['products_id']; ?></td>
                  <td><a href="<?php echo zen_catalog_href_link($type_handler . '_info', 'cPath=' . $cPath . '&products_id=' . $product['products_id'] . '&language=' . $_SESSION['languages_code'] . '&product_type=' . $product['products_type']); ?>" target="_blank"><?php echo zen_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW); ?></a>&nbsp;<?php echo $product['products_name']; ?></td>
                  <td><?php echo $product['products_model']; ?></td>
                  <td colspan="2" class="text-right"><?php echo zen_get_products_display_price($product['products_id']); ?></td>
                  <td class="text-right"><?php echo $product['products_quantity']; ?></td>
                  <td class="text-right">
                      <?php
                      if (zen_get_product_is_linked($product['products_id']) == 'true') {
                        echo zen_image(DIR_WS_IMAGES . 'icon_yellow_on.gif', IMAGE_ICON_LINKED, '', '', 'style="vertical-align:top;"') . '&nbsp;&nbsp;';
                      }
                      if ($product['products_status'] == '1') {
                        echo zen_draw_form('setflag_products', FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'action=setflag&pID=' . $product['products_id'] . '&cPath=' . $cPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . ((isset($_GET['search']) && !empty($_GET['search'])) ? '&search=' . $_GET['search'] : ''));
                        ?>
                      <input type="image" src="<?php echo DIR_WS_IMAGES ?>icon_green_on.gif" title="<?php echo IMAGE_ICON_STATUS_ON; ?>" />
                      <?php echo zen_draw_hidden_field('flag', '0'); ?>
                      <?php echo '</form>'; ?>
                      <?php
                    } else {
                      echo zen_draw_form('setflag_products', FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'action=setflag&pID=' . $product['products_id'] . '&cPath=' . $cPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . ((isset($_GET['search']) && !empty($_GET['search'])) ? '&search=' . $_GET['search'] : ''));
                      ?>
                      <input type="image" src="<?php echo DIR_WS_IMAGES ?>icon_red_on.gif" title="<?php echo IMAGE_ICON_STATUS_OFF; ?>"/>
                      <?php echo zen_draw_hidden_field('flag', '1'); ?>
                      <?php echo '</form>'; ?>
                      <?php
                    }
                    ?>
                  </td>
                  <?php
                  if ($action == '') {
                    ?>
                    <td class="text-right"><?php echo $product['products_sort_order']; ?></td>
                    <td class="text-right">
                      <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_PRODUCT, 'cPath=' . $cPath . '&product_type=' . $product['products_type'] . '&pID=' . $product['products_id'] . '&action=new_product' . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '')); ?>" style="text-decoration: none">
                        <div class="fa-stack fa-lg edit">
                          <i class="fa fa-circle fa-stack-2x base"></i>
                          <i class="fa fa-pencil fa-stack-1x overlay" aria-hidden="true"></i>
                        </div>
                      </a>
                      <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&product_type=' . $product['products_type'] . '&pID=' . $product['products_id'] . '&action=delete_product'); ?>" style="text-decoration: none">
                        <div class="fa-stack fa-lg delete">
                          <i class="fa fa-circle fa-stack-2x base"></i>
                          <i class="fa fa-trash-o fa-stack-1x overlay" aria-hidden="true"></i>
                        </div>
                      </a>
                      <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&product_type=' . $product['products_type'] . '&pID=' . $product['products_id'] . '&action=move_product'); ?>" style="text-decoration: none">
                        <div class="fa-stack fa-lg move">
                          <i class="fa fa-circle fa-stack-2x base"></i>
                          <i class="fa fa-stack-1x overlay" aria-hidden="true"><strong>M</strong></i>
                        </div>
                      </a>
                      <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&product_type=' . $product['products_type'] . '&pID=' . $product['products_id'] . '&action=copy_product'); ?>" style="text-decoration: none">
                        <div class="fa-stack fa-lg copy">
                          <i class="fa fa-circle fa-stack-2x base"></i>
                          <i class="fa fa-stack-1x overlay" aria-hidden="true"><strong>C</strong></i>
                        </div>
                      </a>

                      <?php if (defined('FILENAME_IMAGE_HANDLER') && file_exists(DIR_FS_ADMIN . FILENAME_IMAGE_HANDLER . '.php')) { ?>
                        <a href="<?php echo zen_href_link(FILENAME_IMAGE_HANDLER, 'products_filter=' . $product['products_id'] . '&current_category_id=' . $current_category_id); ?>" style="text-decoration: none">
                          <div class="fa-stack fa-lg imagehandler">
                            <i class="fa fa-circle fa-stack-2x base"></i>
                            <i class="fa fa-stack-1x fa-image overlay" aria-hidden="true"></i>
                          </div>
                        </a>
                      <?php } ?>

                      <?php
// BOF: Attribute commands
//if (!empty($product['products_id']) && zen_has_product_attributes($product['products_id'], 'false')) {
                      ?>
                      <?php
                      if (zen_has_product_attributes($product['products_id'], 'false')) {
                        ?>
                        <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $product['products_id'] . '&action=attribute_features' . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')); ?>" style="text-decoration: none">
                          <div class="fa-stack fa-lg attributes-on">
                            <i class="fa fa-circle fa-stack-2x base"></i>
                            <i class="fa fa-stack-1x overlay" aria-hidden="true"><strong>A</strong></i>
                          </div>
                        </a>
                        <?php
                      } else {
                        ?>
                        <a href="<?php echo zen_href_link(FILENAME_ATTRIBUTES_CONTROLLER, 'products_filter=' . $product['products_id'] . '&current_category_id=' . $current_category_id); ?>" style="text-decoration: none">
                          <div class="fa-stack fa-lg attributes-off">
                            <i class="fa fa-circle fa-stack-2x base"></i>
                            <i class="fa fa-stack-1x overlay" aria-hidden="true"><strong>A</strong></i>
                          </div>
                        </a>
                        <?php
                      }
                      ?>
                      <?php
//} // EOF: Attribute commands
                      ?>
                      <?php
                      if ($zc_products->get_allow_add_to_cart($product['products_id']) == "Y") {
                        ?>
                        <a href="<?php echo zen_href_link(FILENAME_PRODUCTS_PRICE_MANAGER, 'products_filter=' . $product['products_id'] . '&current_category_id=' . $current_category_id); ?>" style="text-decoration: none">
                          <div class="fa-stack fa-lg pricemanager-on">
                            <i class="fa fa-circle fa-stack-2x base"></i>
                            <i class="fa fa-stack-1x fa-dollar overlay" aria-hidden="true"></i>
                          </div>
                        </a>
                        <?php
                      } else {
                        ?>
                        <div class="fa-stack fa-lg pricemanager-off">
                          <i class="fa fa-circle fa-stack-2x base"></i>
                          <i class="fa fa-stack-1x fa-dollar overlay" aria-hidden="true"></i>
                        </div>
                      <?php
                      }
// meta tags
                      if (zen_get_metatags_keywords($product['products_id'], (int)$_SESSION['languages_id']) or zen_get_metatags_description($product['products_id'], (int)$_SESSION['languages_id'])) {
                        ?>
                        <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_PRODUCT, 'page=' . $_GET['page'] . '&product_type=' . $product['products_type'] . '&cPath=' . $cPath . '&pID=' . $product['products_id']); ?>" style="text-decoration: none">
                          <div class="fa-stack fa-lg metatags-on">
                            <i class="fa fa-circle fa-stack-2x base"></i>
                            <i class="fa fa-asterisk fa-stack-1x overlay" aria-hidden="true"></i>
                          </div>
                        </a>
                        <?php
                      } else {
                        ?>
                        <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_PRODUCT, 'page=' . $_GET['page'] . '&product_type=' . $product['products_type'] . '&cPath=' . $cPath . '&pID=' . $product['products_id']); ?>" style="text-decoration: none">
                          <div class="fa-stack fa-lg metatags-off">
                            <i class="fa fa-circle fa-stack-2x base"></i>
                            <i class="fa fa-asterisk fa-stack-1x overlay" aria-hidden="true"></i>
                          </div>
                        </a>
                        <?php
                      }
                      ?>
                    </td>
                    <?php
                  }
                  ?>
                </tr>
                <?php
              }
              ?>
            </table>
          </div>
          <?php
          $heading = [];
          $contents = [];
          switch ($action) {
            case 'setflag_categories':
              $heading[] = array('text' => '<h4>' . TEXT_INFO_HEADING_STATUS_CATEGORY . '</h4>');
              $contents = array('form' => zen_draw_form('categories', FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'action=update_category_status&cPath=' . $_GET['cPath'] . '&cID=' . $_GET['cID'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . ((isset($_GET['search']) && !empty($_GET['search'])) ? '&search=' . $_GET['search'] : ''), 'post', 'enctype="multipart/form-data"') . zen_draw_hidden_field('categories_id', $cInfo->categories_id) . zen_draw_hidden_field('categories_status', $cInfo->categories_status));
              $contents[] = array('text' => '<strong>' . zen_get_category_name($cInfo->categories_id, $_SESSION['languages_id']) . '</strong>');
              $contents[] = array('text' => TEXT_CATEGORIES_STATUS_WARNING . '<br /><br />');
              $contents[] = array('text' => TEXT_CATEGORIES_STATUS_INTRO . ' ' . ($cInfo->categories_status == '1' ? TEXT_CATEGORIES_STATUS_OFF : TEXT_CATEGORIES_STATUS_ON));
              if ($cInfo->categories_status == '1') {
                $contents[] = array('text' => TEXT_PRODUCTS_STATUS_INFO . ' ' . TEXT_PRODUCTS_STATUS_OFF . zen_draw_hidden_field('set_products_status_off', true));
              } else {
                $contents[] = array('text' => zen_draw_label(TEXT_PRODUCTS_STATUS_INFO, 'set_products_status', 'class="control-label"') . '<div class="radio"><label>' .
                  zen_draw_radio_field('set_products_status', 'set_products_status_on', true) . TEXT_PRODUCTS_STATUS_ON . '</label></div><div class="radio"><label>' .
                  zen_draw_radio_field('set_products_status', 'set_products_status_off') . TEXT_PRODUCTS_STATUS_OFF . '</label></div><div class="radio"><label>' .
                  zen_draw_radio_field('set_products_status', 'set_products_status_nochange') . TEXT_PRODUCTS_STATUS_NOCHANGE . '</label></div>');
              }


              //        $contents[] = array('text' => '<br />' . TEXT_PRODUCTS_STATUS_INFO . '<br />' . zen_draw_radio_field('set_products_status', 'set_products_status_off', true) . ' ' . TEXT_PRODUCTS_STATUS_OFF . '<br />' . zen_draw_radio_field('set_products_status', 'set_products_status_on') . ' ' . TEXT_PRODUCTS_STATUS_ON);

              $contents[] = array('align' => 'center', 'text' => '<button type="submit" class="btn btn-primary">' . IMAGE_UPDATE . '</button> <a href="' . zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . ((isset($_GET['search']) && !empty($_GET['search'])) ? '&search=' . $_GET['search'] : '')) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>');
              break;
            case 'delete_category':
              $heading[] = array('text' => '<h4>' . TEXT_INFO_HEADING_DELETE_CATEGORY . '</h4>');

              $contents = array('form' => zen_draw_form('categories', FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'action=delete_category_confirm&cPath=' . $cPath) . zen_draw_hidden_field('categories_id', $cInfo->categories_id));
              $contents[] = array('text' => TEXT_DELETE_CATEGORY_INTRO);
              $contents[] = array('text' => TEXT_DELETE_CATEGORY_INTRO_LINKED_PRODUCTS);
              $contents[] = array('text' => '<strong>' . $cInfo->categories_name . '</strong>');
              if ($cInfo->childs_count > 0) {
                $contents[] = array('text' => sprintf(TEXT_DELETE_WARNING_CHILDS, $cInfo->childs_count));
              }
              if ($cInfo->products_count > 0) {
                $contents[] = array('text' => sprintf(TEXT_DELETE_WARNING_PRODUCTS, $cInfo->products_count));
              }
              /*
                // future cat specific
                if ($cInfo->products_count > 0) {
                $contents[] = array('text' => TEXT_PRODUCTS_LINKED_INFO . '<br>' .
                zen_draw_radio_field('delete_linked', '1') . ' ' . TEXT_PRODUCTS_DELETE_LINKED_YES . '<br>' .
                zen_draw_radio_field('delete_linked', '0', true) . ' ' . TEXT_PRODUCTS_DELETE_LINKED_NO);
                }
               */
              $contents[] = array('align' => 'center', 'text' => '<button type="submit" class="btn btn-danger">' . IMAGE_DELETE . '</button> <a href="' . zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>');
              break;
            case 'move_category':
              $heading[] = array('text' => '<h4>' . TEXT_INFO_HEADING_MOVE_CATEGORY . '</h4>');

              $contents = array('form' => zen_draw_form('move_category', FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'action=move_category_confirm&cPath=' . $cPath, 'post', 'class="form-horizontal"') . zen_draw_hidden_field('categories_id', $cInfo->categories_id));
              $contents[] = array('text' => sprintf(TEXT_MOVE_CATEGORIES_INTRO, $cInfo->categories_name));
              $contents[] = array('text' => zen_draw_label(sprintf(TEXT_MOVE, $cInfo->categories_name), 'move_to_category_id') . zen_draw_pull_down_menu('move_to_category_id', zen_get_category_tree(), $current_category_id, 'class="form-control"'));
              $contents[] = array('align' => 'center', 'text' => '<button type="submit" class="btn btn-primary">' . IMAGE_MOVE . '</button> <a href="' . zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>');
              break;
            case 'delete_product':
              if (file_exists(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/z4a_delete_product.php')) {
                require(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/z4a_delete_product.php');
              } else {
                require(DIR_WS_MODULES . 'z4a_delete_product.php');
              }
              break;
            case 'move_product':
              if (file_exists(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/z4a_move_product.php')) {
                require(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/z4a_move_product.php');
              } else {
                require(DIR_WS_MODULES . 'z4a_move_product.php');
              }
              break;
            case 'copy_product':
              if (file_exists(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/z4a_copy_product.php')) {
                require(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/z4a_copy_product.php');
              } else {
                require(DIR_WS_MODULES . 'z4a_copy_product.php');
              }
              break;
            // attribute features
            case 'attribute_features':
              $copy_attributes_delete_first = '0';
              $copy_attributes_duplicates_skipped = '0';
              $copy_attributes_duplicates_overwrite = '0';
              $copy_attributes_include_downloads = '1';
              $copy_attributes_include_filename = '1';
              $heading[] = array('text' => '<h4>' . TEXT_INFO_HEADING_ATTRIBUTE_FEATURES . $pInfo->products_id . '</h4>');

              $contents[] = array('align' => 'center', 'text' => '<strong>' . TEXT_PRODUCTS_ATTRIBUTES_INFO . '</strong>');

              $contents[] = array('align' => 'center', 'text' => '<strong>' . zen_get_products_name($pInfo->products_id, $languages_id) . ' ID# ' . $pInfo->products_id . '</strong>');
              $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_ATTRIBUTES_CONTROLLER, '&action=attributes_preview' . '&products_filter=' . $pInfo->products_id . '&current_category_id=' . $current_category_id) . '" class="btn btn-info" role="button">' . IMAGE_PREVIEW . '</a> <a href="' . zen_href_link(FILENAME_ATTRIBUTES_CONTROLLER, 'products_filter=' . $pInfo->products_id . '&current_category_id=' . $current_category_id) . '" class="btn btn-primary" role="button">' . IMAGE_EDIT_ATTRIBUTES . '</a>');
              $contents[] = array('align' => 'left', 'text' => '<strong>' . TEXT_PRODUCT_ATTRIBUTES_DOWNLOADS . '</strong>' . zen_has_product_attributes_downloads($pInfo->products_id) . zen_has_product_attributes_downloads($pInfo->products_id, true));
              $contents[] = array('align' => 'left', 'text' => TEXT_INFO_ATTRIBUTES_FEATURES_DELETE . '<strong>' . zen_get_products_name($pInfo->products_id) . ' ID# ' . $pInfo->products_id . '</strong> <a href="' . zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=delete_attributes' . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . '&products_id=' . $pInfo->products_id) . '" class="btn btn-danger" role="button">' . IMAGE_DELETE . '</a>');
              $contents[] = array('align' => 'left', 'text' => TEXT_INFO_ATTRIBUTES_FEATURES_UPDATES . '<strong>' . zen_get_products_name($pInfo->products_id, $languages_id) . ' ID# ' . $pInfo->products_id . '</strong> <a href="' . zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=update_attributes_sort_order' . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . '&products_id=' . $pInfo->products_id) . '" class="btn btn-primary" role="button">' . IMAGE_UPDATE . '</a>');
              $contents[] = array('align' => 'left', 'text' => TEXT_INFO_ATTRIBUTES_FEATURES_COPY_TO_PRODUCT . '<strong>' . zen_get_products_name($pInfo->products_id, $languages_id) . ' ID# ' . $pInfo->products_id . '</strong> <a href="' . zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=attribute_features_copy_to_product' . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . '&products_id=' . $pInfo->products_id) . '" class="btn btn-primary" role="button">' . IMAGE_COPY_TO . '</a>');
              $contents[] = array('align' => 'left', 'text' => '<br>' . TEXT_INFO_ATTRIBUTES_FEATURES_COPY_TO_CATEGORY . '<strong>' . zen_get_products_name($pInfo->products_id, $languages_id) . ' ID# ' . $pInfo->products_id . '</strong> <a href="' . zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=attribute_features_copy_to_category' . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . '&products_id=' . $pInfo->products_id) . '" class="btn btn-primary" role="button">' . IMAGE_COPY_TO . '</a>');
              $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>');
              break;

            // attribute copier to product
            case 'attribute_features_copy_to_product':
              $_GET['products_update_id'] = '';
              // excluded current product from the pull down menu of products
              $products_exclude_array = array();
              $products_exclude_array[] = $pInfo->products_id;

              $heading[] = array('text' => '<h4>' . TEXT_INFO_HEADING_ATTRIBUTE_FEATURES . $pInfo->products_id . '</h4>');
              $contents = array('form' => zen_draw_form('products', FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'action=update_attributes_copy_to_product&cPath=' . $cPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''), 'post', 'class="form-horizontal"') . zen_draw_hidden_field('products_id', $pInfo->products_id) . zen_draw_hidden_field('products_update_id', $_GET['products_update_id']) . zen_draw_hidden_field('copy_attributes', $_GET['copy_attributes']));
              $contents[] = array('text' => zen_draw_label(TEXT_COPY_ATTRIBUTES_CONDITIONS, 'copy_attributes', 'class="control-label"') . '<div class="radio"><label>' . zen_draw_radio_field('copy_attributes', 'copy_attributes_delete', true) . TEXT_COPY_ATTRIBUTES_DELETE . '</label></div><div class="radio"><label>' . zen_draw_radio_field('copy_attributes', 'copy_attributes_update') . TEXT_COPY_ATTRIBUTES_UPDATE . '</label></div><div class="radio"><label>' . zen_draw_radio_field('copy_attributes', 'copy_attributes_ignore') . TEXT_COPY_ATTRIBUTES_IGNORE . '</label></div>');
              $contents[] = array('text' => zen_draw_products_pull_down('products_update_id', 'class="form-control"', $products_exclude_array, true));
              $contents[] = array('align' => 'center', 'text' => '<button type="submit" class="btn btn-primary">' . IMAGE_COPY_TO . '</button> <a href="' . zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>');
              break;

            // attribute copier to product
            case 'attribute_features_copy_to_category':
              $_GET['categories_update_id'] = '';

              $heading[] = array('text' => '<h4>' . TEXT_INFO_HEADING_ATTRIBUTE_FEATURES . $pInfo->products_id . '</h4>');
              $contents = array('form' => zen_draw_form('products', FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'action=update_attributes_copy_to_category&cPath=' . $cPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''), 'post', 'class="form-horizontal"') . zen_draw_hidden_field('products_id', $pInfo->products_id) . zen_draw_hidden_field('categories_update_id', $_GET['categories_update_id']) . zen_draw_hidden_field('copy_attributes', $_GET['copy_attributes']));
              $contents[] = array('text' => zen_draw_label(TEXT_COPY_ATTRIBUTES_CONDITIONS, 'copy_attributes', 'class="control-label"') . '<div class="radio"><label>' . zen_draw_radio_field('copy_attributes', 'copy_attributes_delete', true) . TEXT_COPY_ATTRIBUTES_DELETE . '</label></div><div class="radio"><label>' . zen_draw_radio_field('copy_attributes', 'copy_attributes_update') . TEXT_COPY_ATTRIBUTES_UPDATE . '</label></div><div class="radio"><label>' . zen_draw_radio_field('copy_attributes', 'copy_attributes_ignore') . TEXT_COPY_ATTRIBUTES_IGNORE . '</label></div>');
              $contents[] = array('text' => zen_draw_products_pull_down_categories('categories_update_id', 'class="form-control"', '', true));
              $contents[] = array('align' => 'center', 'text' => '<button type="submit" class="btn btn-primary">' . IMAGE_COPY_TO . '</button> <a href="' . zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>');
              break;
          }
          if ((zen_not_null($heading)) && (zen_not_null($contents))) {
            $box = new box;
            echo '<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 configurationColumnRight">';
            echo $box->infoBox($heading, $contents);
            echo '</div>';
          }
          ?>
        </div>
        <?php
        if ($action == '') {
          $cPath_back = '';
          if (sizeof($cPath_array) > 0) {
            for ($i = 0, $n = sizeof($cPath_array) - 1; $i < $n; $i++) {
              if (empty($cPath_back)) {
                $cPath_back .= $cPath_array[$i];
              } else {
                $cPath_back .= '_' . $cPath_array[$i];
              }
            }
          }

          $cPath_back = (zen_not_null($cPath_back)) ? 'cPath=' . $cPath_back . '&' : '';
          ?>
          <div class="row">
            <div class="col-md-6"><?php echo TEXT_CATEGORIES . '&nbsp;' . $categories_count . '<br>' . TEXT_PRODUCTS . '&nbsp;' . $products_count; ?></div>
            <div class="col-md-6 text-right">
                <?php
                if (sizeof($cPath_array) > 0) {
                  ?>
                <div class="col-sm-3">
                  <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, $cPath_back . 'cID=' . $current_category_id); ?>" class="btn btn-default" role="button"><?php echo IMAGE_BACK; ?></a>
                </div>
                <?php
              }
              if (!isset($_GET['search']) && !$zc_skip_categories) {
                ?>
                <div class="col-sm-3">
                  <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES, 'cPath=' . $cPath . '&action=new_category'); ?>" class="btn btn-primary" role="button"><?php echo IMAGE_NEW_CATEGORY; ?></a>
                </div>
                <?php
              }
              ?>

              <?php if ($zc_skip_products == false) { ?>
                <?php echo zen_draw_form('newproduct', FILENAME_ZEN4ALL_PRODUCT, '', 'post', 'class="form-horizontal"'); ?>
                <?php echo (empty($_GET['search']) ? '<div class="col-sm-3"><button type="submit" class="btn btn-primary">' . IMAGE_NEW_PRODUCT . '</button></div>' : ''); ?>
                <?php
                $sql = "SELECT ptc.product_type_id, pt.type_name
                        FROM " . TABLE_PRODUCT_TYPES_TO_CATEGORY . " ptc,
                             " . TABLE_PRODUCT_TYPES . " pt
                        WHERE ptc.category_id = " . (int)$current_category_id . "
                        AND pt.type_id = ptc.product_type_id";
                $restrict_types = $db->Execute($sql);
                if ($restrict_types->RecordCount() > 0) {
                  $product_restrict_types_array = array();
                  foreach ($restrict_types as $restrict_type) {
                    $product_restrict_types_array[] = array(
                      'id' => $restrict_type['product_type_id'],
                      'text' => $restrict_type['type_name']);
                  }
                } else {

// make array for product types

                  $sql = "SELECT * FROM " . TABLE_PRODUCT_TYPES;
                  $product_types = $db->Execute($sql);
                  $product_types_array = [];
                  foreach ($product_types as $product_type) {
                    $product_types_array[] = array(
                      'id' => $product_type['type_id'],
                      'text' => $product_type['type_name']);
                  }
                  $product_restrict_types_array = $product_types_array;
                }
                ?>
                <?php
                echo '<div class="col-sm-6">' . zen_draw_pull_down_menu('product_type', $product_restrict_types_array, '', 'class="form-control"') . '</div>';
                echo zen_hide_session_id();
                echo zen_draw_hidden_field('cPath', $cPath);
                echo zen_draw_hidden_field('action', 'new_product');
                echo '</form>';
                ?>
                <?php
              } else {
                echo CATEGORY_HAS_SUBCATEGORIES;
                ?>
                <?php
              } // hide has cats
              ?>
            </div>
          </div>
          <div class="row text-center alert">
              <?php
              // warning if products are in top level categories
              $check_products_top_categories = $db->Execute("SELECT COUNT(*) AS products_errors
                                                             FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                                                             WHERE categories_id = 0");
              if ($check_products_top_categories->fields['products_errors'] > 0) {
                echo WARNING_PRODUCTS_IN_TOP_INFO . $check_products_top_categories->fields['products_errors'] . '<br>';
              }
              ?>
          </div>
          <div class="row text-center">
            <?php
// Split Page
            if ($products_query_numrows > 0) {
              echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_RESULTS_CATEGORIES, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS) . '<br>' . $products_split->display_links($products_query_numrows, MAX_DISPLAY_RESULTS_CATEGORIES, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params(array('page', 'info', 'x', 'y', 'pID')));
            }
            ?>
          </div>
          <?php
        }
        ?>
      </div>
    </div>
    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
  </body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>