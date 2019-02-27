<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

        case 'move_category':
          $heading[] = array('text' => '<h4>' . TEXT_INFO_HEADING_MOVE_CATEGORY . '</h4>');

          $contents = array('form' => zen_draw_form('move_category', FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'action=move_category_confirm&cPath=' . $cPath, 'post', 'class="form-horizontal"') . zen_draw_hidden_field('categories_id', $cInfo->categories_id));
          $contents[] = array('text' => sprintf(TEXT_MOVE_CATEGORIES_INTRO, $cInfo->categories_name));
          $contents[] = array('text' => zen_draw_label(sprintf(TEXT_MOVE, $cInfo->categories_name), 'move_to_category_id') . zen_draw_pull_down_menu('move_to_category_id', zen_get_category_tree(), $current_category_id, 'class="form-control"'));
          $contents[] = array('align' => 'center', 'text' => '<button type="submit" class="btn btn-primary">' . IMAGE_MOVE . '</button> <a href="' . zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>');
          break;