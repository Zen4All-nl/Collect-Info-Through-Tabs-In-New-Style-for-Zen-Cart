<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

                  $heading[] = array(
                      'text' => '<h5>' . TEXT_INFO_HEADING_STATUS_CATEGORY . '</h5>' . '<h4>' . zen_output_generated_category_path($current_category_id) . ' > ' . zen_get_category_name($cInfo->categories_id,
                              $_SESSION['languages_id']) . '</h4>'
                  );
                  $contents = array(
                      'form' => zen_draw_form('categories', FILENAME_CATEGORY_PRODUCT_LISTING,
                              'action=update_category_status&cPath=' . $_GET['cPath'] . '&cID=' . $_GET['cID'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . ($search_result ? '&search=' . $_GET['search'] : ''),
                              'post', 'enctype="multipart/form-data"') . zen_draw_hidden_field('categories_id',
                              $cInfo->categories_id) . zen_draw_hidden_field('categories_status',
                              $cInfo->categories_status)
                  );

                  $contents[] = array('text' => TEXT_CATEGORIES_STATUS_INTRO . ' <strong>' . ($cInfo->categories_status == '1' ? TEXT_CATEGORIES_STATUS_OFF : TEXT_CATEGORIES_STATUS_ON) . '</strong>');
                  $contents[] = array('text' => TEXT_CATEGORIES_STATUS_WARNING);

                  if ($cInfo->categories_status == '1') {//category is currently Enabled, so Disable it

                      $contents[] = array(
                          'text' => (
                              //hide subcategory selection if no subcategories
                              zen_has_category_subcategories($_GET['cID']) ?
                                  '<fieldset><legend>' . TEXT_SUBCATEGORIES_STATUS_INFO . '</legend>' .
                                  '<div class="radio"><label class="control-label">' . zen_draw_radio_field('set_subcategories_status', 'set_subcategories_status_off', true) . TEXT_SUBCATEGORIES_STATUS_OFF . '</label></div>' .
                                  '<div class="radio"><label class="control-label">' . zen_draw_radio_field('set_subcategories_status', 'set_subcategories_status_nochange') . TEXT_SUBCATEGORIES_STATUS_NOCHANGE . '</label></div></fieldset>' : '') .
                              //hide products selection if no products
                              (zen_get_products_to_categories($_GET['cID']) > 0 ?
                                  '<fieldset><legend>' . TEXT_PRODUCTS_STATUS_INFO . '</legend>' .
                                  '<div class="radio"><label>' . zen_draw_radio_field('set_products_status', 'set_products_status_off', true) . TEXT_PRODUCTS_STATUS_OFF . '</label></div>' .
                                  '<div class="radio"><label>' . zen_draw_radio_field('set_products_status', 'set_products_status_nochange') . TEXT_PRODUCTS_STATUS_NOCHANGE . '</label></div></fieldset>' : ''));

                  } else {//category is currently Disabled, so Enable it
                      $contents[] = array(
                          'text' => (
                              //hide subcategory selection if no subcategories
                              zen_has_category_subcategories($_GET['cID']) ?
                                  '<fieldset><legend>' . TEXT_SUBCATEGORIES_STATUS_INFO . '</legend>' .
                                  '<div class="radio"><label>' . zen_draw_radio_field('set_subcategories_status', 'set_subcategories_status_on', true) . TEXT_SUBCATEGORIES_STATUS_ON . '</label></div>' .
                                  '<div class="radio"><label>' . zen_draw_radio_field('set_subcategories_status', 'set_subcategories_status_nochange') . TEXT_SUBCATEGORIES_STATUS_NOCHANGE . '</label></div></fieldset>' : '') .
                              //hide products selection if no enabled nor disabled products
                              (zen_get_products_to_categories($_GET['cID'], true) > 0 ?
                                  '<fieldset><legend>' . TEXT_PRODUCTS_STATUS_INFO . '</legend>' .
                                  '<div class="radio"><label>' . zen_draw_radio_field('set_products_status', 'set_products_status_on', true) . TEXT_PRODUCTS_STATUS_ON . '</label></div>' .
                                  '<div class="radio"><label>' . zen_draw_radio_field('set_products_status', 'set_products_status_nochange') . TEXT_PRODUCTS_STATUS_NOCHANGE . '</label></div></fieldset>' : ''));
                  }

                  $contents[] = array(
                      'align' => 'center',
                      'text' => '<button type="submit" class="btn btn-primary">' . IMAGE_UPDATE . '</button> <a href="' . zen_href_link(FILENAME_CATEGORY_PRODUCT_LISTING,
                              'cPath=' . $cPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . ($search_result ? '&search=' . $_GET['search'] : '')) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>'
                  );