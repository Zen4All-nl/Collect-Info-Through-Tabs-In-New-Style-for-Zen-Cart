<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<script>
  $('#select_all').change(function () {
      var checkboxes = $(this).closest('form').find(':checkbox');
      checkboxes.prop('checked', $(this).is(':checked'));
  });

  function setProductFlag(productId, flag) {

      zcJS.ajax({
          url: 'ajax.php?act=ajaxAdminCategoriesProductListing&method=setProductFlag',
          data: {
              'productId': productId,
              'flag': flag
          }
      }).done(function () {
          if (flag == '1') {
              $('#flag_' + productId).removeClass('btn-danger').addClass('btn-success').attr('title', '<?php echo IMAGE_ICON_STATUS_ON; ?>').attr('onclick', 'setProductFlag(\'' + productId + '\',\'0\')');
          } else {
              $('#flag_' + productId).removeClass('btn-success').addClass('btn-danger').attr('title', '<?php echo IMAGE_ICON_STATUS_OFF; ?>').attr('onclick', 'setProductFlag(\'' + productId + '\',\'1\')');
          }
      });
  }
</script>