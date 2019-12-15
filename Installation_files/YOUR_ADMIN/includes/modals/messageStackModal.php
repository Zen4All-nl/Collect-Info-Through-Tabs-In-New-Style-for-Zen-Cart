<!-- MessageStack modal-->
<div id="MessageStackModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <i class="fa fa-times" aria-hidden="true"></i>
          <span class="sr-only"><?php echo TEXT_CLOSE; ?></span>
        </button>
      </div>
      <div class="modal-body" id="MessageStackText">
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