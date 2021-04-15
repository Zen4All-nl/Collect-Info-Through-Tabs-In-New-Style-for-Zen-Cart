<?php
/**
 * @package admin
 * @copyright Copyright 2003-2012 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version GIT: $Id: Author: Ian Wilson  Tue Aug 7 15:42:16 2012 +0100 Modified in v1.5.1 $
 */
include DIR_FS_CATALOG_LANGUAGES . 'english/product_info.php';
?>

<div id="previewmodal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="previewModalLabel">Product Info Preview</h4>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs" data-tabs="tabs">
            <?php
            for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
              ?>
            <li <?php if ($i == 0) echo 'class="active"'; ?>>
              <a data-toggle="tab" href="#productPreviewTabs<?php echo $i + 1; ?>">
                  <?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . $languages[$i]['name']; ?>
              </a>
            </li>
          <?php } ?>
        </ul>
        <div class="tab-content">
            <?php
            for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
              ?>
            <div class="tab-pane fade in <?php if ($i == 0) echo 'active'; ?>" id="productPreviewTabs<?php echo ($i + 1); ?>">

            </div>
          <?php } ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
