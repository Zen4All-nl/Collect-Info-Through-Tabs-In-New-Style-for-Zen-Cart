<?php
/**
 * @package admin
 * @copyright (c) 2008-2017, Zen4All
 * @copyright Copyright 2003-2016 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Author: Zen4All
 */
require('includes/application_top.php');
$languages = zen_get_languages();
$categoryId = (isset($_GET['cID']) ? $_GET['cID'] : '');
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
  <?php
  require('includes/admin_html_head.php');
  ?>
  <body>
    <style>
      /*
       * @package admin
       * @copyright Copyright 2008-2017 Zen4All
       * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
       * @version $Id: collect_info.css Zen4All $
       */

      /* Navs */
      .nav > li.disabled > a {
        color: #999;
      }
      .nav > li.disabled > a:hover, .nav > li.disabled > a:focus {
        color: #999;
      }
      /* Tabs */
      .nav-tabs > li > a {
        color: #666;
        border-radius: 2px 2px 0 0;
        font-size: 1.2em;
      }
      .nav-tabs > li > a:hover {
        border-color: #eee #eee #ddd;
      }
      .nav-tabs {
        margin-bottom: 25px;
      }
      .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus {
        font-weight: bold;
        color: #333;
      }
      .form-control:hover {
        border: 1px solid #b9b9b9;
        border-top-color: #a0a0a0;
        -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
      }
      .table thead td span[data-toggle="tooltip"]:after, label.control-label span:after {
        font-family: FontAwesome;
        color: #1E91CF;
        content: "\f059";
        margin-left: 4px;
      }
      .fa-question-circle {
        color: #008cba;
      }
    </style>
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
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
                  <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
                    <li<?php echo ($i == 0 ? ' class="active"' : ''); ?>>
                      <a data-toggle="tab" href="#categoryNameTabs<?php echo $i + 1; ?>">
                        <?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . $languages[$i]['name']; ?>
                      </a>
                    </li>
                  <?php } ?>
                </ul>
                <div class="tab-content">
                  <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
                    <div class="tab-pane fade in<?php echo ($i == 0 ? ' active' : ''); ?>" <?php echo 'id="categoryNameTabs' . ($i + 1) . '"'; ?>>
                      <div class="form-group">
                        <?php echo zen_draw_label(TEXT_CATEGORIES_NAME, 'categories_name[' . $languages[$i]['id'] . ']', 'class="col-sm-3 control-label"'); ?>
                        <div class="col-sm-9 col-md-6">
                          <?php echo zen_draw_input_field('categories_name[' . $languages[$i]['id'] . ']', htmlspecialchars(zen_get_category_name($cInfo->categories_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), zen_set_field_length(TABLE_CATEGORIES_DESCRIPTION, 'categories_name') . ' class="form-control"'); ?>
                        </div>
                      </div>
                      <div class="form-group">
                        <?php echo zen_draw_label(TEXT_CATEGORIES_DESCRIPTION, 'categories_description[' . $languages[$i]['id'] . ']', 'class="col-sm-3 control-label"'); ?>
                        <div class="col-sm-9 col-md-6">
                          <?php echo zen_draw_textarea_field('categories_description[' . $languages[$i]['id'] . ']', 'soft', '100%', '20', htmlspecialchars(zen_get_category_description($cInfo->categories_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), 'class="editorHook form-control"'); ?>
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
                    <?php echo zen_draw_input_field('sort_order', $cInfo->sort_order, 'size="6" class="form-control"'); ?>
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
                    <?php echo zen_draw_file_field('categories_image', '', 'class="form-control"'); ?>
                  </div>
                </div>
                <?php
                $dir_info = zen_build_subdirectories_array(DIR_FS_CATALOG_IMAGES);
                $default_directory = substr($cInfo->categories_image, 0, strpos($cInfo->categories_image, '/') + 1);
                ?>
                <div class="form-group">
                  <?php echo zen_draw_label(TEXT_CATEGORIES_IMAGE_DIR, 'img_dir', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                    <?php echo zen_draw_pull_down_menu('img_dir', $dir_info, $default_directory, 'class="form-control"'); ?>
                  </div>
                </div>
                <div class="form-group">
                  <?php echo zen_draw_label(TEXT_CATEGORIES_IMAGE_MANUAL, 'categories_image_manual', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                    <?php echo zen_draw_input_field('categories_image_manual', '', 'class="form-control"'); ?>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-3">
                  </div>
                  <div class="col-sm-9 col-md-6">
                    <?php echo zen_info_image($cInfo->categories_image, $cInfo->categories_name); ?>
                    <br>
                    <?php echo $cInfo->categories_image; ?>
                  </div>
                </div>
                <div class="form-group">
                  <?php echo zen_draw_label(TEXT_IMAGES_DELETE, 'image_delete', 'class="col-sm-3 control-label"'); ?>
                  <div class="col-sm-9 col-md-6">
                    <label class="radio-inline"><?php echo zen_draw_radio_field('image_delete', '0', true) . TABLE_HEADING_NO; ?></label>
                    <label class="radio-inline"><?php echo zen_draw_radio_field('image_delete', '1', false) . TABLE_HEADING_YES; ?></label>
                  </div>
                </div>
              </div>
              <div id="categoryTabs4" class="tab-pane fade in">
                <ul class="nav nav-tabs" data-tabs="tabs">
                  <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
                    <li<?php echo ($i == 0 ? ' class="active"' : ''); ?>>
                      <a data-toggle="tab" href="#categoryMetaTagTabs<?php echo $i + 1; ?>">
                        <?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . $languages[$i]['name']; ?>
                      </a>
                    </li>
                  <?php } ?>
                </ul>
                <div class="tab-content">
                  <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
                    <div class="tab-pane fade in<?php echo ($i == 0 ? ' active' : ''); ?>" <?php echo 'id="categoryMetaTagTabs' . ($i + 1) . '"'; ?>>
                      <div class="form-group">
                        <?php echo zen_draw_label(TEXT_EDIT_CATEGORIES_META_TAGS_TITLE, 'metatags_title[' . $languages[$i]['id'] . ']', 'class="col-sm-3 control-label"'); ?>
                        <div class="col-sm-9 col-md-6">
                          <?php echo zen_draw_input_field('metatags_title[' . $languages[$i]['id'] . ']', htmlspecialchars(zen_get_category_metatags_title($cInfo->categories_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), zen_set_field_length(TABLE_METATAGS_CATEGORIES_DESCRIPTION, 'metatags_title') . ' class="form-control"'); ?>
                        </div>
                      </div>
                      <div class="form-group">
                        <?php echo zen_draw_label(TEXT_EDIT_CATEGORIES_META_TAGS_KEYWORDS, 'metatags_keywords[' . $languages[$i]['id'] . ']', 'class="col-sm-3 control-label"'); ?>
                        <div class="col-sm-9 col-md-6">
                          <?php echo zen_draw_textarea_field('metatags_keywords[' . $languages[$i]['id'] . ']', 'soft', '100%', '20', htmlspecialchars(zen_get_category_metatags_keywords($cInfo->categories_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), 'class="noEditor form-control"'); ?>
                        </div>
                      </div>
                      <div class="form-group">
                        <?php echo zen_draw_label(TEXT_EDIT_CATEGORIES_META_TAGS_DESCRIPTION, 'metatags_description[' . $languages[$i]['id'] . ']', 'class="col-sm-3 control-label"'); ?>
                        <div class="col-sm-9 col-md-6">
                          <?php echo zen_draw_textarea_field('metatags_description[' . $languages[$i]['id'] . ']', 'soft', '100%', '20', htmlspecialchars(zen_get_category_metatags_description($cInfo->categories_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), 'class="noEditor form-control"'); ?>
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
              <a href="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id . ((isset($_GET['search']) && !empty($_GET['search'])) ? '&search=' . $_GET['search'] : '')); ?>" class="btn btn-warning" id="btncancel" name="btncancel"><i class="fa fa-undo"></i> Back </a>
            </div>
          </form>
        </div>
        <div class="panel-footer text-center">
          <strong>Cittins is developed by <a href="https:zen4all.nl" title="Zen4All" target="_blank">Zen4All</a>.</strong> - Version: <a href="https://www.zen-cart.com/downloads.php?do=file&id=2171" target="_blank"><?php echo ZEN4ALL_CITTINS_VERSION; ?></a> - <a href="https://github.com/Zen4All-nl/Zen-Cart-Collect-Info-Through-Tabs-In-New-Style/releases/latest" target="_blank"><i class="fa fa-github fa-lg"></i> Github</a><br>
          <img src="images/zen4all_logo_small.png" alt="Zen4All Logo" title="Zen4All Logo" width="100" height="33"> Copyright  &COPY; 2008-<?php echo date("Y"); ?> Zen4All
        </div>
      </div>
    </div>
    <!-- Message Stack modal-->
    <div id="categoryMessageStack" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              <i class="fa fa-times" aria-hidden="true"></i>
              <span class="sr-only"><?php echo TEXT_CLOSE; ?></span>
            </button>
          </div>
          <div class="modal-body" id="categoryMessageStackText">
            <!-- content is entered using AJAX -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">
              <i class="fa fa-close"></i> <?php echo TEXT_CLOSE; ?>
            </button>
          </div>
        </div>
      </div>
    </div>
    <script>
      // this is for activting the correct tab when comming from another page
      var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

        for (i = 0; i < sURLVariables.length; i++) {
          sParameterName = sURLVariables[i].split('=');

          if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
          }
        }
      };
      var hash = '#' + getUrlParameter('activeTab');
      if (hash) {
        $('.nav-tabs a[href="' + hash + '"]').tab('show');
      }

    </script>
    <!-- load main javascript for category_info -->
    <?php require_once 'includes/javascript/z4a_jscriptCategories.php'; ?>
    <!-- footer //-->
    <?php include ($editor_handler); ?>
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
  </body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>