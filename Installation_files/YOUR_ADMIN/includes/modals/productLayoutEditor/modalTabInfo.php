<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<!-- Delete Tab Info Modal -->

<div id="TabInfoModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">
            <i class="fa fa-times" aria-hidden="true"></i>
            <span class="sr-only"><?php echo TEXT_CLOSE; ?></span>
          </button>
          <h4 class="modal-title" id="TabInfoHeading"><?php echo TEXT_INFO_HEADING_TAB; ?></h4>
        </div>
        <div class="modal-body" id="TabInfoBody">
          <div class="row">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> <?php echo TEXT_CLOSE; ?></button>
        </div>
      </div>
    </div>
</div>