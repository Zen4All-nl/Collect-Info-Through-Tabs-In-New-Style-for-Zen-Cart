<?php

/**
 * 2_0_0-beta_8.php
 *
 * @copyright Copyright 2003-2018 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version Author: Zen4All
 */
$db->Execute("UPDATE " . TABLE_ADMIN_PAGES . "
              SET page_key = 'catalogCittinsCategories',
                  language_key = 'BOX_CATALOG_CITTINS_CATEGORIES',
                  main_page = 'FILENAME_CITTINS_CATEGORIES'
              WHERE page_key = 'catalogZen4AllCategories'");
$db->Execute("UPDATE " . TABLE_ADMIN_PAGES . "
              SET page_key = 'catalogCittinsProductLayoutEditor',
                  language_key = 'BOX_CATALOG_CITTINS_PRODUCT_LAYOUT',
                  main_page = 'FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR'
              WHERE page_key = 'catalogZen4AllProductLayoutEditor'");
$db->Execute("UPDATE " . TABLE_ADMIN_PAGES . "
              SET page_key = 'catalogCittinsProducts',
                  language_key = 'BOX_CATALOG_CITTINS_PRODUCTS',
                  main_page = 'FILENAME_CITTINS_PRODUCTS'
              WHERE page_key = 'catalogZen4AllProducts'");
$db->Execute("UPDATE " . TABLE_ADMIN_PAGES . "
              SET page_key = 'catalogCittinsCategoriesProductListing',
                  language_key = 'BOX_CATALOG_CITTINS_CATEGORIES_PRODUCT_LISTING',
                  main_page = 'FILENAME_CITTINS_CATEGORIES_PRODUCT_LISTING'
              WHERE page_key = 'catalogZen4AllCategoriesProductListing'");