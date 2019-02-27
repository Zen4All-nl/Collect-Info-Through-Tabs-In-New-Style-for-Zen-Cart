<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<h4><?php echo TEXT_INFO_HEADING_DELETE_CATEGORY; ?></h4>

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