<?php
/*
 * @copyright (c) 2008-2020, Zen4All
 * @copyright Copyright 2003-2020 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Author: Zen4All
 */
require 'includes/application_top.php';
// temp language file loading
include DIR_WS_LANGUAGES . $_SESSION['language'] . '/product_types.php';
$languages = zen_get_languages();

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if (zen_not_null($action)) {
  switch ($action) {
    case 'layout_save':
      $data = new objectInfo($_POST);
      $action = '';
      $deleteQuery = "DELETE FROM " . TABLE_PRODUCT_FIELDS_TO_TYPE . "
                      WHERE product_type = " . (int)$data->product_type;
      $db->Execute($deleteQuery);
      foreach ($data->tab as $value) {
        $sortOrder = 1;
        foreach ($value['layout'] as $layout) {
          $insertQuery = "INSERT INTO " . TABLE_PRODUCT_FIELDS_TO_TYPE . " (product_type, field_name, sort_order, tab_id, show_in_frontend)
                          VALUES (" . (int)$data->product_type . ",
                                  '" . $layout['field_name'] . "',
                                  " . (int)$sortOrder . ",
                                  " . (int)$layout['tab_id'] . ",
                                  " . (int)$layout['show_in_frontend'] . ")";
          $db->Execute($insertQuery);
          $sortOrder++;
        }
      }
      zen_redirect(zen_href_link(FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR, 'set_product_type=' . (int)$data->product_type));
      break;
    case 'insert_field' :
    case 'save_field' :
      $data = new objectInfo($_POST);
      $filteredSelectValueId = array_filter($data->select_value_id);
      $filteredSelectValueTexts = array_filter($data->select_value_text);
      if (isset($filteredSelectValueId) && $filteredSelectValueId != '') {
        $selectValueId = implode('|', $filteredSelectValueId);
        $selectValueTexts = implode('|', $filteredSelectValueTexts);
      }
      $selectedProductTypeId = (int)$_GET['set_product_type'];
      $checkFieldQuery = "SELECT id
                          FROM " . TABLE_PRODUCT_FIELDS . "
                          WHERE field_name = '" . $data->field_name . "'";
      $checkField = $db->Execute($checkFieldQuery);
      if ($checkField->RecordCount > 0) {
        // return with error
        $messageStack;
        zen_redirect(zen_href_link(FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR, (isset($selectedProductTypeId) && $selectedProductTypeId != '' ? 'set_product_type=' . $selectedProductTypeId : '')));
      } else {
        $sqlDataArray = [
          'description' => $data->description,
          'default_value' => $data->default_value,
          'select_value_id' => $selectValueId,
          'select_value_text' => $selectValueTexts,
          'length' => (int)$data->length,
          'core' => (int)$data->core,
          'configuration_key' => $data->configuration_key,
          'label_define' => $data->label_define,
        ];
        if ($action == 'insert_field') {
          $insertSqlData = [
            'field_name' => $data->field_name,
            'type' => (int)$data->type,
            'language_string' => (int)$data->language_string,
          ];
          $sqlArray = array_merge($sqlDataArray, $insertSqlData);
          zen_db_perform(TABLE_PRODUCT_FIELDS, $sqlArray);

          $typeSqlInformation = setSqlTypeInformation($data->field_name, $data->type, $data->length, $data->default_value);

          if ($data->language_string == '0') {
            $db->Execute("ALTER TABLE " . TABLE_PRODUCTS_EXTRA . " ADD " . $typeSqlInformation);
          } elseif ($data->language_string == '1') {
            $db->Execute("ALTER TABLE " . TABLE_PRODUCTS_DESCRIPTION_EXTRA . " ADD " . $typeSqlInformation);
          }
        } elseif ($action == 'save_field') {
          zen_db_perform(TABLE_PRODUCT_FIELDS, $sqlDataArray, 'update', "id = '" . (int)$data->id . "'");

          $db->Execute("ALTER TABLE " . TABLE_PRODUCTS_EXTRA . " CHANGE " . $fieldName->fields['field_name'] . " " . $fieldName->fields['field_name'] . " VARCHAR(" . (int)$data->length . ")");
        }
      }
      $action = '';
      zen_redirect(zen_href_link(FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR, (isset($selectedProductTypeId) && $selectedProductTypeId != '' ? 'set_product_type=' . $selectedProductTypeId : '')));
      break;
    case 'delete_field_confirm' :

      $action = '';
      $data = new objectInfo($_POST);
      $fieldName = $db->Execute("SELECT field_name
                                 FROM " . TABLE_PRODUCT_FIELDS . "
                                 WHERE id = " . (int)$data->field_id);
      $db->Execute("DELETE FROM " . TABLE_PRODUCT_FIELDS . "
                    WHERE id = " . (int)$data->field_id);

      $db->Execute("ALTER TABLE " . TABLE_PRODUCTS_EXTRA . " DROP " . $fieldName->fields['field_name']);
      zen_redirect(zen_href_link(FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR, (isset($selectedProductTypeId) && $selectedProductTypeId != '' ? 'set_product_type=' . $selectedProductTypeId : '')));
      break;
    case'insert_tab':
      $data = new objectInfo($_POST);

      if (isset($data->product_type_id) && $data->product_type_id != '') {
        $productTypeIds = implode('|', $data->product_type_id);
      }
      $db->Execute("INSERT INTO " . TABLE_PRODUCT_TABS . " (sort_order, product_type_id)
                    VALUES(" . (int)$data->sort_order . ", '" . $productTypeIds . "')");
      $tabId = zen_db_insert_id();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];

        $db->Execute("INSERT INTO " . TABLE_PRODUCT_TAB_NAMES . " (id, language_id, tab_name)
                      VALUES (" . (int)$tabId . ", " . (int)$language_id . " , '" . $data->tab_name[$language_id] . "')");
      }
      $action = '';
      zen_redirect(zen_href_link(FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR));
      break;
    case'save_tab':
      $data = new objectInfo($_POST);
      unset($data->securityToken);

      $sortOrder = 1;
      foreach ($data as $tabId) {
        if (isset($tabId['product_type_id']) && $tabId['product_type_id'] != '') {
          $productTypeIds = implode('|', $tabId['product_type_id']);
        } else {
          $productTypeIds = null;
        }

        $db->Execute("UPDATE " . TABLE_PRODUCT_TABS . "
                                 SET sort_order = " . (int)$sortOrder . ",
                                     product_type_id = '" . $productTypeIds . "'
                                 WHERE id = " . (int)$tabId['id']);
        $sortOrder++;

        for ($i = 0, $n = count($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];
          $db->Execute("UPDATE " . TABLE_PRODUCT_TAB_NAMES . "
                        SET tab_name = '" . $tabId['tab_name'][$language_id] . "'
                        WHERE id = " . (int)$tabId['id'] . "
                        AND language_id = " . (int)$language_id);
        }
      }
      break;
    case'delete_tab_confirm':
      break;
    case'insert_product_type':
      break;
    case'save_product_type':
      break;
    case'delete_product_type_confirm':
      break;
  }
}
$productTypesQuery = "SELECT type_id, type_name
                      FROM " . TABLE_PRODUCT_TYPES;
$productTypes = $db->Execute($productTypesQuery);

$productTypeArray = [];
foreach ($productTypes as $productType) {
  $productTypeArray[] = [
    'id' => $productType['type_id'],
    'text' => $productType['type_name']
  ];
}
$selectedProductTypeId = (isset($_GET['set_product_type']) ? (int)$_GET['set_product_type'] : '1');

$fieldsToAllProductTypeQuery = "SELECT DISTINCT field_name
                                FROM " . TABLE_PRODUCT_FIELDS_TO_TYPE . "
                                ORDER BY field_name";
$fieldsToAllProductType = $db->Execute($fieldsToAllProductTypeQuery);

$fieldsToAllProductTypeArray = [];
foreach ($fieldsToAllProductType as $fieldToAllProductType) {
  $fieldsToAllProductTypeArray[] = ['fieldName' => $fieldToAllProductType['field_name']];
}

$fieldsToProductTypeQuery = "SELECT *
                             FROM " . TABLE_PRODUCT_FIELDS_TO_TYPE . "
                             WHERE product_type = " . (int)$selectedProductTypeId . "
                             ORDER BY tab_id, sort_order";
$fieldsToProductType = $db->Execute($fieldsToProductTypeQuery);

$fieldsToProductTypeArray = [];
foreach ($fieldsToProductType as $fieldToProductType) {
  $fieldsToProductTypeArray[] = [
    'productTypeId' => $fieldToProductType['product_type'],
    'fieldName' => $fieldToProductType['field_name'],
    'sortOrder' => $fieldToProductType['sort_order'],
    'tabId' => $fieldToProductType['tab_id'],
    'showInFrontend' => $fieldToProductType['show_in_frontend']
  ];
}

$htmlOutputDirList = dirListProductFields(INCLUDES_HTML_OUTPUT_FOLDER);

$allFieldsArray = [];
foreach ($htmlOutputDirList as $field) {
  $allFieldsArray[] = str_replace('.php', '', $field);
}

$fieldTypesArray = [
  ['id' => '0', 'text' => PLEASE_SELECT],
  ['id' => '1', 'text' => 'string'],
  ['id' => '2', 'text' => 'text'],
  ['id' => '3', 'text' => 'integer'],
  ['id' => '4', 'text' => 'decimal'],
  ['id' => '5', 'text' => 'float'],
  ['id' => '6', 'text' => 'dropdown'],
  ['id' => '7', 'text' => 'radio'],
  ['id' => '8', 'text' => 'checkbox'],
  ['id' => '9', 'text' => 'datetime'],
];
?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
  <head>`
    <?php require DIR_WS_INCLUDES . 'admin_html_head.php'; ?>
  </head>
  <body>
    <!-- header //-->
    <?php require DIR_WS_INCLUDES . 'header.php'; ?>
    <!-- header_eof //-->
    <!-- body //-->
    <?php if ($action == '') { ?>
      <script>
        $(function () {
          $('#available_fields, [id^=tab-]').sortable({
            connectWith: '.connectedSortable',
            placeholder: 'ui-state-highlight'
          }).disableSelection();
          $('#tabs').sortable({
            placeholder: 'ui-state-highlight',
            items: 'div:not(.ui-state-disabled)'
          }).disableSelection();
          $('#tabs div').disableSelection();
          $('[id^=tab-]').sortable({
            receive: function (event, ui) {
              const dropElemTxt = $(ui.item).find('span').text();
              const dropElemId = $(ui.item).attr('id');
              const dropTabId = $(ui.item).parent().attr('name');
              let replacement = '';
              replacement += '<div id="' + dropElemId + '" class="ui-state-default" role="button">\n';
              replacement += '<input type="checkbox" name="tab[' + dropTabId + '][layout][' + dropElemId + '][show_in_frontend]" value="1" checked="checked">&nbsp;|&nbsp;\n';
              replacement += '<span>' + dropElemTxt + '</span>\n';
              replacement += '<input type="hidden" name="tab[' + dropTabId + '][layout][' + dropElemId + '][field_name]" value="' + dropElemId + '">\n';
              replacement += '<input type="hidden" name="tab[' + dropTabId + '][layout][' + dropElemId + '][tab_id]" value="' + dropTabId + '">\n';
              replacement += '</div>\n';
              $(ui.item).replaceWith(replacement);
            }
          });
          $('#available_fields').sortable({
            receive: function (event, ui) {
              const dropElemTxt = $(ui.item).find('span').text();
              const dropElemId = $(ui.item).attr('id');
              let replacement = '';
              replacement += '<div id="' + dropElemId + '" class="ui-state-default" role="button">\n';
              replacement += '<span>' + dropElemTxt + '</span>\n';
              replacement += '</div>\n';
              $(ui.item).replaceWith(replacement);
            }
          });
        });
      </script>
    <?php } ?>
    <div class="container-fluid">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h1 class="panel-title"><?php echo HEADING_TITLE . (!empty($action) ? ' <i class="fa fa-angle-double-right" aria-hidden="true"></i> ' : '') . $actionTitle; ?></h1>
        </div>
        <div class="panel-body">
          <div id="actionPanel" class="panel col-sm-2">
            <h4><?php echo NAV_TITLE_ACTIONS; ?></h4>
            <a href="<?php echo zen_href_link(FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR, 'action=add_field' . (isset($selectedProductTypeId) && $selectedProductTypeId != '' ? '&set_product_type=' . $selectedProductTypeId : '')) ?>" class="btn btn-primary btn-block" role="button"><?php echo BUTTON_ADD_FIELD; ?></a>
            <a href="<?php echo zen_href_link(FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR, 'action=delete_field' . (isset($selectedProductTypeId) && $selectedProductTypeId != '' ? '&set_product_type=' . $selectedProductTypeId : '')) ?>" class="btn btn-primary btn-block" role="button"><?php echo BUTTON_DELETE_FIELD; ?></a>
            <a href="<?php echo zen_href_link(FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR, 'action=tabs' . (isset($selectedProductTypeId) && $selectedProductTypeId != '' ? '&set_product_type=' . $selectedProductTypeId : '')) ?>" class="btn btn-primary btn-block" role="button"><?php echo BUTTON_TABS; ?></a>
            <a href="<?php echo zen_href_link(FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR, 'action=productTypes' . '&set_product_type=' . $selectedProductTypeId) ?>" class="btn btn-primary btn-block" role="button"><?php echo BUTTON_PRODUCT_TYPES; ?></a>
          </div>
          <?php
          switch ($action) {
            case 'add_field':
              ?>
              <script>
                let row = 3;
                $(function () {
                  $('#select_values').hide();
                  $('#field_type select').change(function () {
                    if ($('#field_type option:selected').text() == 'string') {
                      $('#language_string').show();
                      $('#select_values').hide();
                      $('#field_length').show();
                      $('#select_values').find('input:text').val('');
                    } else if ($('#field_type option:selected').text() == 'text') {
                      $('#language_string').show();
                      $('#select_values').hide();
                      $('#field_length').show();
                      $('#select_values').find('input:text').val('');
                    } else if ($('#field_type option:selected').text() == 'integer') {
                      $('#select_values').hide();
                      $('#language_string').hide();
                      $('#field_length').show();
                      $('#select_values').find('input:text').val('');
                    } else if ($('#field_type option:selected').text() == 'decimal') {
                      $('#select_values').hide();
                      $('#language_string').hide();
                      $('#field_length').show();
                      $('#select_values').find('input:text').val('');
                    } else if ($('#field_type option:selected').text() == 'float') {
                      $('#select_values').hide();
                      $('#language_string').hide();
                      $('#field_length').show();
                      $('#select_values').find('input:text').val('');
                    } else if ($('#field_type option:selected').text() == 'dropdown') {
                      $('#select_values').show();
                      $('#language_string').hide();
                      $('#field_length').hide();
                      $('#field_length').find('input:text').val('');
                    } else if ($('#field_type option:selected').text() == 'radio') {
                      $('#select_values').show();
                      $('#language_string').hide();
                      $('#field_length').hide();
                      $('#field_length').find('input:text').val('');
                    } else if ($('#field_type option:selected').text() == 'checkbox') {
                      $('#select_values').show();
                      $('#language_string').hide();
                      $('#field_length').hide();
                      $('#field_length').find('input:text').val('');
                    } else if ($('#field_type option:selected').text() == 'datetime') {
                      $('#select_values').hide();
                      $('#language_string').hide();
                      $('#field_length').hide();
                      $('#select_values').find('input:text').val('');
                      $('#field_length').find('input:text').val('');
                    }
                  });
                  $('#add_value').click(function () {
                    let html;
                    html = '<div class="col-sm-4">';
                    html += '<input type="text" name="select_value_id[' + row + ']" class="form-control" />';
                    html += '</div>';
                    html += '<div class="col-sm-8">';
                    html += '<input type="text" name="select_value_text[' + row + ']" class="form-control" />';
                    html += '</div>';
                    html += '<div class="col-sm-12"><hr></div>';
                    $('#selection_fields').append(html);
                    row++;
                  });
                });
              </script>
              <div class="col-sm-10">
                <div class="row">
                  <?php echo zen_draw_form('add_field', FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR, 'set_product_type=' . (isset($_GET['set_product_type']) && $_GET['set_product_type'] != '' ? (int)$_GET['set_product_type'] . '&' : '') . 'action=insert_field', 'post', 'class="form-horizontal"'); ?>
                  <div id="field_name" class="form-group">
                    <?php echo zen_draw_label(TEXT_FIELD_NAME, 'field_name', 'class="col-sm-3 control-label"'); ?>
                    <i class="fa fa-lg fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo TEXT_INFO_FIELD_NAME; ?>"></i>
                    <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_input_field('field_name', '', 'placeholder="field_name" class="form-control"', true); ?>
                    </div>
                  </div>
                  <div id="field_description" class="form-group">
                    <?php echo zen_draw_label(TEXT_FIELD_DESCRIPTION, 'description', 'class="col-sm-3 control-label"'); ?>
                    <i class="fa fa-lg fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo TEXT_INFO_FIELD_DESCRIPTION; ?>"></i>
                    <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_input_field('description', '', 'class="form-control"'); ?>
                    </div>
                  </div>
                  <div id="label_define" class="form-group">
                    <?php echo zen_draw_label(TEXT_FIELD_LABEL_DEFINE, 'label_define', 'class="col-sm-3 control-label"'); ?>
                    <i class="fa fa-lg fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo TEXT_INFO_FIELD_LABEL_DEFINE; ?>"></i>
                    <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_input_field('label_define', '', 'placeholder="LABEL_DEFINE" class="form-control"'); ?>
                    </div>
                  </div>
                  <div id="field_type" class="form-group">
                    <?php echo zen_draw_label(TEXT_FIELD_TYPE, 'type', 'class="col-sm-3 control-label"'); ?>
                    <i class="fa fa-lg fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo TEXT_INFO_FIELD_TYPE; ?>"></i>
                    <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_pull_down_menu('type', $fieldTypesArray, '', 'class="form-control"'); ?>
                    </div>
                  </div>
                  <div id="default_value" class="form-group">
                    <?php echo zen_draw_label(TEXT_FIELD_DEFAULT_VALUE, 'default_value', 'class="col-sm-3 control-label"'); ?>
                    <i class="fa fa-lg fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo TEXT_INFO_FIELD_DEFAULT_VALUE; ?>"></i>
                    <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_input_field('default_value', '', 'class="form-control"'); ?>
                    </div>
                  </div>
                  <div id="select_values" class="form-group">
                    <?php echo zen_draw_label(TEXT_FIELD_SELECT_VALUES, 'select_values', 'class="col-sm-3 control-label"'); ?>
                    <i class="fa fa-lg fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo TEXT_INFO_FIELD_SELECT_VALUES; ?>"></i>
                    <div id="selection_fields" class="col-sm-9 col-md-6">
                      <div class="col-sm-4"><label>value (id)</label></div>
                      <div class="col-sm-8"><label>text (define)</label></div>
                      <div class="col-sm-4">
                        <?php echo zen_draw_input_field('select_value_id[1]', '', 'class="form-control"'); ?>
                      </div>
                      <div class="col-sm-8">
                        <?php echo zen_draw_input_field('select_value_text[1]', '', 'class="form-control"'); ?>
                      </div>
                      <div class="col-sm-12"><hr></div>
                      <div class="col-sm-4">
                        <?php echo zen_draw_input_field('select_value_id[2]', '', 'class="form-control"'); ?>
                      </div>
                      <div class="col-sm-8">
                        <?php echo zen_draw_input_field('select_value_text[2]', '', 'class="form-control"'); ?>
                      </div>
                      <div class="col-sm-12"><hr></div>
                    </div>
                    <div class="col-sm-12 text-right">
                      <?php echo zen_html_button('<i class="fa fa-lg fa-plus"></i>', 'info', 'id="add_value"'); ?>
                    </div>
                  </div>
                  <div id="field_length" class="form-group">
                    <?php echo zen_draw_label(TEXT_FIELD_LENGTH, 'length', 'class="col-sm-3 control-label"'); ?>
                    <i class="fa fa-lg fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo TEXT_INFO_FIELD_LENGTH; ?>"></i>
                    <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_input_field('length', '', 'class="form-control"'); ?>
                    </div>
                  </div>
                  <div id="language_string" class="form-group">
                    <?php echo zen_draw_label(TEXT_FIELD_LANGUAGE_STRING, 'language_string', 'class="col-sm-3 control-label"'); ?>
                    <i class="fa fa-lg fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo TEXT_INFO_FIELD_LANGUAGE_STRING; ?>"></i>
                    <div class="col-sm-9 col-md-6">
                      <div class="radio-inline">
                        <label><?php echo zen_draw_radio_field('language_string', '0', true) . TEXT_NO; ?></label>
                      </div>
                      <div class="radio-inline">
                        <label><?php echo zen_draw_radio_field('language_string', '1') . TEXT_YES; ?></label>
                      </div>
                    </div>
                  </div>
                  <div class="form-group text-right">
                    <?php echo zen_draw_hidden_field('core', '0'); ?>
                    <?php echo zen_html_button(IMAGE_INSERT, 'primary'); ?> <a href="<?php echo zen_href_link(FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR, 'set_product_type=' . (isset($_GET['set_product_type']) && $_GET['set_product_type'] != '' ? (int)$_GET['set_product_type'] : '')); ?>" class="btn btn-default" role="button"><?php echo TEXT_CANCEL; ?></a>
                  </div>
                  <?php echo '</form>'; ?>
                </div>
              </div>
              <?php
              break;
            case 'delete_field' :
              ?>
              <div class="col-sm-10">
                <div class="row">
                  <?php
                  $availableFieldsArray = getAvailableFields($allFieldsArray, $fieldsToProductTypeArray);
                  $fieldsArray = [];
                  foreach ($availableFieldsArray as $availableField) {
                    $fieldsArray[] = [
                      'id' => $availableField['id'],
                      'text' => (!empty($availableField['description']) ? $availableField['description'] : $availableField['field_name'])
                    ];
                  }
                  ?>
                  <?php echo zen_draw_form('set_product_field_form', FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR, 'action=edit_field&set_product_type=' . $selectedProductTypeId, 'get', 'class="form-horizontal"'); ?>
                  <div class="form-group">
                    <?php echo zen_draw_label(TEXT_SELECT_FIELD, 'set_field', 'class="col-sm-3 control-label"'); ?>
                    <div class="col-sm-9">
                      <?php $selectedFieldId = (isset($_GET['set_field']) && $_GET['set_field'] != '' ? $_GET['set_field'] : $fieldsArray[0]['id']); ?>
                      <?php echo zen_draw_pull_down_menu('set_field', $fieldsArray, $_GET['set_field'], 'onchange="this.form.submit();" class="form-control"'); ?>
                    </div>
                    <?php echo zen_hide_session_id(); ?>
                    <?php echo zen_post_all_get_params(); ?>
                  </div>
                  <?php echo '</form>'; ?>
                  <?php if ($selectedFieldId != '') { ?>
                    <?php echo zen_draw_form('delete_field', FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR, 'action=delete_field_confirm', 'post', 'class="form-horizontal"'); ?>
                    <div class="row">
                      <div class="text-center alert alert-danger"><?php echo TEXT_DELETE_FIELD; ?></div>
                    </div>
                    <div class="form-group text-right">
                      <?php echo zen_draw_hidden_field('field_id', $selectedFieldId); ?>
                      <?php echo zen_html_button(IMAGE_DELETE, 'primary'); ?> <a href="<?php echo zen_href_link(FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR, 'set_product_type=' . (isset($_GET['set_product_type']) && $_GET['set_product_type'] != '' ? (int)$_GET['set_product_type'] : '')); ?>" class="btn btn-default" role="button"><?php echo TEXT_CANCEL; ?></a>
                    </div>
                    <?php echo '</form>'; ?>
                  <?php } ?>
                </div>
              </div>
              <?php
              break;
            case 'add_tab' :
              ?>
              <div class="col-sm-10">
                <div class="col-sm-5">
                  <h3>Existing Tabs</h3>
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Sort Order</th>
                        <th>Product Types</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $existingTabs = getAllTabs();
                      foreach ($existingTabs as $tab) {
                        ?>
                        <tr>
                          <td><?php echo $tab['tabName']; ?></td>
                          <td><?php echo $tab['sortOrder']; ?></td>
                          <td>
                            <?php
                            foreach ($tab['productType'] as $productType) {
                              echo $productType . ', ';
                            }
                            ?>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
                <div class="col-sm-7">
                  <h3>New Tab</h3>
                  <?php echo zen_draw_form('add_tab', FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR, 'action=insert_tab', 'post', 'class="form-horizontal"'); ?>
                  <div class="form-group">
                    <?php echo zen_draw_label(TEXT_TAB_TITLE, 'tab-name', 'class="col-sm-3 control-label"'); ?>
                    <div class="col-sm-9">
                      <?php for ($i = 0, $n = count($languages); $i < $n; $i++) { ?>
                        <div class="input-group">
                          <span class="input-group-addon">
                            <?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>
                          </span>
                          <?php echo zen_draw_input_field('tab_name[' . $languages[$i]['id'] . ']', '', 'class="form-control"', true); ?>
                        </div><br>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <?php echo zen_draw_label(TEXT_TAB_SORT_ORDER, 'sort_order', 'class="col-sm-3 control-label"'); ?>
                    <div class="col-sm-9">
                      <?php echo zen_draw_input_field('sort_order', '', 'class="form-control"'); ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <?php echo zen_draw_label(TEXT_TAB_PRODUCT_TYPE, 'product_type_id', 'class="col-sm-3 control-label"'); ?>
                    <div class="col-sm-9">
                      <?php
                      $productTypes = getProductTypes();
                      for ($i = 0, $n = sizeof($productTypes); $i < $n; $i++) {
                        ?>
                        <div class="checkbox">
                          <label>
                            <?php echo zen_draw_checkbox_field('product_type_id[' . $productTypes[$i]['type_id'] . ']', $productTypes[$i]['type_id']); ?>
                            <?php echo $productTypes[$i]['type_name']; ?>
                          </label>
                        </div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group text-right">
                    <?php echo zen_html_button(IMAGE_INSERT, 'primary'); ?> <a href="<?php echo zen_href_link(FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR); ?>" class="btn btn-default" role="button"><?php echo TEXT_CANCEL; ?></a>
                  </div>
                  <?php echo '</form>'; ?>
                </div>
              </div>
              <?php
              break;
            case 'tabs':
              ?>
              <div class="col-sm-10">
                <div class="row">
                  <?php echo zen_draw_form('tabs', FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR, 'action=save_tab', 'post', 'id="tabs" class="form-horizontal"'); ?>
                  <div id="tabs" class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th></th>
                          <th><?php echo TABLE_HEADING_TAB_NAME; ?></th>
                          <th><?php echo TABLE_HEADING_PRODUCT_TYPES; ?></th>
                          <th><?php echo TABLE_HEADING_ACTION; ?></th>
                        </tr>
                      </thead>
                      <tbody id="sortableTabRows">
                        <?php
                        $tabArray = getAllTabs();
                        $tabIsInUseQuery = "SELECT DISTINCT tab_id
                                            FROM " . TABLE_PRODUCT_FIELDS_TO_TYPE;
                        $tabIsInUse = $db->Execute($tabIsInUseQuery);
                        $tabIsInUseArray = [];
                        foreach ($tabIsInUse as $tab) {
                          $tabIsInUseArray[] = [$tab['tab_id']];
                        }
                        foreach ($tabArray as $i => $tab) {
                          ?>
                          <tr id="<?php echo $tab['id']; ?>" class="ui-state-default" role="button">
                            <td class="sortOrder" style="vertical-align: middle">
                              <i class="fa fa-arrows-v fa-lg"></i><?php echo $tab['sortOrder']; ?>
                              <?php echo zen_draw_hidden_field('tabSort[' . $i . '][sort_order]', $tab['sort_order'], 'class="sortOrderValue"'); ?>
                              <?php echo zen_draw_hidden_field('tabSort[' . $i . '][id]', $tab['id']); ?>
                            </td>
                            <td>
                              <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
                                <div class="row">
                                  <span>
                                    <?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>
                                  </span>
                                  <?php echo getTabName($tab['id'], $languages[$i]['id']); ?>
                                </div>
                              <?php } ?>
                            </td>
                            <td>
                              <ul>
                                <?php
                                $productTypes = getProductTypes();
                                for ($i = 0, $n = sizeof($productTypes); $i < $n; $i++) {
                                  if (in_array($productTypes[$i]['type_id'], $tab['productType'])) {
                                    ?>
                                    <li><?php echo $productTypes[$i]['type_name']; ?></li>
                                    <?php
                                  }
                                }
                                ?>
                              </ul>
                            </td>
                            <td class="text-right">
                              <?php echo zen_draw_hidden_field('tab_id', $tab['id']); ?>
                              <?php echo zen_html_button('<i class="fa fa-pencil fa-lg"></i>', 'primary', ' id="button-edit-tab-' . $tab['id'] . '" data-toggle="modal" data-target="#TabEditModal" title="' . IMAGE_EDIT . '" onclick="editTab(' . $tab['id'] . ');" ' . ($tab['core'] == '1' ? 'disabled' : '')); ?>
                              <?php echo zen_html_button('<i class="fa fa-trash fa-lg"></i>', 'warning', ' id="button-delete-tab-' . $tab['id'] . '" data-toggle="modal" data-target="#TabDeleteModal" title="' . IMAGE_DELETE . '" onclick="deleteTab(' . $tab['id'] . ');" ' . ($tab['core'] == '1' || in_array($tab['id'], $tabIsInUseArray) || $tab['productType'] != '' ? 'disabled' : '')); ?>
                              <?php echo zen_html_button('<i class="fa fa-info fa-lg"></i>', 'info', ' id="button-info-tab-' . $tab['id'] . '" data-toggle="modal" data-target="#TabInfoModal" title="' . ICON_INFO . '" onclick="tabInfo(' . $tab['id'] . ');"'); ?>
                            </td>
                          </tr>
                          <?php echo zen_draw_hidden_field($tab['id'] . '[id]', $tab['id']); ?>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                  <div class="row text-right">
                    <?php echo zen_html_button('<i class="fa fa-lg fa-plus"></i>', 'primary', 'title="' . IMAGE_INSERT . '"'); ?> <a href="<?php echo zen_href_link(FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR, zen_get_all_get_params(['action'])); ?>" class="btn btn-default" role="button"><?php echo IMAGE_BACK; ?></a>
                  </div>
                  <?php echo '</form>'; ?>
                </div>
              </div>
              <?php
              break;
            case 'delete_tab':
              ?>
              <div class="col-sm-10">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th><?php echo TABLE_HEADING_TAB_NAME; ?></th>
                        <th><?php echo TABLE_HEADING_ACTION; ?></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($availableTabsArray as $tab) { ?>
                        <?php echo zen_draw_form('delete_tab', FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR, 'action=delete_tab_confirm', 'post', 'class="form-horizontal"'); ?>
                        <tr>
                          <td><?php echo $tab['tabName']; ?></td>
                          <td class="text-right">
                            <?php echo zen_draw_hidden_field('tab_id', $tab['id']); ?>
                            <?php echo zen_html_button('<i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>', 'warning', 'title="' . IMAGE_DELETE . '" ' . ($tab['core'] == '1' || in_array($tab['id'], $tabIsInUseArray) || $tab['productType'] != '' ? 'disabled' : '')); ?>
                            <?php if ($tab['core'] == '1' || in_array($tab['id'], $tabIsInUseArray) || $tab['productType'] != '') { ?>
                              <?php echo zen_html_button('<i class="fa fa-info"></i>', 'info', 'onclick="getDeleteTabInfo(' . $tab['id'] . ')" data-toggle="modal" data-target="#DeleteTabInfoModal"', 'submit'); ?>
                            <?php } ?>
                          </td>
                        </tr>
                        <?php echo '</form>'; ?>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
                <div class="row text-right">
                  <a href="<?php echo zen_href_link(FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR); ?>" class="btn btn-default" role="button"><?php echo TEXT_CANCEL; ?></a>
                </div>
              </div>
              <?php
              break;
            case 'productTypes':
              $productTypeInfoArray = getProductTypeInfo($_GET['set_product_type']);
              ?>
              <div class="col-sm-10">
                <h4><?php echo TEXT_HEADING_EDIT_PRODUCT_TYPE; ?> :: <?php echo $productTypeInfoArray->type_name; ?></h4>
                <div class="row"><?php echo TEXT_EDIT_INTRO; ?></div>
                <div class="row">
                  <?php echo zen_draw_form('edit_product_type', FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR, 'action=save_product_type', 'post', 'class="form-horizontal"'); ?>
                  <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCT_TYPES_NAME, 'type_name', 'class="control-label col-sm-3"'); ?>
                    <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_input_field('type_name', $productTypeInfoArray->type_name, zen_set_field_length(TABLE_PRODUCT_TYPES, 'type_name') . ' class="form-control"'); ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCT_TYPES_IMAGE, 'default_image', 'class="control-label col-sm-3"'); ?>
                    <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_file_field('default_image', '', 'class="form-control"') . $productTypeInfoArray->default_image; ?>
                    </div>
                  </div>
                  <?php
                  $dir_info = zen_build_subdirectories_array(DIR_FS_CATALOG_IMAGES);
                  $default_directory = substr($productTypeInfoArray->default_image, 0, strpos($productTypeInfoArray->default_image, '/') + 1);
                  ?>
                  <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCTS_IMAGE_DIR, 'img_dir', 'class="control-label col-sm-3"'); ?>
                    <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_pull_down_menu('img_dir', $dir_info, $default_directory, 'class="form-control"'); ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9 col-md-6"><?php echo zen_info_image($productTypeInfoArray->default_image, $productTypeInfoArray->type_name); ?></div>
                  </div>
                  <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCT_TYPES_HANDLER, 'handler', 'class="control-label col-sm-3"'); ?>
                    <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_input_field('handler', $productTypeInfoArray->type_handler, zen_set_field_length(TABLE_PRODUCT_TYPES, 'type_handler') . ' class="form-control"'); ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <?php echo zen_draw_label(TEXT_PRODUCT_TYPES_ALLOW_ADD_CART, 'catalog_add_to_cart', 'class="control-label col-sm-3"'); ?>
                    <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_checkbox_field('catalog_add_to_cart', $productTypeInfoArray->allow_add_to_cart, ($productTypeInfoArray->allow_add_to_cart == 'Y'), 'class="form-control"'); ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <?php echo zen_draw_label(TEXT_MASTER_TYPE, 'master_type', 'class="control-label col-sm-3"'); ?>
                    <div class="col-sm-9 col-md-6">
                      <?php echo zen_draw_pull_down_menu('master_type', $productTypeArray, $productTypeInfoArray->type_master_type, 'class="form-control"'); ?>
                    </div>
                  </div>
                  <div class="form-group text-right">
                    <?php echo zen_html_button('<i class="fa fa-lg fa-save"></i>', 'primary', 'title="' . IMAGE_SAVE . '"'); ?> <a href="<?php echo zen_href_link(FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR, 'set_product_type=' . $productTypeInfoArray->type_id); ?>" class="btn btn-default" role="button"><?php echo IMAGE_BACK; ?></a>
                  </div>
                  <?php echo '</form>'; ?>
                </div>
              </div>
              <?php
              break;
            default:
              $productTypeInfoArray = getProductTypeInfo($selectedProductTypeId);
              ?>
              <div class="col-sm-10">
                <div class="row">
                  <?php echo zen_draw_form('set_product_type_form', FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR, '', 'get', 'class="form-horizontal"'); ?>
                  <div class="form-group">
                    <?php echo zen_draw_label(TEXT_SELECT_PRODUCT_TYPE, 'set_product_type', 'class="col-sm-3 control-label"'); ?>
                    <div class="col-sm-9">
                      <?php echo zen_draw_pull_down_menu('set_product_type', $productTypeArray, $selectedProductTypeId, 'onchange="this.form.submit();" class="form-control"'); ?>
                    </div>
                    <?php echo zen_hide_session_id(); ?>
                  </div>
                  <?php echo '</form>'; ?>
                </div>
                <div class="form-group">
                  <div class="col-sm-6"><label class="col-sm-6"><?php echo TEXT_PRODUCT_TYPES_NAME; ?></label><div class="col-sm-6"><?php echo $productTypeInfoArray->type_name; ?></div></div>
                  <div class="col-sm-6"><label class="col-sm-6"><?php echo TEXT_PRODUCT_TYPES_IMAGE; ?></label><div class="col-sm-6"><?php echo $productTypeInfoArray->default_image; ?></div></div>
                  <div class="col-sm-6"><label class="col-sm-6"><?php echo TEXT_PRODUCT_TYPES_HANDLER; ?></label><div class="col-sm-6"><?php echo $productTypeInfoArray->type_handler; ?></div></div>
                  <div class="col-sm-6"><label class="col-sm-6"><?php echo TEXT_PRODUCT_TYPES_ALLOW_ADD_CART; ?></label><div class="col-sm-6"><?php echo ($productTypeInfoArray->allow_add_to_cart == 'Y' ? TEXT_YES : TEXT_NO); ?></div></div>
                  <div class="col-sm-6"><label class="col-sm-6"><?php echo TEXT_MASTER_TYPE; ?></label><div class="col-sm-6"><?php echo zen_get_handler_from_type($productTypeInfoArray->type_master_type); ?></div></div>
                  <div class="col-sm-6"><label class="col-sm-6"><?php echo TEXT_DATE_ADDED; ?></label><div class="col-sm-6"><?php echo $productTypeInfoArray->date_added; ?></div></div>
                  <div class="col-sm-6"><label class="col-sm-6"><?php echo TEXT_LAST_MODIFIED; ?></label><div class="col-sm-6"><?php echo $productTypeInfoArray->last_modified; ?></div></div>
                  <div class="col-sm-6"><label class="col-sm-6"><?php echo TEXT_PRODUCTS; ?></label><div class="col-sm-6"><?php echo $productTypeInfoArray->products_count; ?></div></div>
                </div>
                <div class="row"><?php echo zen_draw_separator('pixel_black.gif', '100%', '1') ?></div>
                <div class="alert alert-info"><?php echo TEXT_INTRO_PRODUCT_TYPES; ?></div>
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th><h3><?php echo TEXT_AVAILABLE_FIELDS; ?></h3></th>
                        <th><h3><?php echo TEXT_AVAILABLE_TABS; ?></h3></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>
                          <div id="available_fields" class="connectedSortable">
                            <?php $availableFieldsArray = getAvailableFields($allFieldsArray, $fieldsToProductTypeArray) ?>
                            <?php for ($i = 0, $n = sizeof($availableFieldsArray); $i < $n; $i++) { ?>
                              <div id="<?php echo $availableFieldsArray[$i]; ?>" class="ui-state-default" role="button"><span><?php echo $availableFieldsArray[$i]; ?></span></div>
                            <?php } ?>
                          </div>
                        </td>
                        <td>

                          <?php echo zen_draw_form('fields', FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR, '&action=layout_save', 'post', 'class="form-horizontal"'); ?>
                          <?php
                          $availableTabsArray = getTabsInType($selectedProductTypeId);
                          for ($i = 0, $n = sizeof($availableTabsArray); $i < $n; $i++) {
                            ?>
                            <div class="row">
                              <h4><?php echo $availableTabsArray[$i]['tabName']; ?></h4>
                              <div class="row">
                                <div id="tab-<?php echo $availableTabsArray[$i]['id']; ?>" class="connectedSortable" name="<?php echo $availableTabsArray[$i]['id']; ?>">
                                  <?php
                                  $fields = getFieldsInTab($selectedProductTypeId, $availableTabsArray[$i]['id']);
                                  for ($j = 0, $m = sizeof($fields); $j < $m; $j++) {
                                    ?>
                                    <div id="<?php echo $fields[$j]['fieldName']; ?>" class="ui-state-default" role="button">
                                      <?php echo zen_draw_checkbox_field('tab[' . $fields[$j]['tabId'] . ']' . '[layout]' . '[' . $fields[$j]['fieldName'] . '][show_in_frontend]', '1', ($fields[$j]['showInFrontend'] == 1 ? true : false), 'class="ui-state-enabled"'); ?>&nbsp;|&nbsp;
                                      <span><?php echo $fields[$j]['fieldName']; ?></span>
                                      <?php echo zen_draw_hidden_field('tab[' . $fields[$j]['tabId'] . ']' . '[layout]' . '[' . $fields[$j]['fieldName'] . '][field_name]', $fields[$j]['fieldName']); ?>
                                      <?php echo zen_draw_hidden_field('tab[' . $fields[$j]['tabId'] . ']' . '[layout]' . '[' . $fields[$j]['fieldName'] . '][tab_id]', $fields[$j]['tabId']); ?>
                                    </div>
                                  <?php } ?>
                                </div>
                              </div>
                            </div>
                          <?php } ?>
                          <div class="col-sm-12 text-right">
                            <?php echo zen_draw_hidden_field('product_type', $selectedProductTypeId); ?>
                            <?php echo zen_html_button(IMAGE_SAVE, 'primary', '', 'submit'); ?> <a href="<?php echo zen_href_link(FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR); ?>" class="btn btn-default" role="button">Reset</a>
                          </div>
                          <?php echo '</form>'; ?>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <!-- body_eof //-->
                </div>
              </div>
          <?php } ?>
        </div>
        <?php require 'cittinsFooter.php'; ?>
      </div>
    </div>
    <?php
    require DIR_WS_INCLUDES . 'footer.php';
    /* Autoload Product modals */
    foreach (glob(DIR_WS_MODALS . 'cittins/productLayoutEditor/*.php') as $filename) {
      include $filename;
    }
    /* Message Stack modal */
    require_once DIR_WS_MODALS . 'messageStackModal.php';
    ?>
  </body>
</html>
<?php
require DIR_WS_INCLUDES . 'application_bottom.php';
