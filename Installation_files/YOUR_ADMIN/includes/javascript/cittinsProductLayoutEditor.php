<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<script>
  function editTab(tabId) {

  }
  function deleteTab(tabId) {

  }
  function deleteTabConfirm() {

  }
  function saveTab() {

  }
  function tabInfo(tabId) {
      var content;
      zcJS.ajax({
          url: 'ajax.php?act=ajaxAdminProductLayoutEditor&method=getTabInfo',
          data: {
              'tabId': tabId
          }
      }).done(function (resultArray) {
          console.log(resultArray);
          if (resultArray['tabIsCore'] == '1') {
              $('#TabInfoBody').html('<p>This tab is core, and can not be removed.</p>');
          } else {
              content = '<p>This tab is used, in the following product types:</p>';
              content += '<ul>';
              content += resultArray.productTypes;
              content += '</ul>';
              $('.modal-body').html(content);
          }
      });
  }
  function updateTabSortOrder() {
      var SortOrder = document.getElementById('tabs');
      $('#tabs').off('submit').on('submit', (function (e) {
          e.preventDefault();
          console.log(SortOrder);
          zcJS.ajax({
              url: 'ajax.php?act=ajaxAdminProductLayoutEditor&method=updateTabSortOrder',
              data: new FormData(SortOrder)
          }).done(function (resultArray) {
              console.log(resultArray);
          });
      }));
  }

  if (typeof jQuery.fn.sortable !== 'undefined') {
      $(function () {
          $('#sortableTabRows').sortable({
              placeholder: 'ui-state-highlight',
              start: function (event, ui) {
                  var start_pos = ui.item.index();
                  ui.item.data('start_pos', start_pos);
              },
              update: function (event, ui) {
                  var index = ui.item.index();
                  var start_pos = ui.item.data('start_pos');

                  //update the html of the moved item to the current index
                  $('#sortableTabRows tr:nth-child(' + (index + 1) + ') .sortOrder').html(index);
                  $('#sortableTabRows tr:nth-child(' + (index + 1) + ') .sortOrderValue').val(index);

                  if (start_pos < index) {
                      //update the items before the re-ordered item
                      for (var i = index; i > 0; i--) {
                          $('#sortableTabRows tr:nth-child(' + i + ') .sortOrder').html(i - 1);
                          $('#sortableTabRows tr:nth-child(' + i + ') .sortOrderValue').val(i - 1);
                      }
                  } else {
                      //update the items after the re-ordered item
                      for (var i = index + 2; i <= $('#sortableTabRows tr .sortOrder').length; i++) {
                          $('#sortableTabRows tr:nth-child(' + i + ') .sortOrder').html(i - 1);
                          $('#sortableTabRows tr:nth-child(' + i + ') .sortOrderValue').val(i - 1);
                      }
                  }
                  updateTabSortOrder();
              },
              axis: 'y'
          });
          $('#sortableTabRows').disableSelection();
      });
  }
</script>
