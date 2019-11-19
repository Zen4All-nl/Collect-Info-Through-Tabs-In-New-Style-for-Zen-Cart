<?php
include (DIR_WS_LANGUAGES . $_SESSION['language'] . '/attributes_controller.php');
if (isset($_GET['pID']) && $_GET['pID'] != '') {
  $productsId = (int)$_GET['pID'];
  $attributesQuery = "SELECT pa.products_attributes_id, pa.products_id, pa.options_id, pa.options_values_id,
                                      pa.options_values_price, pa.price_prefix, pa.products_options_sort_order,
                                      pa.products_attributes_weight, pa.products_attributes_weight_prefix,
                                      pa.product_attribute_is_free, pa.attributes_display_only, pa.attributes_default,
                                      pa.attributes_discounted, pa.attributes_image, pa.attributes_price_base_included,
                                      pa.attributes_required
                      FROM (" . TABLE_PRODUCTS_ATTRIBUTES . " pa
                      LEFT JOIN " . TABLE_PRODUCTS_OPTIONS . " po ON pa.options_id = po.products_options_id
                        AND po.language_id = " . (int)$_SESSION['languages_id'] . ")
                      WHERE pa.products_id = " . $productsId . "
                      ORDER BY LPAD(po.products_options_sort_order,11,'0'), LPAD(pa.options_id,11,'0'), LPAD(pa.products_options_sort_order,11,'0')";
  $attributes = $db->Execute($attributesQuery);

  $productCheck = $db->Execute("SELECT products_tax_class_id
                                FROM " . TABLE_PRODUCTS . "
                                WHERE products_id = " . $productsId . "
                                LIMIT 1");
  ?>
  <style>
    .attributes_display_only button{
        background-color:#FF0;
    }
    .product_attribute_is_free button{
        background-color:#2C54F5;
    }
    .attributes_default button{
        background-color:#ffa346;
    }
    .attributes_discounted button{
        background-color:#f0f;
    }
    .attributes_price_base_included button{
        background-color:#d200f0;
    }
    .attributes_required button{
        background-color:#FF0606;
    }
    .pt-5{padding-top: 5px;}
    .pb-5{padding-bottom: 5px;}
    .pl-5{padding-left: 5px;}
    .pr-5{padding-right: 5px;}
  </style>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-3">
      <div class="dropdown">
        <button class="btn btn-default dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
            <?php echo BUTTON_ADDITITONAL_ACTIONS; ?>
          <i class="fa fa-caret-down"></i>
        </button>
        <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
          <li role="presentation"><a role="menuitem" href="<?php echo zen_href_link(FILENAME_OPTIONS_NAME_MANAGER) ?>" target="_blank"><?php echo IMAGE_OPTION_NAMES; ?></a></li>
          <li role="presentation"><a role="menuitem" href="<?php echo zen_href_link(FILENAME_OPTIONS_VALUES_MANAGER) ?>" target="_blank"><?php echo IMAGE_OPTION_VALUES; ?></a></li>
          <li role="presentation" class="divider"></li>
          <li role="presentation"><a role="menuitem" href="#" data-toggle="modal" data-target="#update_sort_order_to_default"><?php echo TEXT_UPDATE_DEFAULTE_SORT_ORDER; ?></a></li>
          <li role="presentation"><a role="menuitem" href="#" data-toggle="modal" data-target="#deleteAllAttributes"><?php echo TEXT_DELETE_ALL_OPTIONS_FROM_PRODUCT; ?></a></li>
          <li role="presentation"><a role="menuitem" href="#" data-toggle="modal" data-target="#updateAttributesCopyToProduct"><?php echo TEXT_COPY_ALL_OPTIONS_TO_PRODUCT; ?></a></li>
          <li role="presentation"><a role="menuitem" href="#" data-toggle="modal" data-target="#updateAttributesCopyToCategory"><?php echo TEXT_COPY_ALL_OPTIONS_TO_CATEGORY; ?></a></li>
        </ul>
      </div>
    </div>
    <div class="col-md-9 hidden-xs hidden-sm">
      <div class="table-responsive text-right">
        <table class="table-bordered">
          <thead>
            <tr>
              <th class="smallText text-right"><?php echo LEGEND_BOX; ?></th>
              <th class="smallText text-center"><?php echo LEGEND_ATTRIBUTES_DISPLAY_ONLY; ?></th>
              <th class="smallText text-center"><?php echo LEGEND_ATTRIBUTES_IS_FREE; ?></th>
              <th class="smallText text-center"><?php echo LEGEND_ATTRIBUTES_DEFAULT; ?></th>
              <th class="smallText text-center"><?php echo LEGEND_ATTRIBUTE_IS_DISCOUNTED; ?></th>
              <th class="smallText text-center"><?php echo LEGEND_ATTRIBUTE_PRICE_BASE_INCLUDED; ?></th>
              <th class="smallText text-center"><?php echo LEGEND_ATTRIBUTES_REQUIRED; ?></th>
              <th class="smallText text-center"><?php echo LEGEND_ATTRIBUTES_IMAGES ?></th>
              <th class="smallText text-center"><?php echo LEGEND_ATTRIBUTES_DOWNLOAD ?></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="smallText text-right"><?php echo LEGEND_KEYS; ?></td>
              <td class="text-center attributes_display_only">
                <button type="button" class="btn btn-xs btn-default" style="opacity:0.50;"><i class="fa fa-times" aria-hidden="true" style="color:#f00;"></i></button>
                <button type="button" class="btn btn-xs btn-default"><i class="fa fa-check" aria-hidden="true"></i></button>
              </td>
              <td class="text-center product_attribute_is_free">
                <button type="button" class="btn btn-xs btn-default" style="opacity:0.50;"><i class="fa fa-times" aria-hidden="true" style="color:#f00;"></i></button>
                <button type="button" class="btn btn-xs btn-default"><i class="fa fa-check" aria-hidden="true"></i></button>
              </td>
              <td class="text-center attributes_default">
                <button type="button" class="btn btn-xs btn-default" style="opacity:0.50;"><i class="fa fa-times" aria-hidden="true" style="color:#f00;"></i></button>
                <button type="button" class="btn btn-xs btn-default"><i class="fa fa-check" aria-hidden="true"></i></button>
              </td>
              <td class="text-center attributes_discounted">
                <button type="button" class="btn btn-xs btn-default" style="opacity:0.50;"><i class="fa fa-times" aria-hidden="true" style="color:#f00;"></i></button>
                <button type="button" class="btn btn-xs btn-default"><i class="fa fa-check" aria-hidden="true"></i></button>
              </td>
              <td class="text-center attributes_price_base_included">
                <button type="button" class="btn btn-xs btn-default" style="opacity:0.50;"><i class="fa fa-times" aria-hidden="true" style="color:#f00;"></i></button>
                <button type="button" class="btn btn-xs btn-default"><i class="fa fa-check" aria-hidden="true"></i></button>
              </td>
              <td class="text-center attributes_required">
                <button type="button" class="btn btn-xs btn-default" style="opacity:0.50;"><i class="fa fa-times" aria-hidden="true"></i></button>
                <button type="button" class="btn btn-xs btn-default"><i class="fa fa-check" aria-hidden="true"></i></button>
              </td>
              <td class="text-center"><?php echo zen_image(DIR_WS_IMAGES . 'icon_status_yellow.gif'); ?></td>
              <td class="text-center"><?php echo zen_image(DIR_WS_IMAGES . 'icon_status_green.gif') . '&nbsp;' . zen_image(DIR_WS_IMAGES . 'icon_status_red.gif'); ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="table-responsive clearBoth">
    <table id="productAttributes" class="table table-striped">
      <thead>
        <tr>
          <th>&nbsp;</th>
          <th><?php echo TABLE_HEADING_OPT_NAME; ?></th>
          <th><?php echo TABLE_HEADING_ID; ?></th>
          <th><?php echo TABLE_HEADING_OPT_VALUE; ?></th>
          <th class="text-right"><?php echo TABLE_HEADING_OPT_PRICE_PREFIX; ?>&nbsp;<?php echo TABLE_HEADING_OPT_PRICE; ?></th>
          <th class="text-right"><?php echo TABLE_HEADING_OPT_WEIGHT_PREFIX; ?>&nbsp;<?php echo TABLE_HEADING_OPT_WEIGHT; ?></th>
          <th class="text-right"><?php echo TABLE_HEADING_OPT_SORT_ORDER; ?></th>
          <th class="text-center hidden-xs hidden-sm"><?php echo LEGEND_BOX; ?></th>
          <th class="text-right"><?php echo TABLE_HEADING_PRICE_TOTAL; ?></th>
          <th class="text-center"><?php echo TABLE_HEADING_ACTION; ?></th>
        </tr>
      </thead>
      <tbody>
          <?php
          $current_options_name = '';
          if (isset($attributes) && $attributes != '') {
            foreach ($attributes as $attribute) { // begin foreach $attributes
              $current_attributes_products_id = $attribute['products_id'];
              $current_attributes_options_id = $attribute['options_id'];

              $products_name_only = zen_get_products_name($attribute['products_id']);
              $options_name = zen_options_name($attribute['options_id']);
              $values_name = zen_values_name($attribute['options_values_id']);
              // delete all option name values
              if ($current_options_name != $options_name) {
                $current_options_name = $options_name;
                ?>
              <tr <?php echo 'id="option-row-' . $attribute['options_id'] . '"'; ?>>
                <td>
                  <button type="button" <?php echo 'id="deleteOptionButton' . $attribute['options_id'] . '"'; ?> class="btn btn-sm btn-danger" data-toggle="modal" title="Remove Attribute Value" data-original-title="Remove Attribute Value" data-target="#deleteOptionModal" onclick="deleteOptionConfirm('<?php echo $attribute['options_id']; ?>')">
                    <i class="fa fa-trash"></i>
                  </button>
                </td>
                <td><span style="font-weight: bold;"><?php echo $current_options_name; ?></span></td>
                <td colspan="8"></td>
              </tr>
            <?php } // option name delete  ?>


            <tr <?php echo 'id="option-value-row-' . $attribute['products_attributes_id'] . '"'; ?> <?php echo 'class="option-id-' . $attribute['options_id'] . '"'; ?>>
              <td <?php echo 'id="option-value-row-' . $attribute['products_attributes_id'] . '-a"'; ?> class="align-middle">&nbsp;</td>
              <td <?php echo 'id="option-value-row-' . $attribute['products_attributes_id'] . '-b"'; ?> class="align-middle">&nbsp;</td>
              <td <?php echo 'id="option-value-row-' . $attribute['products_attributes_id'] . '-c"'; ?> class="align-middle"><?php echo $attribute['products_attributes_id']; ?></td>
              <td <?php echo 'id="option-value-row-' . $attribute['products_attributes_id'] . '-d"'; ?> class="align-middle">
                  <?php
// bof: show filename if it exists
                  if (DOWNLOAD_ENABLED == 'true') {
                    $downloadDisplayQuery = "SELECT products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount
                                           FROM " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . "
                                           WHERE products_attributes_id = " . (int)$attribute['products_attributes_id'];
                    $downloadDisplay = $db->Execute($downloadDisplayQuery);
                    if ($downloadDisplay->RecordCount() > 0) {

                      $filename_is_missing = '';
                      if (!file_exists(DIR_FS_DOWNLOAD . $downloadDisplay->fields['products_attributes_filename'])) {
                        $filename_is_missing = zen_image(DIR_WS_IMAGES . 'icon_status_red.gif');
                      } else {
                        $filename_is_missing = zen_image(DIR_WS_IMAGES . 'icon_status_green.gif');
                      }
                      ?>
                    <div class="smallText"><?php echo $filename_is_missing . '&nbsp;' . $downloadDisplay->fields['products_attributes_filename'] . '&nbsp;-&nbsp;' . TABLE_TEXT_MAX_DAYS_SHORT . '&nbsp;' . $downloadDisplay->fields['products_attributes_maxdays'] . '&nbsp;-&nbsp;' . TABLE_TEXT_MAX_COUNT_SHORT . '&nbsp;' . $downloadDisplay->fields['products_attributes_maxcount']; ?></div>
                    <?php
                  } // show downloads
                }
// eof: show filename if it exists
                ?>
                <?php echo ($attribute['attributes_image'] != '' ? zen_image(DIR_WS_IMAGES . 'icon_status_yellow.gif') . '&nbsp;' : '&nbsp;&nbsp;') . $values_name; ?>
              </td>
              <td <?php echo 'id="option-value-row-' . $attribute['products_attributes_id'] . '-e"'; ?> class="text-right align-middle"><?php echo $attribute['price_prefix']; ?>&nbsp;<?php echo $attribute['options_values_price']; ?></td>
              <td <?php echo 'id="option-value-row-' . $attribute['products_attributes_id'] . '-f"'; ?> class="text-right align-middle"><?php echo $attribute['products_attributes_weight_prefix']; ?>&nbsp;<?php echo $attribute['products_attributes_weight']; ?></td>
              <td <?php echo 'id="option-value-row-' . $attribute['products_attributes_id'] . '-g"'; ?> class="text-right align-middle"><?php echo $attribute['products_options_sort_order']; ?></td>
              <td <?php echo 'id="option-value-row-' . $attribute['products_attributes_id'] . '-h"'; ?> class="text-center align-middle hidden-xs hidden-sm">
                <span class="attributes_display_only">
                    <?php if ($attribute['attributes_display_only'] == '0') { ?>
                    <button type="button" <?php echo 'id="flag-' . $attribute['products_attributes_id'] . '-attributes_display_only"'; ?> class="btn btn-xs btn-default flagNotActive" onClick="switchFlag('1', '<?php echo $attribute['products_attributes_id']; ?>', 'attributes_display_only');" title="<?php echo LEGEND_ATTRIBUTES_DISPLAY_ONLY; ?>"><i class="fa fa-times" aria-hidden="true"></i>
                    </button>
                  <?php } else { ?>
                    <button type="button" <?php echo 'id="flag-' . $attribute['products_attributes_id'] . '-attributes_display_only"'; ?> class="btn btn-xs btn-default" onClick="switchFlag('0', '<?php echo $attribute['products_attributes_id']; ?>', 'attributes_display_only');" title="<?php echo LEGEND_ATTRIBUTES_DISPLAY_ONLY; ?>"><i class="fa fa-check" aria-hidden="true"></i>
                    </button>
                  <?php } ?>
                </span>
                <span class="product_attribute_is_free">
                    <?php if ($attribute['product_attribute_is_free'] == '0') { ?>
                    <button type="button" <?php echo 'id="flag-' . $attribute['products_attributes_id'] . '-product_attribute_is_free"'; ?> class="btn btn-xs btn-default flagNotActive" onClick="switchFlag('1', '<?php echo $attribute['products_attributes_id']; ?>', 'product_attribute_is_free');" title="<?php echo LEGEND_ATTRIBUTES_IS_FREE; ?>">
                      <i class="fa fa-times" aria-hidden="true"></i>
                    </button>
                  <?php } else { ?>
                    <button type="button" <?php echo 'id="flag-' . $attribute['products_attributes_id'] . '-product_attribute_is_free"'; ?> class="btn btn-xs btn-default" onClick="switchFlag('0', '<?php echo $attribute['products_attributes_id']; ?>', 'product_attribute_is_free');" title="<?php echo LEGEND_ATTRIBUTES_IS_FREE; ?>">
                      <i class="fa fa-check" aria-hidden="true"></i>
                    </button>
                  <?php } ?>
                </span>
                <span class="attributes_default">
                    <?php if ($attribute['attributes_default'] == '0') { ?>
                    <button type="button" <?php echo 'id="flag-' . $attribute['products_attributes_id'] . '-attributes_default"'; ?> class="btn btn-xs btn-default flagNotActive" onClick="switchFlag('1', '<?php echo $attribute['products_attributes_id']; ?>', 'attributes_default');" title="<?php echo LEGEND_ATTRIBUTES_DEFAULT; ?>">
                      <i class="fa fa-times" aria-hidden="true"></i>
                    </button>
                  <?php } else { ?>
                    <button type="button" <?php echo 'id="flag-' . $attribute['products_attributes_id'] . '-attributes_default"'; ?> class="btn btn-xs btn-default" onClick="switchFlag('0', '<?php echo $attribute['products_attributes_id']; ?>', 'attributes_default');" title="<?php echo LEGEND_ATTRIBUTES_DEFAULT; ?>">
                      <i class="fa fa-check" aria-hidden="true"></i>
                    </button>
                  <?php } ?>
                </span>
                <span class="attributes_discounted">
                    <?php if ($attribute['attributes_discounted'] == '0') { ?>
                    <button type="button" <?php echo 'id="flag-' . $attribute['products_attributes_id'] . '-attributes_discounted"'; ?> class="btn btn-xs btn-default flagNotActive" onClick="switchFlag('1', '<?php echo $attribute['products_attributes_id']; ?>', 'attributes_discounted');" title="<?php echo LEGEND_ATTRIBUTE_IS_DISCOUNTED; ?>">
                      <i class="fa fa-times" aria-hidden="true"></i>
                    </button>
                  <?php } else { ?>
                    <button type="button" <?php echo 'id="flag-' . $attribute['products_attributes_id'] . '-attributes_discounted"'; ?> class="btn btn-xs btn-default" onClick="switchFlag('0', '<?php echo $attribute['products_attributes_id']; ?>', 'attributes_discounted');" title="<?php echo LEGEND_ATTRIBUTE_IS_DISCOUNTED; ?>">
                      <i class="fa fa-check" aria-hidden="true"></i>
                    </button>
                  <?php } ?>
                </span>
                <span class="attributes_price_base_included">
                    <?php if ($attribute['attributes_price_base_included'] == '0') { ?>
                    <button type="button" <?php echo 'id="flag-' . $attribute['products_attributes_id'] . '-attributes_price_base_included"'; ?> class="btn btn-xs btn-default flagNotActive" onClick="switchFlag('1', '<?php echo $attribute['products_attributes_id']; ?>', 'attributes_price_base_included');" title="<?php echo LEGEND_ATTRIBUTE_PRICE_BASE_INCLUDED; ?>">
                      <i class="fa fa-times" aria-hidden="true"></i>
                    </button>
                  <?php } else { ?>
                    <button type="button" <?php echo 'id="flag-' . $attribute['products_attributes_id'] . '-attributes_price_base_included"'; ?> class="btn btn-xs btn-default" onClick="switchFlag('0', '<?php echo $attribute['products_attributes_id']; ?>', 'attributes_price_base_included');" title="<?php echo LEGEND_ATTRIBUTE_PRICE_BASE_INCLUDED; ?>">
                      <i class="fa fa-check" aria-hidden="true"></i>
                    </button>
                  <?php } ?>
                </span>
                <span class="attributes_required">
                    <?php if ($attribute['attributes_required'] == '0') { ?>
                    <button type="button" <?php echo 'id="flag-' . $attribute['products_attributes_id'] . '-attributes_required"'; ?> class="btn btn-xs btn-default flagNotActive" onClick="switchFlag('1', '<?php echo $attribute['products_attributes_id']; ?>', 'attributes_required');" title="<?php echo LEGEND_ATTRIBUTES_REQUIRED; ?>">
                      <i class="fa fa-times" aria-hidden="true"></i>
                    </button>
                  <?php } else { ?>
                    <button type="button" <?php echo 'id="flag-' . $attribute['products_attributes_id'] . '-attributes_required"'; ?> class="btn btn-xs btn-default" onClick="switchFlag('0', '<?php echo $attribute['products_attributes_id']; ?>', 'attributes_required');" title="<?php echo LEGEND_ATTRIBUTES_REQUIRED; ?>">
                      <i class="fa fa-check" aria-hidden="true"></i>
                    </button>
                  <?php } ?>
                </span>
              </td>
              <?php
              // calculate current total attribute price
// $attributes_values
              $attributes_price_final = zen_get_attributes_price_final($attribute['products_attributes_id'], 1, $attribute, 'false');
              $attributes_price_final_value = $attributes_price_final;
              $attributes_price_final = $currencies->display_price($attributes_price_final, zen_get_tax_rate($productCheck->fields['products_tax_class_id']), 1);
              $attributes_price_final_onetime = zen_get_attributes_price_final_onetime($attribute['products_attributes_id'], 1, $attribute);
              $attributes_price_final_onetime = $currencies->display_price($attributes_price_final_onetime, zen_get_tax_rate($productCheck->fields['products_tax_class_id']), 1);
              $new_attributes_price = '';
              if ($attribute['attributes_discounted']) {
                $new_attributes_price = zen_get_attributes_price_final($attribute['products_attributes_id'], 1, '', 'false');
                $new_attributes_price = zen_get_discount_calc($productsId, true, $new_attributes_price);
                if ($new_attributes_price != $attributes_price_final_value) {
                  $new_attributes_price = '|' . $currencies->display_price($new_attributes_price, zen_get_tax_rate($productCheck->fields['products_tax_class_id']), 1);
                } else {
                  $new_attributes_price = '';
                }
              }
              ?>
              <td <?php echo 'id="option-value-row-' . $attribute['products_attributes_id'] . '-i"'; ?> class="text-right align-middle"><?php echo $attributes_price_final . $new_attributes_price . ' ' . $attributes_price_final_onetime; ?></td>
              <td <?php echo 'id="option-value-row-' . $attribute['products_attributes_id'] . '-j"'; ?> class="text-center align-middle">
                <button type="button" <?php echo 'id="button-edit-attribute-value-' . $attribute['products_attributes_id'] . '"'; ?> class="btn btn-sm btn-primary" data-toggle="modal" title="Edit Attribute Value" data-original-title="Edit Attribute Value" data-target="#editAttributeValueModal" onclick="editAttribute('<?php echo $attribute['products_attributes_id']; ?>')">
                  <i class="fa fa-edit"></i>
                </button>
                <button type="button" <?php echo 'id="button-delete-attribute-value-' . $attribute['products_attributes_id'] . '"'; ?> class="btn btn-sm btn-danger" data-toggle="modal" title="Remove Attribute Value" data-original-title="Remove Attribute Value" data-target="#deleteOptionValueModal" onclick="deleteOptionValueConfirm('<?php echo $attribute['products_attributes_id']; ?>');">
                  <i class="fa fa-minus-circle"></i>
                </button>
              </td>
            </tr>
          <?php } // end foreach $attributes  ?>
        <?php } // end if ?>
        <tr id="addAttribute">
          <td>
            <button type="button" data-toggle="modal" title="Add Attribute" class="btn btn-sm btn-primary" onclick="addAttribute();" data-original-title="Add Attribute" data-target="#addAttributeModal">
              <i class="fa fa-plus-circle"></i>
            </button>
          </td>
          <td colspan="9">&nbsp;</td>
        </tr>
      </tbody>
    </table>
  </div>
  <?php
} else {
  ?>
  <p class="bg-info"><?php echo TEXT_SAVE_PRODUCT_FIRST; ?></p>
  <?php
}