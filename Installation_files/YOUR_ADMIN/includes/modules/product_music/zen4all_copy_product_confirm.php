<?php

/**
 * @package admin
 * @copyright Copyright 2003-2016 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Author: Zen4All
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
    if (isset($_POST['copy_media']) && ($_POST['copy_media'] == '1' || $_POST['copy_media'] == 'on')) {
      $product_media = $db->Execute("SELECT media_id
                                     FROM " . TABLE_MEDIA_TO_PRODUCTS . "
                                     WHERE product_id = " . (int)$products_id);
      foreach ($product_media as $item) {
        $db->Execute("INSERT INTO " . TABLE_MEDIA_TO_PRODUCTS . " (media_id, product_id)
                      VALUES ('" . $item['media_id'] . "',
                              '" . $dup_products_id . "')");
      }
    }

    $music_extra = $db->Execute("SELECT artists_id, record_company_id, music_genre_id
                                 FROM " . TABLE_PRODUCT_MUSIC_EXTRA . "
                                 WHERE products_id = " . (int)$products_id);

    $db->Execute("INSERT INTO " . TABLE_PRODUCT_MUSIC_EXTRA . " (products_id, artists_id, record_company_id, music_genre_id)
                  VALUES ('" . (int)$dup_products_id . "',
                          '" . zen_db_input($music_extra->fields['artists_id']) . "',
                          '" . zen_db_input($music_extra->fields['record_company_id']) . "',
                          '" . zen_db_input($music_extra->fields['music_genre_id']) . "')");
