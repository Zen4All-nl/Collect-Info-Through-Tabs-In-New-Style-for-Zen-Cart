<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<!-- Attribute Features Modal -->
<?php
$copy_attributes_delete_first = '0';
$copy_attributes_duplicates_skipped = '0';
$copy_attributes_duplicates_overwrite = '0';
$copy_attributes_include_downloads = '1';
$copy_attributes_include_filename = '1';
?>
<div id="AttributeFeaturesModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <form name="attribute_features" method="post" enctype="multipart/form-data" id="attributeAddForm">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">
            <i class="fa fa-times" aria-hidden="true"></i>
            <span class="sr-only"><?php echo TEXT_CLOSE; ?></span>
          </button>
          <h4 class="modal-title" id="AttibuteFeaturesHeading"><?php echo TEXT_INFO_HEADING_ATTRIBUTE_FEATURES . $pInfo->products_id; ?></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-12 text-center"><strong><?php echo TEXT_PRODUCTS_ATTRIBUTES_INFO; ?></strong></div>
            <div class="col-sm-12 text-center"><strong><?php echo zen_get_products_name($pInfo->products_id, $languages_id) . ' ID# ' . $pInfo->products_id; ?></strong></div>
            <div class="col-sm-12 text-center">
              <a href="<?php echo zen_href_link(FILENAME_ATTRIBUTES_CONTROLLER, '&action=attributes_preview' . '&products_filter=' . $pInfo->products_id . '&current_category_id=' . $current_category_id); ?>" class="btn btn-info" role="button"><?php echo IMAGE_PREVIEW; ?></a> <a href="<?php echo zen_href_link(FILENAME_ATTRIBUTES_CONTROLLER, 'products_filter=' . $pInfo->products_id . '&current_category_id=' . $current_category_id); ?>" class="btn btn-primary" role="button"><?php echo IMAGE_EDIT_ATTRIBUTES; ?></a>
            </div>
            <div class="col-sm-12"><strong>' . TEXT_PRODUCT_ATTRIBUTES_DOWNLOADS . '</strong>' . zen_has_product_attributes_downloads($pInfo->products_id) . zen_has_product_attributes_downloads($pInfo->products_id, true));</div>
            <div class="col-sm-12">TEXT_INFO_ATTRIBUTES_FEATURES_DELETE . '<strong>' . zen_get_products_name($pInfo->products_id) . ' ID# ' . $pInfo->products_id . '</strong> <a href="' . zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=delete_attributes' . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . '&products_id=' . $pInfo->products_id) . '" class="btn btn-danger" role="button">' . IMAGE_DELETE . '</a></div>
            <div class="col-sm-12">TEXT_INFO_ATTRIBUTES_FEATURES_UPDATES . '<strong>' . zen_get_products_name($pInfo->products_id, $languages_id) . ' ID# ' . $pInfo->products_id . '</strong> <a href="' . zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=update_attributes_sort_order' . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . '&products_id=' . $pInfo->products_id) . '" class="btn btn-primary" role="button">' . IMAGE_UPDATE . '</a></div>
            <div class="col-sm-12">TEXT_INFO_ATTRIBUTES_FEATURES_COPY_TO_PRODUCT . '<strong>' . zen_get_products_name($pInfo->products_id, $languages_id) . ' ID# ' . $pInfo->products_id . '</strong> <a href="' . zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=attribute_features_copy_to_product' . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . '&products_id=' . $pInfo->products_id) . '" class="btn btn-primary" role="button">' . IMAGE_COPY_TO . '</a></div>
            <div class="col-sm-12"><br>' . TEXT_INFO_ATTRIBUTES_FEATURES_COPY_TO_CATEGORY . '<strong>' . zen_get_products_name($pInfo->products_id, $languages_id) . ' ID# ' . $pInfo->products_id . '</strong> <a href="' . zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=attribute_features_copy_to_category' . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . '&products_id=' . $pInfo->products_id) . '" class="btn btn-primary" role="button">' . IMAGE_COPY_TO . '</a>');</div>
            <div class="col-sm-12 text-center"><a href="' . zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> <?php echo TEXT_CLOSE; ?></button>
        </div>
      </div>
    </div>
  </form>
</div>