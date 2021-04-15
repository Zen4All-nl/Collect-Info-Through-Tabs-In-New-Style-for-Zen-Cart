<?php
/*
 * @copyright (c) 2008-2021, Zen4All
 * @copyright Copyright 2003-2020 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Author: Zen4All
 */
require 'includes/application_top.php';
$languages = zen_get_languages();
$categoryId = (isset($_GET['cID']) ? (int)$_GET['cID'] : '');
if ($categoryId != '') {
  $category = $db->Execute("SELECT c.categories_id, cd.categories_name, cd.categories_description, c.categories_image,
                                   c.sort_order, c.date_added, c.last_modified
                            FROM " . TABLE_CATEGORIES . " c
                            LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON cd.categories_id = c.categories_id
                              AND cd.language_id = " . (int)$_SESSION['languages_id'] . "
                            WHERE c.categories_id = " . (int)$categoryId);
  $cInfo = new objectInfo($category->fields);
}
?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
  <head>
    <?php require DIR_WS_INCLUDES . 'admin_html_head.php'; ?>
  </head>
  <body>
    <?php require DIR_WS_INCLUDES . 'header.php'; ?>
    <div class="container-fluid">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h1 class="panel-title"><?php echo HEADING_TITLE; ?>&nbsp;-&nbsp;<?php echo zen_output_generated_category_path($current_category_id); ?></h1>
        </div>
        <div class="panel-body">
          <form name="categoryInfo" enctype="multipart/form-data" id="categoryInfo" class="form-horizontal">
            <?php
            echo zen_draw_hidden_field('securityToken', $_SESSION['securityToken']);
            echo zen_draw_hidden_field('categories_id', $categoryId);
            echo zen_draw_hidden_field('action', ($categoryId != '' ? 'update_category' : 'insert_category'), 'id="action"');
            echo zen_draw_hidden_field('parent_category_id', $current_category_id);
            echo zen_draw_hidden_field('view', 'save_category');
            ?>
            <ul class="nav nav-tabs" data-tabs="tabs">
              <li class="active">
                <a data-toggle="tab" href="#categoryTabs1"><?php echo TAB_TITLE_GENERAL; ?></a>
              </li>
              <li>
                <a data-toggle="tab" href="#categoryTabs2"><?php echo TAB_TITLE_DATA; ?></a>
              </li>
              <li>
                <a data-toggle="tab" href="#categoryTabs3"><?php echo TAB_TITLE_IMAGE; ?></a>
              </li>
              <li>
                <a data-toggle="tab" href="#categoryTabs4"><?php echo TAB_TITLE_METATAGS; ?></a>
              </li>
            </ul>
            <div class="tab-content">
              <div id="categoryTabs1" class="tab-pane fade in active">
                <ul class="nav nav-tabs" data-tabs="tabs">
                  <?php for ($i = 0, $n = count($languages); $i < $n; $i++) { ?>
                    <li<?php echo ($i == 0 ? ' class="active"' : ''); ?>>
                      <a data-toggle="tab" href="#categoryNameTabs<?php echo $i + 1; ?>">
                        <?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . $languages[$i]['name']; ?>
                      </a>
                    </li>
                  <?php } ?>
                </ul>
                <div class="tab-content">
                  <?php for ($i = 0, $n = count($languages); $i < $n; $i++) { ?>
                    <div class="tab-pane fade in<?php echo ($i == 0 ? ' active' : ''); ?>" <?php echo 'id="categoryNameTabs' . ($i + 1) . '"'; ?>>
                      <div class="form-group">
                        <?php echo zen_draw_label(TEXT_CATEGORIES_NAME, 'categories_name[' . $languages[$i]['id'] . ']', 'class="col-sm-3 control-label"'); ?>
                        <div class="col-sm-9 col-md-6">
                          <?php echo zen_draw_input_field('categories_name[' . $languages[$i]['id'] . ']', htmlspecialchars(zen_get_category_name($cInfo->categories_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), zen_set_field_length(TABLE_CATEGORIES_DESCRIPTION, 'categories_name') . ' class="form-control" id="categories_name[' . $languages[$i]['id'] . ']"'); ?>
                        </div>
                      </div>
                      <div class="form-group">
                        <?php echo zen_draw_label(TEXT_CATEGORIES_DESCRIPTION, 'categories_description[' . $languages[$i]['id'] . ']', 'class="col-sm-3 control-label"'); ?>
                        <div class="col-sm-9 col-md-6">
                          <?php echo zen_draw_textarea_field('categories_description[' . $languages[$i]['id'] . ']', 'soft', '100%', '20', htmlspecialchars(zen_get_category_description($cInfo->categories_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), 'class="editorHook form-control" id="categories_description[' . $languages[$i]['id'] . ']"'); ?>
                        </div>
                      </div>
                    </div>
                  <?php } ?>
                </div>
              </div>
              <div id="categoryTabs2" class="tab-pane fade in">
                <div class="form-group">
                  <?php echo zen_draw_label(TEXT_EDIT_SORT_ORDER, 'sort_order', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                    <?php echo zen_draw_input_field('sort_order', $cInfo->sort_order, 'size="6" class="form-control" id="sort_order"'); ?>
                  </div>
                </div>
                <div class="form-group">
                  <?php
                  // Make an array of product types
                  $productTypesQuery = "SELECT type_id, type_name
                                        FROM " . TABLE_PRODUCT_TYPES;
                  $productTypes = $db->Execute($productTypesQuery);
                  $type_array = [];
                  foreach ($productTypes as $productType) {
                    $type_array[] = array(
                      'id' => $productType['type_id'],
                      'text' => $productType['type_name']);
                  }
                  ?>
                  <?php echo zen_draw_label(TEXT_RESTRICT_PRODUCT_TYPE, 'restrict_type', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6"><?php echo zen_draw_pull_down_menu('restrict_type', $type_array, '', 'id="restrict_type" class="form-control"'); ?></div>
                </div>
                <div class="form-group">
                  <div class="col-sm-offset-3 col-sm-9 col-md-6">
                    <button type="button" name="add_type_all" class="btn btn-info" onclick="addType('true')"><?php echo BUTTON_ADD_PRODUCT_TYPES_SUBCATEGORIES_ON; ?></button>&nbsp;<input type="button" name="add_type" value="<?php echo BUTTON_ADD_PRODUCT_TYPES_SUBCATEGORIES_OFF; ?>" class="btn btn-info" onclick="addType('false')">
                  </div>
                </div>
                <?php
                $restrictTypesQuery = "SELECT *
                                       FROM " . TABLE_PRODUCT_TYPES_TO_CATEGORY . "
                                       WHERE category_id = " . (int)$cInfo->categories_id;

                $restrictTypes = $db->Execute($restrictTypesQuery);
                if ($restrictTypes->RecordCount() > 0) {
                  ?>
                  <div class="form-group">
                    <div class="col-sm-3"><?php echo zen_draw_label(TEXT_CATEGORY_HAS_RESTRICTIONS, '', 'class="control-label"'); ?></div>
                    <div class="col-sm-9 col-md-6" id="restrict_types">
                      <?php
                      foreach ($restrictTypes as $restrictType) {
                        $typeQuery = "SELECT type_name
                                      FROM " . TABLE_PRODUCT_TYPES . "
                                      WHERE type_id = " . (int)$restrictType['product_type_id'];
                        $type = $db->Execute($typeQuery);
                        ?>
                        <button type="button" class="btn btn-warning" onclick="removeType('<?php echo $restrictType['product_type_id']; ?>')"><?php echo IMAGE_DELETE; ?></button>&nbsp;<?php echo $type->fields['type_name']; ?><br><br>
                      <?php } ?>
                    </div>
                  </div>
                <?php } ?>
              </div>
              <div id="categoryTabs3" class="tab-pane fade in">
                <div class="form-group">
                  <?php echo zen_draw_label(TEXT_EDIT_CATEGORIES_IMAGE, 'categories_image', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                    <?php echo zen_draw_file_field('categories_image', '', 'class="form-control" id="categories_image"'); ?>
                  </div>
                </div>
                <?php
                $dir_info = zen_build_subdirectories_array(DIR_FS_CATALOG_IMAGES);
                $default_directory = substr($cInfo->categories_image, 0, strpos($cInfo->categories_image, '/') + 1);
                ?>
                <div class="form-group">
                  <?php echo zen_draw_label(TEXT_CATEGORIES_IMAGE_DIR, 'img_dir', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                    <?php echo zen_draw_pull_down_menu('img_dir', $dir_info, $default_directory, 'class="form-control" id="img_dir"'); ?>
                  </div>
                </div>
                <div class="form-group">
                  <?php echo zen_draw_label(TEXT_CATEGORIES_IMAGE_MANUAL, 'categories_image_manual', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                    <?php echo zen_draw_input_field('categories_image_manual', '', 'class="form-control" id="categories_image_manual"'); ?>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-offset-3 col-sm-9 col-md-6">
                    <?php echo zen_info_image($cInfo->categories_image, $cInfo->categories_name); ?>
                    <br>
                    <?php echo $cInfo->categories_image; ?>
                  </div>
                </div>
                <div class="form-group">
                  <p class="col-sm-3 control-label"><?php echo TEXT_IMAGES_DELETE; ?></p>
                  <div class="col-sm-9 col-md-6">
                    <label class="radio-inline"><?php echo zen_draw_radio_field('image_delete', '0', true) . TABLE_HEADING_NO; ?></label>
                    <label class="radio-inline"><?php echo zen_draw_radio_field('image_delete', '1', false) . TABLE_HEADING_YES; ?></label>
                  </div>
                </div>
              </div>
              <div id="categoryTabs4" class="tab-pane fade in">
                <ul class="nav nav-tabs" data-tabs="tabs">
                  <?php for ($i = 0, $n = count($languages); $i < $n; $i++) { ?>
                    <li<?php echo ($i == 0 ? ' class="active"' : ''); ?>>
                      <a data-toggle="tab" href="#categoryMetaTagTabs<?php echo $i + 1; ?>">
                        <?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . $languages[$i]['name']; ?>
                      </a>
                    </li>
                  <?php } ?>
                </ul>
                <div class="tab-content">
                  <?php for ($i = 0, $n = count($languages); $i < $n; $i++) { ?>
                    <div class="tab-pane fade in<?php echo ($i == 0 ? ' active' : ''); ?>" <?php echo 'id="categoryMetaTagTabs' . ($i + 1) . '"'; ?>>
                      <div class="form-group">
                        <?php echo zen_draw_label(TEXT_EDIT_CATEGORIES_META_TAGS_TITLE, 'metatags_title[' . $languages[$i]['id'] . ']', 'class="col-sm-3 control-label"'); ?>
                        <div class="col-sm-9 col-md-6">
                          <?php echo zen_draw_input_field('metatags_title[' . $languages[$i]['id'] . ']', htmlspecialchars(zen_get_category_metatags_title($cInfo->categories_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), zen_set_field_length(TABLE_METATAGS_CATEGORIES_DESCRIPTION, 'metatags_title') . ' class="form-control" id="metatags_title[' . $languages[$i]['id'] . ']"'); ?>
                        </div>
                      </div>
                      <div class="form-group">
                        <?php echo zen_draw_label(TEXT_EDIT_CATEGORIES_META_TAGS_KEYWORDS, 'metatags_keywords[' . $languages[$i]['id'] . ']', 'class="col-sm-3 control-label"'); ?>
                        <div class="col-sm-9 col-md-6">
                          <?php echo zen_draw_textarea_field('metatags_keywords[' . $languages[$i]['id'] . ']', 'soft', '100%', '20', htmlspecialchars(zen_get_category_metatags_keywords($cInfo->categories_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), 'class="noEditor form-control" id="metatags_keywords[' . $languages[$i]['id'] . ']"'); ?>
                        </div>
                      </div>
                      <div class="form-group">
                        <?php echo zen_draw_label(TEXT_EDIT_CATEGORIES_META_TAGS_DESCRIPTION, 'metatags_description[' . $languages[$i]['id'] . ']', 'class="col-sm-3 control-label"'); ?>
                        <div class="col-sm-9 col-md-6">
                          <?php echo zen_draw_textarea_field('metatags_description[' . $languages[$i]['id'] . ']', 'soft', '100%', '20', htmlspecialchars(zen_get_category_metatags_description($cInfo->categories_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), 'class="noEditor form-control" id="metatags_description[' . $languages[$i]['id'] . ']"'); ?>
                        </div>
                      </div>
                    </div>
                  <?php } ?>
                </div>
              </div>
            </div>
            <div class="btn-group">
              <button id="btnsubmit" class="btn btn-primary" onclick="saveCategory()" type="submit" >
                <i class="fa fa-save"></i> <?php echo IMAGE_SAVE; ?>
              </button>
              <a href="<?php echo zen_href_link(FILENAME_CITTINS_CATEGORIES_PRODUCT_LISTING, zen_get_all_get_params(['cID'])); ?>" class="btn btn-warning" id="btncancel" name="btncancel"><i class="fa fa-undo"></i> Back </a>
            </div>
          </form>
        </div>
        <?php require 'cittinsFooter.php'; ?>
      </div>
    </div>
    <?php
    include ($editor_handler);
    require DIR_WS_INCLUDES . 'footer.php';
    /* Message Stack modal */
    require_once DIR_WS_MODALS . 'messageStackModal.php';
    ?>
  </body>
</html>
<?php
require DIR_WS_INCLUDES . 'application_bottom.php';
