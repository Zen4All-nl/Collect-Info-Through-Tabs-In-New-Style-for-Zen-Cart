<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<h4><?php echo TEXT_INFO_HEADING_STATUS_CATEGORY; ?></h4>
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