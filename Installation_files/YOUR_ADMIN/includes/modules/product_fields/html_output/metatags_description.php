<?php
if (zen_not_null($_POST)) {
  $metatags_title = (isset($_POST['metatags_description']) ? $_POST['metatags_description'] : '');
}
?>
<p class="col-sm-3 control-label"><?php echo TEXT_META_TAGS_DESCRIPTION; ?></p>
<div class="col-sm-9 col-md-6">
  <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
    <div class="input-group">
      <span class="input-group-addon" style="vertical-align: top;">
        <?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>
      </span>
      <?php echo zen_draw_textarea_field('metatags_description[' . $languages[$i]['id'] . ']', 'soft', '100', '10', htmlspecialchars((isset($metatags_description[$languages[$i]['id']])) ? stripslashes($metatags_description[$languages[$i]['id']]) : zen_get_metatags_description($productInformation->products_id['value'], $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE), 'class="noEditor form-control"'); ?>
    </div>
    <br>
  <?php } ?>
</div>