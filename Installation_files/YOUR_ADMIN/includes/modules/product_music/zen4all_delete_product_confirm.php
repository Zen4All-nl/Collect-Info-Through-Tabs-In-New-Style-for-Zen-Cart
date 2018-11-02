<?php

/**
 * @package admin
 * @copyright Copyright 2008-2017 Zen4All
 * @copyright Copyright 2003-2017 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: delete_product_confirm.php 17088 2010-07-31 05:08:33Z drbyte $
 */
// Delete media components, but only if the product is no longer cross-linked to another:
$resVal = $db->Execute("SELECT categories_id
                        FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                        WHERE products_id = " . (int)$product_id);
if ($resVal->RecordCount() < 2) {
  // First we delete related records from related product-type tables:

  $product_media = $db->Execute("SELECT media_id
                                 FROM " . TABLE_MEDIA_TO_PRODUCTS . "
                                 WHERE product_id = " . (int)$product_id);
  foreach ($product_media as $item) {
    $db->Execute("DELETE FROM " . TABLE_MEDIA_TO_PRODUCTS . "
                  WHERE media_id = " . (int)zen_db_input($item['media_id']) . "
                  AND product_id = " . (int)$product_id);
  }

  $music_extra = $db->Execute("SELECT artists_id, record_company_id, music_genre_id
                               FROM " . TABLE_PRODUCT_MUSIC_EXTRA . "
                               WHERE products_id = " . (int)$product_id);
  if ($music_extra->RecordCount() > 0) {
    $db->Execute("DELETE FROM " . TABLE_PRODUCT_MUSIC_EXTRA . "
                  WHERE products_id = " . (int)$product_id . "
                  AND artists_id = " . zen_db_input($music_extra->fields['artists_id']) . "
                  AND record_company_id = " . zen_db_input($music_extra->fields['record_company_id']) . "
                  AND music_genre_id = " . zen_db_input($music_extra->fields['music_genre_id']));
  }
}
