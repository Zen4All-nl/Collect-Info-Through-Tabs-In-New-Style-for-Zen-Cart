<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class zcAjaxAdminCategoriesProductListing extends base {

  /**
   * Set the product status
   */
  public function setProductFlag()
  {
    $data = new objectInfo($_POST);
    if (isset($data->flag) && ($data->flag == '0') || ($data->flag == '1')) {
      if (isset($data->productId)) {
        zen_set_product_status($data->productId, $data->flag);
      }
    }
  }

  public function setCategoryFlag()
  {

    $data = new objectInfo($_POST);
    $path = zen_output_generated_category_path($data->current_category_id);
    $categoryName = zen_get_category_name($data->categoryId, $_SESSION['languages_id']);
    $hasCategorySubcategories = zen_has_category_subcategories($data->categoryId);
    $getProductsToCategories = zen_get_products_to_categories($data->categoryId, ($data->flag == '0' ? true : false));
    return ([
      'path' => $path,
      'categoryName' => $categoryName,
      'hasCategorySubcategories' => $hasCategorySubcategories,
      'getProductsToCategories' => $getProductsToCategories
    ]);
  }

  public function setCategoryFlagConfirm()
  {
    global $db;
    $data = new objectInfo($_POST);
    // disable category and products including subcategories
    if (!isset($data->categories_id)) {
      return(['error' => 'ERROR']);
    }
    $categories_id = zen_db_prepare_input($data->categories_id);

    $categories = zen_get_category_tree($categories_id, '', '0', '', true);

    // change the status of categories and products
    zen_set_time_limit(600);
    if ($data->categories_status == '1') {//form is coming from an Enabled category which is to be changed to Disabled
      $category_status = '0'; //Disable this category
      $subcategories_status = isset($data->set_subcategories_status) && $data->set_subcategories_status == 'set_subcategories_status_off' ? '0' : ''; //Disable subcategories or no change?
      $products_status = isset($data->set_products_status) && $data->set_products_status == 'set_products_status_off' ? '0' : ''; //Disable products or no change?
    } else {//form is coming from a Disabled category which is to be changed to Enabled
      $category_status = '1'; //Enable this category
      $subcategories_status = isset($data->set_subcategories_status) && $data->set_subcategories_status == 'set_subcategories_status_on' ? '1' : ''; //also Enable subcategories or no change?
      $products_status = isset($data->set_products_status) && $data->set_products_status == 'set_products_status_on' ? '1' : ''; //Disable products or no change?
    }

    for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {

      //set categories_status
      if ($categories[$i]['id'] == $categories_id) {//always update THIS category
        $sql = "UPDATE " . TABLE_CATEGORIES . "
                SET categories_status = " . (int)$category_status . "
                WHERE categories_id = " . (int)$categories[$i]['id'];
        $db->Execute($sql);
      } elseif ($subcategories_status != '') {//optionally update subcategories if a change was selected
        $sql = "UPDATE " . TABLE_CATEGORIES . "
                SET categories_status = " . (int)$subcategories_status . "
                WHERE categories_id = " . (int)$categories[$i]['id'];
        $db->Execute($sql);
      }

      //set products_status
      if ($products_status != '') {//only execute if a change was selected
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
    }
    $newFlag = ($data->categories_status == '1' ? '0' : '1');
    $totalProducts = zen_get_products_to_categories($data->categories_id, true);
    $totalProductsOn = zen_get_products_to_categories($data->categories_id, false);
    return ([
      'newFlag' => $newFlag,
      'categoryId' => $data->categories_id,
      'cPath' => $data->cPath,
      'totalProducts' => $totalProducts,
      'totalProductsOn' => $totalProductsOn
    ]);
  }

  public function deleteAttributes()
  {
    global $messageStack;
    $data = new objectInfo($_POST);
    zen_delete_products_attributes($data->products_id);
    $messageStack->add_session(SUCCESS_ATTRIBUTES_DELETED . ' ID#' . $data->products_id, 'success');

// reset products_price_sorter for searches etc.
    zen_update_products_price_sorter($data->products_id);

    return([
      'cPath' => $data->cPath,
      'pID' => $data->products_id
    ]);
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

  public function deleteCategory()
  {
    $data = new objectInfo($_POST);

    $categoryName = $this->getCategoryName((int)$data->categoryId);
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
    if (isset($data->delete_linked) && $data->delete_linked != '') {
      $delete_linked = $data->delete_linked;
    }

// delete category and products
    if (isset($data->categories_id) && $data->categories_id != '' && is_numeric($data->categories_id) && $data->categories_id != 0) {
      $categories_id = (int)$data->categories_id;

// create list of any subcategories in the selected category,
      $categories = zen_get_category_tree($categories_id, '', 0, '', true);

      zen_set_time_limit(600);

      for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {
        $sql = "SELECT products_id
                FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                WHERE categories_id = " . $categories[$i]['id'];
        $category_products = $db->Execute($sql);

        foreach ($category_products as $category_product) {
          $cascaded_prod_id_for_delete = $category_product['products_id'];
          $cascaded_prod_cat_for_delete = [];
          $cascaded_prod_cat_for_delete[] = $categories[$i]['id'];
          $product_type = zen_get_products_type($category_product['products_id']);

          $do_delete_flag = false;
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

            for ($k = 0, $m = sizeof($product_categories); $k < $m; $k++) {
              $db->Execute("DELETE FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                            WHERE products_id = " . (int)$product_id . "
                            AND categories_id = " . (int)$product_categories[$k]);
            }
            $count_categories = $db->Execute("SELECT COUNT(categories_id) AS total
                                              FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                                              WHERE products_id = " . (int)$product_id);
            if ($count_categories->fields['total'] == 0) {
              zen_remove_product($product_id, $delete_linked);
            }
          }
        }

        zen_remove_category($categories[$i]['id']);
      }
    }
    return (['cID' => $data->categories_id]);
  }

  public function moveCategory()
  {
    $data = new objectInfo($_POST);
    $categoryName = $this->getCategoryName((int)$data->categoryId);
    return([
      'categoryName' => $categoryName
    ]);
  }

  public function moveCategoryConfirm()
  {
    global $db, $messageStack;
    $data = new objectInfo($_POST);
    if (isset($data->categories_id) && ($data->categories_id != $data->move_to_category_id)) {
      $categories_id = (int)$data->categories_id;
      $new_parent_id = (int)$data->move_to_category_id;

      $path = explode('_', zen_get_generated_category_path_ids($new_parent_id));

      if (in_array($categories_id, $path)) {
        $messageStack->add_session(ERROR_CANNOT_MOVE_CATEGORY_TO_PARENT, 'error');
        return;
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
                      SET parent_id = " . (int)$new_parent_id . ",
                          last_modified = now()
                      WHERE categories_id = " . (int)$categories_id);

// fix here - if this is a category with subcats it needs to know to loop through
// reset all products_price_sorter for moved category products
        $reset_price_sorter = $db->Execute("SELECT products_id
                                            FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                                            WHERE categories_id = " . (int)$categories_id);
        foreach ($reset_price_sorter as $item) {
          zen_update_products_price_sorter($item['products_id']);
        }

        return([
          'cPath' => $new_parent_id,
          'cID' => $data->categories_id
        ]);
      }
    } else {
      $messageStack->add_session(ERROR_CANNOT_MOVE_CATEGORY_TO_CATEGORY_SELF . $data->cPath, 'error');
      return([
        'cPath' => $data->cPath,
        'data' => $data
      ]);
    }
  }

  public function deleteProduct()
  {
    include DIR_FS_ADMIN . 'includes/languages/dutch/category_product_listing.php';
    $data = new objectInfo($_POST);
    $productName = 'ID#' . $pInfo->products_id . ': ' . zen_get_products_name($data->productId, (int)$_SESSION['languages_id']);
    $productCategories = zen_generate_category_path($data->productId, 'product');
    if (!isset($categoryPath)) {
      $categoryPath = '';
    }
    $productCategoriesString = '';

    for ($i = 0, $n = sizeof($productCategories); $i < $n; $i++) {
      $categoryPath = '';
      for ($j = 0, $k = sizeof($productCategories[$i]); $j < $k; $j++) {
        $categoryPath .= $productCategories[$i][$j]['text'];
        if ($j + 1 < $k) {
          $categoryPath .= '&nbsp;&gt;&nbsp;';
        }
      }
      if (sizeof($productCategories) > 1 && zen_get_parent_category_id($data->productId) == $productCategories[$i][sizeof($productCategories[$i]) - 1]['id']) {
        $productCategoriesString .= '<div class="checkbox">' . "\n";
        $productCategoriesString .= '  <label>' . "\n";
        $productCategoriesString .= '    <strong><span class="text-danger">' . zen_draw_checkbox_field('product_categories[]', $productCategories[$i][sizeof($productCategories[$i]) - 1]['id'], false) . $categoryPath . '</span></strong>' . "\n";
        $productCategoriesString .= '  </label>' . "\n";
        $productCategoriesString .= '</div>' . "\n";
        $productMasterCategoryString = $categoryPath;
      } else {
        $productCategoriesString .= '<div class="checkbox">' . "\n";
        $productCategoriesString .= '  <label>' . "\n";
        $productCategoriesString .= '    <strong>' . zen_draw_checkbox_field('product_categories[]', $productCategories[$i][sizeof($productCategories[$i]) - 1]['id'], true) . $categoryPath . '</strong>' . "\n";
        $productCategoriesString .= '  </label>' . "\n";
        $productCategoriesString .= '</div>' . "\n";
      }
    }
    $productCategoriesStringReturn = substr($productCategoriesString, 0, -4);
    $intro = sprintf(TEXT_DELETE_PRODUCT_INTRO, $data->productId);
    $masterCat = '<span class="text-danger"><strong>' . TEXT_MASTER_CATEGORIES_ID . ' ID#' . zen_get_parent_category_id($data->productId) . ' ' . $productMasterCategoryString . '</strong></span>';

    return([
      'productName' => $productName,
      'masterCat' => $masterCat,
      'productCategories' => $productCategories,
      'intro' => $intro,
      'contents' => $productCategoriesStringReturn
    ]);
  }

  public function deleteProductConfirm()
  {
    global $db, $zc_products;
    $data = new objectInfo($_POST);

    $do_delete_flag = false;
    if (isset($data->products_id) && isset($data->product_categories) && is_array($data->product_categories)) {
      $product_id = (int)$data->products_id;
      $product_categories = $data->product_categories;
      $do_delete_flag = true;

      $delete_linked = 'true';
      if (isset($data->delete_linked) && $data->delete_linked == 'delete_linked_no') {
        $delete_linked = 'false';
      } else {
        $delete_linked = 'true';
      }
    }

    /*
     * Zen4All: commented as this is nott used for now
      if (!empty($cascaded_prod_id_for_delete) && !empty($cascaded_prod_cat_for_delete)) {
      $product_id = $cascaded_prod_id_for_delete;
      $product_categories = $cascaded_prod_cat_for_delete;
      $do_delete_flag = true;
      // no check for $delete_linked here, because it should already be passed from categories.php
      }
     */

    if ($do_delete_flag) {

      /*
       * PRODUCT_TYPE_SPECIFIC_INSTRUCTIONS_GO__BELOW_HERE
       */
      if (file_exists(DIR_WS_MODULES . $zc_products->get_handler($data->product_type) . '/zen4all_delete_product_confirm.php')) {
        require(DIR_WS_MODULES . $zc_products->get_handler($data->product_type) . '/zen4all_delete_product_confirm.php');
      }
      /*
       * PRODUCT_TYPE_SPECIFIC_INSTRUCTIONS_GO__ABOVE__HERE
       */

      /*
       * now do regular non-type-specific delete:
       * remove product from all its categories:
       */
      for ($k = 0, $m = sizeof($product_categories); $k < $m; $k++) {
        $db->Execute("DELETE FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                      WHERE products_id = " . (int)$product_id . "
                      AND categories_id = " . (int)$product_categories[$k]);
      }
      // confirm that product is no longer linked to any categories
      $count_categories = $db->Execute("SELECT COUNT(categories_id) AS total
                                        FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                                        WHERE products_id = " . (int)$product_id);

      // if not linked to any categories, do delete:
      if ($count_categories->fields['total'] == '0') {
        zen_remove_product($product_id, $delete_linked);
      }
    } // endif $do_delete_flag
    return ([
      'pID' => $product_id
    ]);
  }

  public function setSessionColumnValue()
  {
    $data = new objectInfo($_POST);
    $newValue = ($_SESSION['columnVisibility'][$data->column] == 'true' ? 'false' : 'true');
    $_SESSION['columnVisibility'][$data->column] = $newValue;
    return([
      'data' => $data,
      'session' => $_SESSION['columnVisibility']
    ]);
  }

  /**
   * 
   * @global type $db
   * @param integer $categoryId
   * @return string
   */
  private function getCategoryName($categoryId)
  {
    global $db;
    $getCategoryInfo = $db->Execute("SELECT cd.categories_name
                                     FROM " . TABLE_CATEGORIES . " c
                                     LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON cd.categories_id = c.categories_id
                                       AND cd.language_id = " . (int)$_SESSION['languages_id'] . "
                                     WHERE c.categories_id = " . (int)$categoryId);
    $categoryName = $getCategoryInfo->fields['categories_name'];
    return $categoryName;
  }

  public function messageStack()
  {
    global $messageStack;
    if ($messageStack->size > 0) {
      return([
        'modalMessageStack' => $messageStack->output()]);
    }
  }

}
