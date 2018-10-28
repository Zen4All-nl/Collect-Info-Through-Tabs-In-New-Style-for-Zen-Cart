<!-- Add Attribute modal-->
<?php
$products_options_types_list = array();
$products_options_type_array = $db->Execute("SELECT products_options_types_id, products_options_types_name
                                             FROM " . TABLE_PRODUCTS_OPTIONS_TYPES . "
                                             ORDER BY products_options_types_id");
foreach ($products_options_type_array as $item) {
  $products_options_types_list[$item['products_options_types_id']] = $item['products_options_types_name'];
}

function translate_type_to_name($opt_type) {
  global $products_options_types_list;
  return $products_options_types_list[$opt_type];
}

$options = $db->Execute("SELECT products_options_id, products_options_name, products_options_type
                         FROM " . TABLE_PRODUCTS_OPTIONS . "
                         WHERE language_id = '" . (int)$_SESSION['languages_id'] . "'
                         ORDER BY products_options_name");
$optionsDropDownArray = [];
foreach ($options as $option) {
  $optionsDropDownArray[] = [
    'id' => $option['products_options_id'],
    'text' => $option['products_options_name'] . '&nbsp;&nbsp;&nbsp;[' . translate_type_to_name($option['products_options_type']) . ']' . ' &nbsp; [ #' . $option['products_options_id'] . ' ] '
  ];
}
// set defaults for copying
$new_attr_on_overwrite = true;
$new_attr_off_overwrite = false;

$defaultPricePrefix = zen_get_show_product_switch($_GET['pID'], 'PRICE_PREFIX', 'DEFAULT_', '');
$defaultPricePrefixResult = ($defaultPricePrefix == 1 ? '+' : ($defaultPricePrefix == 2 ? '-' : ''));
$defaultWeightPrefix = zen_get_show_product_switch($_GET['pID'], 'PRODUCTS_ATTRIBUTES_WEIGHT_PREFIX', 'DEFAULT_', '');
$defaultWeightPrefixResult = ($defaultWeightPrefix == 1 ? '+' : ($defaultWeightPrefix == 2 ? '-' : ''));
$on_product_attribute_is_free = (zen_get_show_product_switch($_GET['pID'], 'ATTRIBUTE_IS_FREE', 'DEFAULT_', '') == 1 ? true : false);
$off_product_attribute_is_free = ($on_product_attribute_is_free == 1 ? false : true);
$on_attributes_display_only = (zen_get_show_product_switch($_GET['pID'], 'ATTRIBUTES_DISPLAY_ONLY', 'DEFAULT_', '') == 1 ? true : false);
$off_attributes_display_only = ($on_attributes_display_only == 1 ? false : true);
$on_attributes_default = (zen_get_show_product_switch($_GET['pID'], 'ATTRIBUTES_DEFAULT', 'DEFAULT_', '') == 1 ? true : false);
$off_attributes_default = ($on_attributes_default == 1 ? false : true);
$on_attributes_discounted = (zen_get_show_product_switch($_GET['pID'], 'ATTRIBUTES_DISCOUNTED', 'DEFAULT_', '') == 1 ? true : false);
$off_attributes_discounted = ($on_attributes_discounted == 1 ? false : true);
$on_attributes_price_base_included = (zen_get_show_product_switch($_GET['pID'], 'ATTRIBUTES_PRICE_BASE_INCLUDED', 'DEFAULT_', '') == 1 ? true : false);
$off_attributes_price_base_included = ($on_attributes_price_base_included == 1 ? false : true);
$on_attributes_required = (zen_get_show_product_switch($_GET['pID'], 'ATTRIBUTES_REQUIRED', 'DEFAULT_', '') == 1 ? true : false);
$off_attributes_required = ($on_attributes_required == 1 ? false : true);
?>
<div id="addAttributeModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <form name="add_attribute" method="post" enctype="multipart/form-data" id="attributeAddForm">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">
            <i class="fa fa-times" aria-hidden="true"></i>
            <span class="sr-only"><?php echo TEXT_CLOSE; ?></span>
          </button>
          <h4 class="modal-title" id="EditAttributeValueModalLabel"><?php echo TITLE_ADD_ATTRIBUTE; ?></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-6">
              <?php echo zen_draw_label(TABLE_HEADING_OPT_NAME, 'options_id', 'class="control-label"'); ?>
              <?php echo zen_draw_pull_down_menu('options_id', $optionsDropDownArray, '', 'id="OptionName" size="15" class="form-control" onchange="updateAttributeValueDropDown(this.value)"'); ?>
            </div>
            <div class="col-sm-6">
              <?php echo zen_draw_label(TABLE_HEADING_OPT_VALUE, 'values_id', 'class="control-label"'); ?>
              <select name="values_id[]" id="OptionValue" multiple="multiple" size="15" class="form-control">
                <option selected>&lt;-- Please select an Option Name from the list ... </option>
              </select>
            </div>
          </div>
          <hr style="border: 1px solid #ccc; margin: 10px 0;">
          <!-- bof: Edit Prices -->
          <h5><?php echo TEXT_PRICES_AND_WEIGHTS; ?></h5>
          <div class="row">
            <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
              <?php echo zen_draw_label(TABLE_HEADING_OPT_PRICE, 'default_price_prefix', 'class="control-label"'); ?>
                <?php echo zen_draw_input_field('default_price_prefix', $defaultPricePrefixResult, 'size="2" class="form-control"'); ?>
              </div>
            <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                <?php echo zen_draw_label(TABLE_HEADING_OPT_PRICE, 'value_price', 'class="control-label"'); ?>
                <?php echo zen_draw_input_field('value_price', '', 'size="6" class="form-control"'); ?>
            </div>
            <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
              <?php echo zen_draw_label(TABLE_HEADING_OPT_WEIGHT, 'default_weight_prefix', 'class="control-label"'); ?>
                <?php echo zen_draw_input_field('default_weight_prefix', $defaultWeightPrefixResult, 'size="2" class="form-control"'); ?>
              </div>
            <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                <?php echo zen_draw_label(TABLE_HEADING_OPT_WEIGHT, 'products_attributes_weight', 'class="control-label"'); ?>
                <?php echo zen_draw_input_field('products_attributes_weight', '', 'size="6" class="form-control"'); ?>
            </div>
            <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
              <?php echo zen_draw_label(TABLE_HEADING_OPT_SORT_ORDER, 'products_options_sort_order', 'class="control-label"'); ?>
              <?php echo zen_draw_input_field('products_options_sort_order', '', 'size="4" class="form-control"'); ?>
            </div>
            <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
              <?php echo zen_draw_label(TABLE_HEADING_ATTRIBUTES_PRICE_ONETIME, 'attributes_price_onetime', 'class="control-label"'); ?>
              <?php echo zen_draw_input_field('attributes_price_onetime', '', 'size="6" class="form-control"'); ?>
            </div>
          </div>
          <hr style="border: 1px solid #ccc; margin: 10px 0;">

          <?php if (ATTRIBUTES_ENABLED_PRICE_FACTOR == 'true') { ?>
            <div class="row">
              <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                <?php echo zen_draw_label(TABLE_HEADING_ATTRIBUTES_PRICE_FACTOR, 'attributes_price_factor', 'class="control-label"'); ?>
                <?php echo zen_draw_input_field('attributes_price_factor', '', 'size="6" class="form-control"'); ?>
              </div>
              <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                <?php echo zen_draw_label(TABLE_HEADING_ATTRIBUTES_PRICE_FACTOR_OFFSET, 'attributes_price_factor_offset', 'class="control-label"'); ?>
                <?php echo zen_draw_input_field('attributes_price_factor_offset', '', 'size="6" class="form-control"'); ?>
              </div>
              <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                <?php echo zen_draw_label(TABLE_HEADING_ATTRIBUTES_PRICE_FACTOR_ONETIME, 'attributes_price_factor_onetime', 'class="control-label"'); ?>
                <?php echo zen_draw_input_field('attributes_price_factor_onetime', '', 'size="6" class="form-control"'); ?>
              </div>
              <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                <?php echo zen_draw_label(TABLE_HEADING_ATTRIBUTES_PRICE_FACTOR_OFFSET_ONETIME, 'attributes_price_factor_onetime_offset', 'class="control-label"'); ?>
                <?php echo zen_draw_input_field('attributes_price_factor_onetime_offset', '', 'size="6" class="form-control"'); ?>
              </div>
            </div>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <?php
          }
          ?>

          <?php if (ATTRIBUTES_ENABLED_QTY_PRICES == 'true') { ?>
            <div class="row">
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
                <?php echo zen_draw_label(TABLE_HEADING_ATTRIBUTES_QTY_PRICES, 'attributes_qty_prices', 'class="control-label"'); ?>
                <?php echo zen_draw_input_field('attributes_qty_prices', '', 'class="form-control"'); ?>
              </div>
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
                <?php echo zen_draw_label(TABLE_HEADING_ATTRIBUTES_QTY_PRICES_ONETIME, '', 'class="control-label"'); ?>
                <?php echo zen_draw_input_field('attributes_qty_prices_onetime', '', 'class="form-control"'); ?>
              </div>
            </div>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <?php
          }
          ?>

          <?php if (ATTRIBUTES_ENABLED_TEXT_PRICES == 'true') { ?>
            <div class="row">
              <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                <?php echo zen_draw_label(TABLE_HEADING_ATTRIBUTES_PRICE_WORDS, 'attributes_price_words', 'class="control-label"'); ?>
                <?php echo zen_draw_input_field('attributes_price_words', '', 'size="6" class="form-control"'); ?>
              </div>
              <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                <?php echo zen_draw_label(TABLE_HEADING_ATTRIBUTES_PRICE_WORDS_FREE, 'attributes_price_words_free', 'class="control-label"'); ?>
                <?php echo zen_draw_input_field('attributes_price_words_free', '', 'size="6" class="form-control"'); ?>
              </div>
              <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                <?php echo zen_draw_label(TABLE_HEADING_ATTRIBUTES_PRICE_LETTERS, 'attributes_price_letters', 'class="control-label"'); ?>
                <?php echo zen_draw_input_field('attributes_price_letters', '', 'size="6" class="form-control"'); ?>
              </div>
              <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                <?php echo zen_draw_label(TABLE_HEADING_ATTRIBUTES_PRICE_LETTERS_FREE, 'attributes_price_letters_free', 'class="control-label"'); ?>
                <?php echo zen_draw_input_field('attributes_price_letters_free', '', 'size="6" class="form-control"'); ?>
              </div>
            </div>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <?php
          }
          ?>

          <!-- eof: Edit Prices -->
          <h5><?php echo TEXT_ATTRIBUTES_FLAGS; ?></h5>
            <div class="row row-eq-height" id="addAttributeFlags">
              <div class="col-sm-2" style="background-color: #ff0;padding-bottom: 10px;">
                <?php echo zen_draw_label(TEXT_ATTRIBUTES_DISPLAY_ONLY, 'attributes_display_only', 'class="control-label"'); ?>
                <label class="radio-inline">
                  <?php echo zen_draw_radio_field('attributes_display_only', '0', $off_attributes_display_only) . TABLE_HEADING_NO; ?>
                </label>
                <label class="radio-inline">
                  <?php echo zen_draw_radio_field('attributes_display_only', '1', $on_attributes_display_only) . TABLE_HEADING_YES; ?>
                </label>
              </div>
              <div class="col-sm-2" style="background-color: #2c54f5;padding-bottom: 10px;">
                <?php echo zen_draw_label(TEXT_ATTRIBUTES_IS_FREE, 'product_attribute_is_free', 'class="control-label"'); ?>
                <label class="radio-inline">
                  <?php echo zen_draw_radio_field('product_attribute_is_free', '0', $off_product_attribute_is_free) . TABLE_HEADING_NO; ?>
                </label>
                <label class="radio-inline">
                  <?php echo zen_draw_radio_field('product_attribute_is_free', '1', $on_product_attribute_is_free) . TABLE_HEADING_YES; ?>
                </label>
              </div>
              <div class="col-sm-2" style="background-color: #ffa346;padding-bottom: 10px;">
                <?php echo zen_draw_label(TEXT_ATTRIBUTES_DEFAULT, 'product_attribute_is_free', 'class="control-label"'); ?>
                <label class="radio-inline">
                  <?php echo zen_draw_radio_field('attributes_default', '0', $off_attributes_default) . TABLE_HEADING_NO; ?>
                </label>
                <label class="radio-inline">
                  <?php echo zen_draw_radio_field('attributes_default', '1', $on_attributes_default) . TABLE_HEADING_YES; ?>
                </label>
              </div>
              <div class="col-sm-2" style="background-color: #f0f;padding-bottom: 10px;">
                <?php echo zen_draw_label(TEXT_ATTRIBUTE_IS_DISCOUNTED, 'attributes_discounted', 'class="control-label"'); ?>
                <label class="radio-inline">
                  <?php echo zen_draw_radio_field('attributes_discounted', '0', $off_attributes_discounted) . TABLE_HEADING_NO; ?>
                </label>
                <label class="radio-inline">
                  <?php echo zen_draw_radio_field('attributes_discounted', '1', $on_attributes_discounted) . TABLE_HEADING_YES; ?>
                </label>
              </div>
              <div class="col-sm-2" style="background-color: #d200f0;padding-bottom: 10px;">
                <?php echo zen_draw_label(TEXT_ATTRIBUTE_PRICE_BASE_INCLUDED, 'attributes_price_base_included', 'class="control-label"'); ?>
                <label class="radio-inline">
                  <?php echo zen_draw_radio_field('attributes_price_base_included', '0', $off_attributes_price_base_included) . TABLE_HEADING_NO; ?>
                </label>
                <label class="radio-inline">
                  <?php echo zen_draw_radio_field('attributes_price_base_included', '1', $on_attributes_price_base_included) . TABLE_HEADING_YES; ?>
                </label>
              </div>
              <div class="col-sm-2" style="background-color: #FF0606;padding-bottom: 10px;">
                <?php echo zen_draw_label(TEXT_ATTRIBUTES_REQUIRED, 'attributes_required', 'class="control-label"'); ?>
                <label class="radio-inline">
                  <?php echo zen_draw_radio_field('attributes_required', '0', $off_attributes_required) . TABLE_HEADING_NO; ?>
                </label>
                <label class="radio-inline">
                  <?php echo zen_draw_radio_field('attributes_required', '1', $on_attributes_required) . TABLE_HEADING_YES; ?>
                </label>
              </div>
          </div>
          <?php if (ATTRIBUTES_ENABLED_IMAGES == 'true') { ?>
            <h5><?php echo TEXT_ATTRIBUTES_IMAGE; ?></h5>
            <div id="addAttributeImage">
              <div class="row">
                <div class="col-sm-2">No image selected</div>
                <div class="col-sm-10">
                  <?php echo zen_draw_file_field('attributes_image', '', 'class="form-control"'); ?>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-6"><?php echo zen_draw_label(TEXT_ATTRIBUTES_IMAGE_DIR, 'img_dir', 'class="control-label"') . zen_draw_pull_down_menu('img_dir', zen_build_subdirectories_array(DIR_FS_CATALOG_IMAGES), 'attributes/', 'class="form-control"'); ?></div>
                <div class="col-sm-6">
                  <?php echo zen_draw_label(TEXT_IMAGES_OVERWRITE, 'attributes_overwrite', 'çlass="control-label"'); ?>
                  <div class="btn-group" data-toggle="buttons">
                    <label class="btn <?php echo($attr_off_overwrite == true ? 'active' : ''); ?>">
                      <?php echo zen_draw_radio_field('attributes_overwrite', '0', $new_attr_off_overwrite); ?>
                      <i class="fa fa-circle-o fa-lg"></i>
                      <i class="fa fa-dot-circle-o fa-lg"></i>
                      <span><?php echo TABLE_HEADING_NO; ?></span>
                    </label>
                    <label class="btn <?php echo($attr_on_overwrite == true ? 'active' : ''); ?>">
                      <?php echo zen_draw_radio_field('attributes_overwrite', '1', $new_attr_on_overwrite); ?>
                      <i class="fa fa-circle-o fa-lg"></i>
                      <i class="fa fa-dot-circle-o fa-lg"></i>
                      <span><?php echo TABLE_HEADING_YES; ?></span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <?php
          }
          ?>

          <?php
          if (DOWNLOAD_ENABLED == 'true') {
            ?>
            <h5><?php echo TABLE_HEADING_DOWNLOAD; ?></h5>
            <div class="row">
              <div class="col-sm-4">
                <?php echo zen_draw_label(TABLE_TEXT_FILENAME, 'products_attributes_filename', 'class="control-label"'); ?>
                <?php
                $dirname = DIR_FS_DOWNLOAD;
                $files = array();
                $dir = opendir($dirname);
                $ignore = array('.', '..', '.htaccess', 'index.html');
                while (($file = readdir($dir)) !== false) {
                  if (!in_array($file, $ignore) && !is_dir($file)) {
                    $files[] = $file;
                  }
                }
                closedir($dir);
                sort($files);
                $filesArray = '<option value="" selected> - None Selected - </option>' . "\n";
                foreach ($files as $file) {
                  $filesArray .= '<option value="' . $file . '">' . $file . '</option>' . "\n";
                }
                ?>
                <select name="products_attributes_filename" class="form-control" id="attributeFileName">
                  <?php echo $filesArray; ?>
                </select>
              </div>
              <div class="col-sm-4">
                <?php echo zen_draw_label(TABLE_TEXT_MAX_DAYS, 'products_attributes_maxdays', 'class="control-label"'); ?>
                <?php echo zen_draw_input_field('products_attributes_maxdays', DOWNLOAD_MAX_DAYS, 'size="5" class="form-control"'); ?>
              </div>
              <div class="col-sm-4">
                <?php echo zen_draw_label(TABLE_TEXT_MAX_COUNT, 'products_attributes_maxcount', 'class="control-label"'); ?>
                <?php echo zen_draw_input_field('products_attributes_maxcount', DOWNLOAD_MAX_COUNT, 'size="5" class="form-control"'); ?>
              </div>
            </div>
            <?php
          }
          ?>
        </div>
        <div class="modal-footer">
          <?php echo zen_draw_hidden_field('products_id', $productsId); ?>
          <button type="submit" class="btn btn-primary" onclick="insertAttribute()"><i class="fa fa-save"></i></button>
          <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> <?php echo TEXT_CLOSE; ?></button>
        </div>
      </div>
    </div>
  </form>
</div>