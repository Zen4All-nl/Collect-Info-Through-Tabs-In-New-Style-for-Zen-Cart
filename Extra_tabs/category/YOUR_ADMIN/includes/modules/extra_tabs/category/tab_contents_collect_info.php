<?php
include (DIR_WS_LANGUAGES . $_SESSION['language'] . '/products_to_categories.php');
$categories_query = "SELECT DISTINCT ptoc.categories_id, cd.*
                     FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " ptoc
                     LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON cd.categories_id = ptoc.categories_id
                       AND cd.language_id = " . (int)$_SESSION['languages_id'] . "
                     ORDER BY cd.categories_name";
$categories_list = $db->Execute($categories_query);

// current products to categories
$products_list = $db->Execute("SELECT products_id, categories_id
                               FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                               WHERE products_id = " . (int)$_GET['pID']);
?>
<p><?php echo TEXT_INFO_PRODUCTS_TO_CATEGORIES_LINKER_INTRO; ?></p>
<?php
// show when product is linked
if (zen_get_product_is_linked($_GET['pID']) == 'true' and $_GET['pID'] > 0) {
  ?>
  <div class="row">
    <div class="col-sm-4">
      <?php echo zen_image(DIR_WS_IMAGES . 'icon_yellow_on.gif', IMAGE_ICON_LINKED); ?>&nbsp;&nbsp;<strong><?php echo TEXT_MASTER_CATEGORIES_ID; ?></strong>
      <?php echo zen_draw_pull_down_menu('master_category', zen_get_master_categories_pulldown($_GET['pID']), $productInfo['master_categories_id']['value'], 'class="form-control"'); ?>
    </div>
    <?php if ($productInfo['master_categories_id']['value'] <= 0) { ?>
      <div class="col-sm-4 text-warning"><?php echo WARNING_MASTER_CATEGORIES_ID; ?></div>
    <?php } ?>
    <div class="col-sm-4"><?php echo TEXT_INFO_LINKED_TO_COUNT . $products_list->RecordCount(); ?></div>
  </div>
<?php } else { ?>
  <div class="row">
    <p>
      <?php
      echo TEXT_MASTER_CATEGORIES_ID;
      echo TEXT_INFO_ID . ($_GET['pID'] > 0 ? $productInfo['master_categories_id']['value'] . ' ' . zen_get_category_name($productInfo['master_categories_id']['value'], $_SESSION['languages_id']) : $current_category_id . ' ' . zen_get_category_name($current_category_id, $_SESSION['languages_id']));
      ?>
    </p>
  </div>
<?php } ?>
<?php if ($_GET['pID'] > 0) { ?>

  <div class="row">
    <p class="text-center"><?php echo TEXT_INFO_PRODUCTS_TO_CATEGORIES_AVAILABLE; ?></p>
  </div>
  <div class="table-responsive">
    <table class="table table-striped">
      <?php
      while (!$products_list->EOF) {
        $selected_categories_check .= $products_list->fields['categories_id'];
        $products_list->MoveNext();
        if (!$products_list->EOF) {
          $selected_categories_check .= ',';
        }
      }
      $selected_categories = explode(',', $selected_categories_check);
      ?>
      <?php $cnt_columns = 0; ?>
      <tr>
        <?php
        while ($cnt_columns != MAX_DISPLAY_PRODUCTS_TO_CATEGORIES_COLUMNS) {
          $cnt_columns++;
          ?>
          <td class="text-right"><?php echo TEXT_INFO_ID; ?></td>
          <td>Categories Name</td>
        <?php } ?>
      </tr>
      <?php
      $cnt_columns = 0;
      while (!$categories_list->EOF) {
        $cnt_columns++;
        if (zen_not_null($selected_categories_check)) {
          $selected = in_array($categories_list->fields['categories_id'], $selected_categories);
        } else {
          $selected = false;
        }
        $zc_categories_checkbox = zen_draw_checkbox_field('categories_add[]', $categories_list->fields['categories_id'], $selected);
        if ($cnt_columns == 1) {
          ?>
          <tr>
          <?php } ?>
          <td class="text-right"><?php echo $categories_list->fields['categories_id']; ?></td>
          <?php if ($productInfo['master_categories_id']['value'] == $categories_list->fields['categories_id']) { ?>
            <td><?php echo zen_image(DIR_WS_IMAGES . 'icon_yellow_on.gif', IMAGE_ICON_LINKED) . '&nbsp;' . $categories_list->fields['categories_name'] . zen_draw_hidden_field('current_master_categories_id', $categories_list->fields['categories_id']); ?></td>
          <?php } else { ?>
            <td><?php echo ($selected ? '<strong>' : '') . $zc_categories_checkbox . '&nbsp;' . $categories_list->fields['categories_name'] . ($selected ? '</strong>' : ''); ?></td>
            <?php
          }
          $categories_list->MoveNext();
          if ($cnt_columns == MAX_DISPLAY_PRODUCTS_TO_CATEGORIES_COLUMNS || $categories_list->EOF) {
            if ($categories_list->EOF && $cnt_columns != MAX_DISPLAY_PRODUCTS_TO_CATEGORIES_COLUMNS) {
              while ($cnt_columns < MAX_DISPLAY_PRODUCTS_TO_CATEGORIES_COLUMNS) {
                $cnt_columns++;
                ?>
                <td class="text-right">&nbsp;</td>
                <td>&nbsp;</td>
                <?php
              }
            }
            echo '</tr>' . "\n";
            $cnt_columns = 0;
          }
        }
        ?>
    </table>
  </div>
  <?php
}