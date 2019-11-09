<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class zcAjaxAdminCategoriesProductListing extends base {

  public function setProductFlag()
  {

    if (isset($_POST['flag']) && ($_POST['flag'] == '0') || ($_POST['flag'] == '1')) {
      if (isset($_POST['productId'])) {
        zen_set_product_status($_POST['productId'], $_POST['flag']);
      }
    }
  }

  public function deleteAttributes()
  {
    zen_delete_products_attributes($_GET['products_id']);
    $messageStack->add_session(SUCCESS_ATTRIBUTES_DELETED . ' ID#' . $_GET['products_id'], 'success');
    $action = '';

// reset products_price_sorter for searches etc.
    zen_update_products_price_sorter($_GET['products_id']);

    zen_redirect(zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $_GET['products_id'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
  }

  public function updateAttributesSortOrder()
  {
    zen_update_attributes_products_option_values_sort_order($_GET['products_id']);
    $messageStack->add_session(SUCCESS_ATTRIBUTES_UPDATE . ' ID#' . $_GET['products_id'], 'success');
    $action = '';
    zen_redirect(zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $_GET['products_id'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
  }

  public function updateAttributesCopyToProduct()
  {
    $copy_attributes_delete_first = ($_POST['copy_attributes'] == 'copy_attributes_delete' ? '1' : '0');
    $copy_attributes_duplicates_skipped = ($_POST['copy_attributes'] == 'copy_attributes_ignore' ? '1' : '0');
    $copy_attributes_duplicates_overwrite = ($_POST['copy_attributes'] == 'copy_attributes_update' ? '1' : '0');
    zen_copy_products_attributes($_POST['products_id'], $_POST['products_update_id']);
    $_GET['action'] = '';
    zen_redirect(zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $_GET['products_id'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
  }

  public function updateAttributesCopyToCategory()
  {
    $copy_attributes_delete_first = ($_POST['copy_attributes'] == 'copy_attributes_delete' ? '1' : '0');
    $copy_attributes_duplicates_skipped = ($_POST['copy_attributes'] == 'copy_attributes_ignore' ? '1' : '0');
    $copy_attributes_duplicates_overwrite = ($_POST['copy_attributes'] == 'copy_attributes_update' ? '1' : '0');
    $copy_to_category = $db->Execute("SELECT products_id
                                        FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                                        WHERE categories_id = " . (int)$_POST['categories_update_id']);
    foreach ($copy_to_category as $item) {
      zen_copy_products_attributes($_POST['products_id'], $item['products_id']);
    }

    $_GET['action'] = '';
    zen_redirect(zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $_GET['products_id'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
  }

  public function updateCategoryStatus()
  {
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
  }

  public function deleteCategory()
  {
    global $db;
    $data = new objectInfo($_POST);
    
    $getCategoryInfo = $db->Execute("SELECT cd.categories_name
                                     FROM " . TABLE_CATEGORIES . " c
                                     LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON cd.categories_id = c.categories_id
                                       AND cd.language_id = " . (int)$_SESSION['languages_id'] . "
                                     WHERE c.categories_id = " . (int)$data->categoryId);
    $categoryName = $getCategoryInfo->fields['categories_name'];
    $categoryChilds = zen_childs_in_category_count($data->categoryId);
    $categoryProducts = zen_products_in_category_count($data->categoryId);
    
    return([
      'categoryName' => $categoryName,
      'categoryChilds' => $categoryChilds,
      'categoryProducts' => $categoryProducts
    ]);
  }

  public function deleteCategoryConfirm()
  {
    global $db, $zc_products;
    $data = new objectInfo($_POST);
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
            if (file_exists(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/zen4all_delete_product_confirm.php')) {
              require(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/zen4all_delete_product_confirm.php');
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
  }

  public function moveCategoryConfirm()
  {
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
  }

}
