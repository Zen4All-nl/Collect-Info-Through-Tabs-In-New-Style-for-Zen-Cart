<!-- Edit Attribute modal-->
<div id="editAttributeValueModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <form name="edit_attribute" method="post" enctype="multipart/form-data" id="attributeEditForm">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">
            <i class="fa fa-times" aria-hidden="true"></i>
            <span class="sr-only"><?php echo TEXT_CLOSE; ?></span>
          </button>
          <h4 class="modal-title" id="EditAttributeValueModalLabel"><?php echo TITLE_EDIT_ATTRIBUTE; ?></h4>
        </div>
        <div class="modal-body">
          <div class="row">
              <?php echo zen_draw_label(TABLE_HEADING_OPT_VALUE, 'values_id', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9" id="optionValuesPullDown">
              <!-- the contents is placed with AJAX -->
            </div>
          </div>
          <hr style="border: 1px solid #ccc; margin: 10px 0;">
          <!-- bof: Edit Prices -->
          <h5><?php echo TEXT_PRICES_AND_WEIGHTS; ?></h5>
          <div class="row">
            <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                <?php echo zen_draw_label(TABLE_HEADING_OPT_PRICE_PREFIX, 'price_prefix', 'class="control-label"'); ?>
                <?php echo zen_draw_input_field('price_prefix', '', 'size="2" class="form-control"'); ?>
            </div>
            <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                <?php echo zen_draw_label(TABLE_HEADING_OPT_PRICE, 'value_price', 'class="control-label"'); ?>
                <?php echo zen_draw_input_field('value_price', '', 'size="6" class="form-control"'); ?>
            </div>
            <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                <?php echo zen_draw_label(TABLE_HEADING_OPT_WEIGHT_PREFIX, 'products_attributes_weight_prefix', 'class="control-label"'); ?>
                <?php echo zen_draw_input_field('products_attributes_weight_prefix', '', 'size="2" class="form-control"'); ?>
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
          } else {
            echo zen_draw_hidden_field('attributes_price_factor');
            echo zen_draw_hidden_field('attributes_price_factor_offset');
            echo zen_draw_hidden_field('attributes_price_factor_onetime');
            echo zen_draw_hidden_field('attributes_price_factor_onetime_offset');
          } // ATTRIBUTES_ENABLED_PRICE_FACTOR
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
          } else {
            echo zen_draw_hidden_field('attributes_qty_prices');
            echo zen_draw_hidden_field('attributes_qty_prices_onetime');
          } // ATTRIBUTES_ENABLED_QTY_PRICES
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
          } else {
            echo zen_draw_hidden_field('attributes_price_words');
            echo zen_draw_hidden_field('attributes_price_words_free');
            echo zen_draw_hidden_field('attributes_price_letters');
            echo zen_draw_hidden_field('attributes_price_letters_free');
          } // ATTRIBUTES_ENABLED_TEXT_PRICES
          ?>
          <!-- eof: Edit Prices -->
          <h5><?php echo TEXT_ATTRIBUTES_FLAGS; ?></h5>
          <div class="row row-eq-height" id="attributeFlags">
            <!-- Content is placed with AJAX -->
          </div>
          <?php if (ATTRIBUTES_ENABLED_IMAGES == 'true') { ?>
            <h5><?php echo TEXT_ATTRIBUTES_IMAGE; ?></h5>
            <div id="attributeImage">
              <!-- Content is placed with AJAX -->
            </div>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <?php
          } else {
            echo zen_draw_hidden_field('attributes_previous_image', $attribute['attributes_image']);
            echo zen_draw_hidden_field('attributes_image', $attribute['attributes_image']);
          } // ATTRIBUTES_ENABLED_IMAGES
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
                  <?php echo zen_draw_input_field('products_attributes_maxdays', $products_attributes_maxdays, 'size="5" class="form-control"'); ?>
              </div>
              <div class="col-sm-4">
                  <?php echo zen_draw_label(TABLE_TEXT_MAX_COUNT, 'products_attributes_maxcount', 'class="control-label"'); ?>
                  <?php echo zen_draw_input_field('products_attributes_maxcount', $products_attributes_maxcount, 'size="5" class="form-control"'); ?>
              </div>
            </div>
            <?php
          }
          ?>
        </div>
        <div class="modal-footer">
            <?php echo zen_draw_hidden_field('view', 'saveAttribute'); ?>
            <?php echo zen_draw_hidden_field('attributes_id'); ?>
            <?php echo zen_draw_hidden_field('options_id'); ?>
            <?php echo zen_draw_hidden_field('products_id'); ?>
          <button type="submit" class="btn btn-primary" onclick="saveAttribute()"><i class="fa fa-save"></i></button>
          <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> <?php echo TEXT_CLOSE; ?></button>
        </div>
      </div>
    </div>
  </form>
</div>