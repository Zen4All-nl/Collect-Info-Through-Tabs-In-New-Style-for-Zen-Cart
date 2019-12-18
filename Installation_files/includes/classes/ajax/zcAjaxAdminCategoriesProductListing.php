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

  /**
   * 
   * @return array
   */
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

  /**
   * 
   * @global type $db
   * @return array
   */
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

  /**
   * 
   * @global type $messageStack
   * @return array
   */
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

  /**
   * 
   */
  public function updateAttributesSortOrder()
  {
    zen_update_attributes_products_option_values_sort_order($_GET['products_id']);
    $messageStack->add_session(SUCCESS_ATTRIBUTES_UPDATE . ' ID#' . $_GET['products_id'], 'success');
    $action = '';
    zen_redirect(zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $_GET['products_id'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
  }

  /**
   * 
   */
  public function updateAttributesCopyToProduct()
  {
    $copy_attributes_delete_first = ($_POST['copy_attributes'] == 'copy_attributes_delete' ? '1' : '0');
    $copy_attributes_duplicates_skipped = ($_POST['copy_attributes'] == 'copy_attributes_ignore' ? '1' : '0');
    $copy_attributes_duplicates_overwrite = ($_POST['copy_attributes'] == 'copy_attributes_update' ? '1' : '0');
    zen_copy_products_attributes($_POST['products_id'], $_POST['products_update_id']);
    $_GET['action'] = '';
    zen_redirect(zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $_GET['products_id'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
  }

  /**
   * 
   */
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

  /**
   * 
   * @return array
   */
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

  /**
   * 
   * @global type $db
   * @global integer $zc_products
   * @return array
   */
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

  /**
   * 
   * @return array
   */
  public function moveCategory()
  {
    $data = new objectInfo($_POST);
    $categoryName = $this->getCategoryName((int)$data->categoryId);
    return([
      'categoryName' => $categoryName
    ]);
  }

  /**
   * 
   * @global type $db
   * @global type $messageStack
   * @return array
   */
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

  /**
   * 
   * @return array
   */
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

  /**
   * 
   * @global type $db
   * @global integer $zc_products
   * @return array
   */
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

  /**
   * 
   * @return array
   */
  public function moveProduct()
  {
    $data = new objectInfo($_POST);

    $product_categories = zen_generate_category_path($data->productId, 'product');
    for ($i = 0, $n = sizeof($product_categories); $i < $n; $i++) {
      $category_path = '';
      for ($j = 0, $k = sizeof($product_categories[$i]); $j < $k; $j++) {
        $category_path .= $product_categories[$i][$j]['text'];
        if ($j + 1 < $k) {
          $category_path .= '&nbsp;&gt;&nbsp;';
        }
      }
      if (sizeof($product_categories) > 1 && zen_get_parent_category_id($data->productId) == $product_categories[$i][sizeof($product_categories[$i]) - 1]['id']) {
        $product_master_category_string = $category_path;
      }
    }
    $currentParrentCatId = zen_get_parent_category_id($data->productId) . ' ' . $product_master_category_string;

    $currentCategories = zen_output_generated_category_path($data->productId, 'product');
    return([
      'currentParrentCatId' => $currentParrentCatId,
      'currentCategories' => $currentCategories
    ]);
  }

  /**
   * 
   * @global type $db
   * @global type $messageStack
   * @return array
   */
  public function moveProductConfirm()
  {
    global $db, $messageStack;
    $data = new objectInfo($_POST);

    $products_id = (int)$data->products_id;
    $new_parent_id = (int)$data->move_to_category_id;

    $duplicate_check = $db->Execute("SELECT COUNT(*) AS total
                                     FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                                     WHERE products_id = " . (int)$products_id . "
                                     AND categories_id = " . (int)$new_parent_id);

    if ($duplicate_check->fields['total'] < 1) {
      $db->Execute("UPDATE " . TABLE_PRODUCTS_TO_CATEGORIES . "
                    SET categories_id = " . (int)$new_parent_id . "
                    WHERE products_id = " . (int)$products_id . "
                    AND categories_id = " . (int)$data->current_category_id);

      // reset master_categories_id if moved from original master category
      $check_master = $db->Execute("SELECT products_id, master_categories_id
                                    FROM " . TABLE_PRODUCTS . "
                                    WHERE products_id = " . (int)$products_id);
      if ($check_master->fields['master_categories_id'] == (int)$data->current_category_id) {
        $db->Execute("UPDATE " . TABLE_PRODUCTS . "
                      SET master_categories_id = " . (int)$new_parent_id . "
                      WHERE products_id = " . (int)$products_id);
      }

      // reset products_price_sorter for searches etc.
      zen_update_products_price_sorter((int)$products_id);
      zen_record_admin_activity('Moved product ' . (int)$products_id . ' from category ' . (int)$data->current_category_id . ' to category ' . (int)$new_parent_id, 'notice');
    } else {
      $messageStack->add_session(ERROR_CANNOT_MOVE_PRODUCT_TO_CATEGORY_SELF, 'error');
    }
    return ([
      'pID' => $products_id
    ]);
  }

  /**
   * 
   * @global type $db
   * @return array
   */
  public function copyProduct()
  {
    global $db;
    $data = new objectInfo($_POST);
    $productModel = zen_get_products_model((int)$data->productId);
    $productName = zen_get_products_name((int)$data->productId, (int)$_SESSION['languages_id']);
    $currentCategories = zen_output_generated_category_path($data->productId, 'product');

// only ask about attributes if defined
    $productHasAttributes = zen_has_product_attributes($data->productId, 'false');
//are any metatags defined
    $metatagsDefined = false;
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
      if (zen_get_metatags_description($data->productId,
                      $languages[$i]['id']) . zen_get_metatags_keywords($data->productId,
                      $languages[$i]['id']) . zen_get_metatags_title($data->productId, $languages[$i]['id']) != '') {
        $metatagsDefined = true;
      }
    }
    $productIsLinked = zen_get_product_is_linked($pInfo->products_id);
    $productHasDiscounts = zen_has_product_discounts($pInfo->products_id);

    return ([
      'productId' => (int)$data->productId,
      'productModel' => $productModel,
      'productName' => $productName,
      'currentCategories' => $currentCategories,
      'productHasAttributes' => $productHasAttributes,
      'metatagsDefined' => $metatagsDefined,
      'productIsLinked' => $productIsLinked,
      'productHasDiscounts' => $productHasDiscounts
    ]);
  }

  /**
   * 
   * @global type $db
   * @global type $messageStack
   */
  public function copyProductConfirm()
  {
    global $db, $messageStack;
    $data = new objectInfo($_POST);
    $products_id = (int)$data->products_id;
    $categories_id = (int)$data->categories_id;

    if ($data->copy_as == 'link') {
      if ($categories_id != $current_category_id) {
        $check = $db->Execute("SELECT COUNT(*) AS total
                               FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                               WHERE products_id = " . $products_id . "
                               AND categories_id = " . $categories_id);
        if ($check->fields['total'] < '1') {
          $db->Execute("INSERT INTO " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id)
                        VALUES (" . $products_id . ", " . $categories_id . ")");

          zen_record_admin_activity('Product ' . $products_id . ' copied as link to category ' . $categories_id . ' via admin console.', 'info');
        }
      } else {
        $messageStack->add_session(ERROR_CANNOT_LINK_TO_SAME_CATEGORY, 'error');
      }
    } elseif ($data->copy_as == 'duplicate') {

      $product = $db->Execute("SELECT products_type, products_quantity, products_model, products_image,
                                      products_price, products_virtual, products_date_available, products_weight,
                                      products_tax_class_id, manufacturers_id,
                                      products_quantity_order_min, products_quantity_order_units, products_priced_by_attribute,
                                      product_is_free, product_is_call, products_quantity_mixed,
                                      product_is_always_free_shipping, products_qty_box_status, products_quantity_order_max, products_sort_order,
                                    products_price_sorter, master_categories_id
                               FROM " . TABLE_PRODUCTS . "
                               WHERE products_id = " . $products_id);

// fix Product copy from if Unit is 0
      if ($product->fields['products_quantity_order_units'] == 0) {
        $sql = "UPDATE " . TABLE_PRODUCTS . "
                SET products_quantity_order_units = 1
                WHERE products_id = " . $products_id;
        $results = $db->Execute($sql);
      }
// fix Product copy from if Minimum is 0
      if ($product->fields['products_quantity_order_min'] == 0) {
        $sql = "UPDATE " . TABLE_PRODUCTS . "
                SET products_quantity_order_min = 1
                WHERE products_id = " . $products_id;
        $results = $db->Execute($sql);
      }

      $products_quantity = (float)$product->fields['products_quantity'];
      $products_price = (float)$product->fields['products_price'];
      $products_weight = (float)$product->fields['products_weight'];

      $db->Execute("INSERT INTO " . TABLE_PRODUCTS . " (products_type, products_quantity, products_model, products_image,
                                                        products_price, products_virtual, products_date_added, products_date_available,
                                                        products_weight, products_status, products_tax_class_id,
                                                        manufacturers_id, products_quantity_order_min, products_quantity_order_units,
                                                        products_priced_by_attribute, product_is_free, product_is_call, products_quantity_mixed,
                                                        product_is_always_free_shipping, products_qty_box_status, products_quantity_order_max,
                                                        products_sort_order, products_price_sorter, master_categories_id)
                    VALUES (" . (int)$product->fields['products_type'] . ",
                            " . (float)$products_quantity . ",
                            '" . zen_db_input($product->fields['products_model']) . "',
                            '" . zen_db_input($product->fields['products_image']) . "',
                            " . (int)$products_price . ",
                            " . (int)$product->fields['products_virtual'] . ",
                            now(),
                            " . (zen_not_null(zen_db_input($product->fields['products_date_available'])) ? "'" . zen_db_input($product->fields['products_date_available']) . "'" : 'null') . ",
                            " . (float)$products_weight . ",
                            0,
                            " . (int)$product->fields['products_tax_class_id'] . ",
                            " . (int)$product->fields['manufacturers_id'] . ",
                            " . (float)($product->fields['products_quantity_order_min'] == 0 ? 1 : $product->fields['products_quantity_order_min']) . ",
                            '" . (float)($product->fields['products_quantity_order_units'] == 0 ? 1 : $product->fields['products_quantity_order_units']) . ",
                            " . (int)$product->fields['products_priced_by_attribute'] . ",
                            " . (int)$product->fields['product_is_free'] . ",
                            " . (int)$product->fields['product_is_call'] . ",
                            " . (int)$product->fields['products_quantity_mixed'] . ",
                            " . (int)$product->fields['product_is_always_free_shipping'] . ",
                           " . (int)$product->fields['products_qty_box_status'] . ",
                            " . (float)$product->fields['products_quantity_order_max'] . ",
                            " . (int)$product->fields['products_sort_order'] . ",
                            " . (float)$product->fields['products_price_sorter'] . ",
                            " . (int)$categories_id . ")");

      $dup_products_id = (int)$db->Insert_ID();

      $descriptions = $db->Execute("SELECT language_id, products_name, products_description, products_url
                                    FROM " . TABLE_PRODUCTS_DESCRIPTION . "
                                    WHERE products_id = " . $products_id);
      foreach ($descriptions as $description) {
        $db->Execute("INSERT INTO " . TABLE_PRODUCTS_DESCRIPTION . " (products_id, language_id, products_name, products_description, products_url, products_viewed)
                      VALUES (" . $dup_products_id . ",
                              " . (int)$description['language_id'] . ",
                              '" . zen_db_input($description['products_name']) . "',
                              '" . zen_db_input($description['products_description']) . "',
                              '" . zen_db_input($description['products_url']) . "',
                              0)");
      }

      $db->Execute("INSERT INTO " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id)
                    VALUES (" . $dup_products_id . ", " . $categories_id . ")");

// FIX HERE
/////////////////////////////////////////////////////////////////////////////////////////////
// copy attributes to Duplicate
      if (!empty($data->copy_attributes) && $data->copy_attributes == 'copy_attributes_yes') {
        /* - @Zen4All:
         *  commented out for now, code seems to unused
          if (DOWNLOAD_ENABLED == 'true') {
          $copy_attributes_include_downloads = '1';
          $copy_attributes_include_filename = '1';
          } else {
          $copy_attributes_include_downloads = '0';
          $copy_attributes_include_filename = '0';
          }
         */

        zen_copy_products_attributes($products_id, $dup_products_id);
      }

// copy meta tags to Duplicate
      if (!empty($data->copy_metatags) && $data->copy_metatags == 'copy_metatags_yes') {
        $metatags_status = $db->Execute("SELECT metatags_title_status, metatags_products_name_status, metatags_model_status, metatags_price_status, metatags_title_tagline_status
                                         FROM " . TABLE_PRODUCTS . "
                                         WHERE products_id = " . $products_id);

        $db->Execute("UPDATE " . TABLE_PRODUCTS . "
                      SET metatags_title_status = " . (int)$metatags_status->fields['metatags_title_status'] . ",
                          metatags_products_name_status = " . (int)$metatags_status->fields['metatags_products_name_status'] . ",
                          metatags_model_status = " . (int)$metatags_status->fields['metatags_model_status'] . ",
                          metatags_price_status= " . (int)$metatags_status->fields['metatags_price_status'] . ",
                          metatags_title_tagline_status = " . (int)$metatags_status->fields['metatags_title_tagline_status'] . "
                      WHERE products_id = " . $dup_products_id);

        $metatags_descriptions = $db->Execute("SELECT language_id, metatags_title, metatags_keywords, metatags_description
                                               FROM " . TABLE_META_TAGS_PRODUCTS_DESCRIPTION . "
                                               WHERE products_id = " . $products_id);

        foreach ($metatags_descriptions as $metatags_description) {
          $db->Execute("INSERT INTO " . TABLE_META_TAGS_PRODUCTS_DESCRIPTION . " (products_id, language_id, metatags_title, metatags_keywords, metatags_description)
                        VALUES (" . $dup_products_id . ", " . (int)$metatags_description['language_id'] . ", '" . zen_db_input($metatags_description['metatags_title']) . "', '" . zen_db_input($metatags_description['metatags_keywords']) . "', '" . zen_db_input($metatags_description['metatags_description']) . "')");

          $messageStack->add_session(sprintf(TEXT_COPY_AS_DUPLICATE_METATAGS, (int)$metatags_descriptions->fields['language_id'], $products_id, $dup_products_id), 'success');
        }
      }

// copy linked categories to Duplicate
      if (!empty($data->copy_linked_categories) && $data->copy_linked_categories == 'copy_linked_categories_yes') {
        $categories_from = $db->Execute("SELECT categories_id
                                         FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                                         WHERE products_id = " . $products_id);

        foreach ($categories_from as $row) {
//"insert ignore" as the new product already has one entry for the master category
          $db->Execute("INSERT IGNORE INTO " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id)
                        VALUES (" . $dup_products_id . ", " . (int)$row['categories_id'] . ")");
          $messageStack->add_session(sprintf(TEXT_COPY_AS_DUPLICATE_CATEGORIES, (int)$row['categories_id'], $products_id, $dup_products_id), 'success');
        }
      }

// copy product discounts to Duplicate
      if (!empty($data->copy_discounts) && $data->copy_discounts == 'copy_discounts_yes') {
        zen_copy_discounts_to_product($products_id, $dup_products_id);
        $messageStack->add_session(sprintf(TEXT_COPY_AS_DUPLICATE_DISCOUNTS, $products_id, $dup_products_id), 'success');
      }

      zen_record_admin_activity('Product ' . $products_id . ' duplicated as product ' . $dup_products_id . ' via admin console.', 'info');

      $zco_notifier->notify('NOTIFY_MODULES_COPY_TO_CONFIRM_DUPLICATE', array('products_id' => $products_id, 'dup_products_id' => $dup_products_id));

      $products_id = $dup_products_id; //reset for further use in price update and final redirect to new linked product or new duplicated product
    }// EOF duplication
// reset products_price_sorter for searches etc.
    zen_update_products_price_sorter($products_id);
  }

  /**
   * 
   * @return array
   */
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

}
