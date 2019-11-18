<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

        // attribute copier to product
        case 'attribute_features_copy_to_category':
          $_GET['categories_update_id'] = '';

          $heading[] = array('text' => '<h4>' . TEXT_INFO_HEADING_ATTRIBUTE_FEATURES . $pInfo->products_id . '</h4>');
          $contents = array('form' => zen_draw_form('products', FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'action=update_attributes_copy_to_category&cPath=' . $cPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''), 'post', 'class="form-horizontal"') . zen_draw_hidden_field('products_id', $pInfo->products_id) . zen_draw_hidden_field('categories_update_id', $_GET['categories_update_id']) . zen_draw_hidden_field('copy_attributes', $_GET['copy_attributes']));
          $contents[] = array('text' => zen_draw_label(TEXT_COPY_ATTRIBUTES_CONDITIONS, 'copy_attributes', 'class="control-label"') . '<div class="radio"><label>' . zen_draw_radio_field('copy_attributes', 'copy_attributes_delete', true) . TEXT_COPY_ATTRIBUTES_DELETE . '</label></div><div class="radio"><label>' . zen_draw_radio_field('copy_attributes', 'copy_attributes_update') . TEXT_COPY_ATTRIBUTES_UPDATE . '</label></div><div class="radio"><label>' . zen_draw_radio_field('copy_attributes', 'copy_attributes_ignore') . TEXT_COPY_ATTRIBUTES_IGNORE . '</label></div>');
          $contents[] = array('text' => zen_draw_products_pull_down_categories('categories_update_id', 'class="form-control"', '', true));
          $contents[] = array('align' => 'center', 'text' => '<button type="submit" class="btn btn-primary">' . IMAGE_COPY_TO . '</button> <a href="' . zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>');
          break;