<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<!-- Delete Tab Info Modal -->

<div id="TabEditModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <i class="fa fa-times" aria-hidden="true"></i>
          <span class="sr-only"><?php echo TEXT_CLOSE; ?></span>
        </button>
        <h4 class="modal-title" id="TabEditHeading"><?php echo TEXT_EDIT_HEADING_TAB; ?></h4>
      </div>
      <div class="modal-body" id="TabEditBody">
        <form name="edie-tab" class="form-horizontal" method="post">
          <div class="from-group">
              <?php echo zen_draw_label($text, $for, 'class="control-label col-sm-3"'); ?>
            <div class="col-sm-9 col-md-6">
                <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
                <div class="row">
                  <span>
                      <?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>
                  </span>
                  <?php echo zen_draw_input_field('tab_name', '', 'class="form-control"'); ?>
                </div>
              <?php } ?>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> <?php echo TEXT_CLOSE; ?></button>
      </div>
    </div>
  </div>
</div>