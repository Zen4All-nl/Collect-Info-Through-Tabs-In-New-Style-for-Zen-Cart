<?php
include (DIR_WS_LANGUAGES . $_SESSION['language'] . '/products_to_categories.php');
$catagories_query = "SELECT DISTINCT ptoc.categories_id, cd.*
                     FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " ptoc
                     LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON cd.categories_id = ptoc.categories_id
                       AND cd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                     ORDER BY cd.categories_name";
$categories_list = $db->Execute($catagories_query);

// current products to categories
$products_list = $db->Execute("SELECT products_id, categories_id
                               FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                               WHERE products_id = '" . $_GET['pID'] . "'");
?>
<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <td><?php echo TEXT_INFO_PRODUCTS_TO_CATEGORIES_LINKER_INTRO; ?></td>
      </tr>
    </thead>
    <tbody>
        <?php
        // show when product is linked
        if (zen_get_product_is_linked($_GET['pID']) == 'true' and $_GET['pID'] > 0) {
          ?>
        <tr>

          <td>
            <div class="col-sm-4 text-right">
                <?php
                echo zen_image(DIR_WS_IMAGES . 'icon_yellow_on.gif', IMAGE_ICON_LINKED) . '&nbsp;&nbsp;';
                echo '<strong>' . TEXT_MASTER_CATEGORIES_ID . '</strong> </div><div class="col-sm-4">' . zen_draw_pull_down_menu('master_category', zen_get_master_categories_pulldown($_GET['pID']), $pInfo->master_categories_id, 'class="form-control"');
                if ($pInfo->master_categories_id <= 0) {
                  echo '&nbsp;&nbsp;' . '</div><div class="col-sm-4 text-wanring">' . WARNING_MASTER_CATEGORIES_ID;
                }
                echo '</div><div class="col-sm-4">' . TEXT_INFO_LINKED_TO_COUNT . $products_list->RecordCount();
                ?>
            </div>
          </td>
        </tr>
      <?php } else { ?>
        <tr>
          <td>
            <div class="col-sm-4 text-right">
                <?php
                echo TEXT_MASTER_CATEGORIES_ID;
                echo TEXT_INFO_ID . ($_GET['pID'] > 0 ? $pInfo->master_categories_id . ' ' . zen_get_category_name($pInfo->master_categories_id, $_SESSION['languages_id']) : $current_category_id . ' ' . zen_get_category_name($current_category_id, $_SESSION['languages_id']));
                ?>
            </div>
          </td>
        </tr>
<?php } ?>
    </tbody>
  </table>

</div>
<?php if ($_GET['pID'] > 0){ ?>
<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <td colspan="<?php echo MAX_DISPLAY_PRODUCTS_TO_CATEGORIES_COLUMNS * 2; ?>" align="center"><?php echo TEXT_INFO_PRODUCTS_TO_CATEGORIES_AVAILABLE; ?></td>
      </tr>
    </thead>
    <tbody>
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
        <?php
        $cnt_columns = 0;
        echo '<tr>';
        while ($cnt_columns != MAX_DISPLAY_PRODUCTS_TO_CATEGORIES_COLUMNS) {
          $cnt_columns++;
          echo '<td align="right">' . TEXT_INFO_ID . '</td>' . '<td align="left">' . '&nbsp;&nbsp;Categories Name' . '</td>';
        }
        echo '</tr>';
//        echo '<tr class="dataTableHeadingRow">';

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
            echo '<tr>';
          }
          echo '  <td align="right">' . $categories_list->fields['categories_id'] . '</td>' . "\n";
          if ($pInfo->master_categories_id == $categories_list->fields['categories_id']) {
            echo '  <td align="left">' . '&nbsp;' . zen_image(DIR_WS_IMAGES . 'icon_yellow_on.gif', IMAGE_ICON_LINKED) . '&nbsp;' . $categories_list->fields['categories_name'] . zen_draw_hidden_field('current_master_categories_id', $categories_list->fields['categories_id']) . '</td>' . "\n";
          } else {
            echo '  <td align="left">' . ($selected ? '<strong>' : '') . $zc_categories_checkbox . '&nbsp;' . $categories_list->fields['categories_name'] . ($selected ? '</strong>' : '') . '</td>' . "\n";
          }
          $categories_list->MoveNext();
          if ($cnt_columns == MAX_DISPLAY_PRODUCTS_TO_CATEGORIES_COLUMNS or $categories_list->EOF) {
            if ($categories_list->EOF and $cnt_columns != MAX_DISPLAY_PRODUCTS_TO_CATEGORIES_COLUMNS) {
              while ($cnt_columns < MAX_DISPLAY_PRODUCTS_TO_CATEGORIES_COLUMNS) {
                $cnt_columns++;
                echo '  <td align="right">' . '&nbsp;' . '</td>' . "\n";
                echo '  <td align="left">' . '&nbsp;' . '</td>' . "\n";
              }
            }
            echo '</tr>' . "\n";
            $cnt_columns = 0;
          }
        }
        ?>
    </tbody>
  </table>
</div>
<?php } ?>