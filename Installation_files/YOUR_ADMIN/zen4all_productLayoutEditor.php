<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require('includes/application_top.php');
$action = (isset($_GET['action']) ? $_GET['action'] : '');

if (zen_not_null($action)) {
  switch ($action) {
    case 'layout_save':
      $data = new objectInfo($_POST);
      $action = '';
      $deleteQuery = "DELETE FROM " . TABLE_PRODUCT_TYPE_FIELDS_TO_TYPE . "
                      WHERE product_type_id = " . (int)$data->product_type_id;
      $db->Execute($deleteQuery);
      foreach ($data->tab as $value) {
        $sortOrder = 1;
        foreach ($value['layout'] as $layout) {
          $insertQuery = "INSERT INTO " . TABLE_PRODUCT_TYPE_FIELDS_TO_TYPE . " (product_type_id, field_id, sort_order, tab_id, show_in_frontend)
                          VALUES ('" . (int)$data->product_type_id . "',
                                  '" . (int)$layout['field_id'] . "',
                                  '" . (int)$sortOrder . "',
                                  '" . (int)$layout['tab_id'] . "',
                                  '" . (int)$layout['show_in_frontend'] . "')";
          $db->Execute($insertQuery);
          $sortOrder++;
        }
      }
      zen_redirect(zen_href_link(FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'set_product_type=' . (int)$data->product_type_id));
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
                          FROM " . TABLE_PRODUCT_TYPE_FIELDS . "
                          WHERE name = '" . $data->name . "'";
      $checkField = $db->Execute($checkFieldQuery);
      if ($checkField->RecordCount > 0) {
        // return with error
        $messageStack;
        zen_redirect(zen_href_link(FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, (isset($selectedProductTypeId) && $selectedProductTypeId != '' ? 'set_product_type=' . $selectedProductTypeId : '')));
      } else {
        $sqlDataArray = [
          'description' => $data->description,
          'default_value' => $data->default_value,
          'select_value_id' => $selectValueId,
          'select_value_text' => $selectValueTexts,
          'length' => (int)$data->length,
          'core' => (int)$data->core,
          'configuration_key' => $data->configuration_key,
          'label_define' => $data->label_define
        ];
        if ($action == 'insert_field') {
          $insertSqlData = [
            'name' => $data->name,
            'type' => (int)$data->type,
            'language_string' => (int)$data->language_string,
          ];
          $sqlArray = array_merge($sqlDataArray, $insertSqlData);
          zen_db_perform(TABLE_PRODUCT_TYPE_FIELDS, $sqlArray);

          $typeSqlInformation = setSqlTypeInformation($data->name, $data->type, $data->length, $data->default_value);

          if ($data->language_string == '0') {
            $db->Execute("ALTER TABLE " . TABLE_PRODUCTS_EXTRA . " ADD " . $typeSqlInformation);
          } elseif ($data->language_string == '1') {
            $db->Execute("ALTER TABLE " . TABLE_PRODUCTS_DESCRIPTION_EXTRA . " ADD " . $typeSqlInformation);
          }
        } elseif ($action == 'save_field') {
          zen_db_perform(TABLE_PRODUCT_TYPE_FIELDS, $sqlDataArray, 'update', "id = '" . (int)$data->id . "'");

          $db->Execute("ALTER TABLE " . TABLE_PRODUCTS_EXTRA . " CHANGE " . $fieldName->fields['name'] . " " . $fieldName->fields['name'] . " VARCHAR(" . (int)$data->length . ")");
        }
      }
      $action = '';
      zen_redirect(zen_href_link(FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, (isset($selectedProductTypeId) && $selectedProductTypeId != '' ? 'set_product_type=' . $selectedProductTypeId : '')));
      break;
    case 'delete_field_confirm' :

      $action = '';
      $data = new objectInfo($_POST);
      $fieldName = $db->Execute("SELECT name
                                 FROM " . TABLE_PRODUCT_TYPE_FIELDS . "
                                 WHERE id = " . (int)$data->field_id);
      $db->Execute("DELETE FROM " . TABLE_PRODUCT_TYPE_FIELDS . "
                    WHERE id = " . (int)$data->field_id);

      $db->Execute("ALTER TABLE " . TABLE_PRODUCTS_EXTRA . " DROP " . $fieldName->fields['name']);
      zen_redirect(zen_href_link(FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, (isset($selectedProductTypeId) && $selectedProductTypeId != '' ? 'set_product_type=' . $selectedProductTypeId : '')));
      break;
    case'insert_tab':
      $data = new objectInfo($_POST);
      $checkDefineQuery = "SELECT define
                           FROM " . TABLE_PRODUCT_TABS . "
                           WHERE define = '" . $data->define . "'";
      $checkDefine = $db->Execute($checkDefineQuery);
      if ($checkDefine->RecordCount() > 0) {
        ?>
        <?php
        zen_redirect(zen_href_link(FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'tab-define=' . $data->define));
      } else {
        $db->Execute("INSERT INTO " . TABLE_PRODUCT_TABS . " (define, sort_order)
                      VALUES('" . $data->define . "',
                             '" . $data->sort_order . "')");
        $action = '';
        zen_redirect(zen_href_link(FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR));
      }
      break;
    case'save_tab':
      $data = new objectInfo($_POST['tab']);
      $sortOrder = 1;
      foreach ($data as $tabId) {
        $updateSortOrderQuery = "UPDATE " . TABLE_PRODUCT_TABS . "
                                 SET sort_order = " . (int)$sortOrder . "
                                 WHERE id = " . (int)$tabId;
        $db->Execute($updateSortOrderQuery);
        $sortOrder++;
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
$productTypesQuery = "SELECT type_id, type_name FROM " . TABLE_PRODUCT_TYPES;
$productTypes = $db->Execute($productTypesQuery);

$productTypeArray = [];
foreach ($productTypes as $productType) {
  $productTypeArray[] = [
    'id' => $productType['type_id'],
    'text' => $productType['type_name']
  ];
}
$selectedProductTypeId = (isset($_GET['set_product_type']) ? (int)$_GET['set_product_type'] : '1');

$fieldsToAllProductTypeQuery = "SELECT DISTINCT field_id
                                FROM " . TABLE_PRODUCT_TYPE_FIELDS_TO_TYPE . "
                                ORDER BY field_id";
$fieldsToAllProductType = $db->Execute($fieldsToAllProductTypeQuery);

$fieldsToAllProductTypeArray = [];
foreach ($fieldsToAllProductType as $fieldToAllProductType) {
  $fieldsToAllProductTypeArray[] = ['fieldId' => $fieldToAllProductType['field_id']];
}

$fieldsToProductTypeQuery = "SELECT *
                             FROM " . TABLE_PRODUCT_TYPE_FIELDS_TO_TYPE . "
                             WHERE product_type_id = " . (int)$selectedProductTypeId . "
                             ORDER BY tab_id, sort_order";
$fieldsToProductType = $db->Execute($fieldsToProductTypeQuery);

$fieldsToProductTypeArray = [];
foreach ($fieldsToProductType as $fieldToProductType) {
  $fieldsToProductTypeArray[] = [
    'productTypeId' => $fieldToProductType['product_type_id'],
    'fieldId' => $fieldToProductType['field_id'],
    'sortOrder' => $fieldToProductType['sort_order'],
    'tabId' => $fieldToProductType['tab_id'],
    'showInFrontend' => $fieldToProductType['show_in_frontend']];
}

$allFieldsQuery = "SELECT *
                   FROM " . TABLE_PRODUCT_TYPE_FIELDS . "
                   ORDER BY description";
$allFields = $db->Execute($allFieldsQuery);

$allFieldsArray = [];
foreach ($allFields as $field) {
  $allFieldsArray[] = [
    'id' => $field['id'],
    'name' => $field['name'],
    'type' => $field['type'],
    'description' => $field['description'],
    'value' => $field['default_value'],
    'selectValues' => $field['select_values'],
    'fieldLength' => $field['length'],
    'core' => $field['core'],
    'languageString' => $field['language_string']];
}

$fieldTypes = $db->Execute("SELECT *
                            FROM " . TABLE_PRODUCT_TYPE_FIELD_TYPES);

$fieldTypesArray = [];
$fieldTypesArray[0] = [
  'id' => '0',
  'text' => PLEASE_SELECT];

foreach ($fieldTypes as $type) {
  $fieldTypesArray[] = ['id' => $type['id'], 'text' => $type['text']];
}
?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
    <?php
    $extraCss = [];
    $extraCss[0] = ['location' => 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'];
    $extraCss[1] = ['location' => 'includes/css/z4a_productLayoutEditor.css'];
    ?>
    <?php
    require('includes/admin_html_head.php');
    ?>
  <body onLoad="init()">
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <!-- body //-->
    <?php if ($action == '') { ?>
      <script>
        $(function () {
            $('#available_fields, [id^=tab-]').sortable({
                connectWith: '.connectedSortable',
                placeholder: 'ui-state-highlight',
            }).disableSelection();
            $('#tabs').sortable({
                placeholder: 'ui-state-highlight',
                items: 'div:not(.ui-state-disabled)'
            }).disableSelection();
            $('#tabs div').disableSelection();
            $('[id^=tab-]').sortable({
                receive: function (event, ui) {
                    var dropElemTxt = $(ui.item).find('span').text();
                    var dropElemId = $(ui.item).attr('id');
                    var dropTabId = $(ui.item).parent().attr('name');
                    var replacement = '';
                    replacement += '<div id="' + dropElemId + '" class="ui-state-default" role="button">\n';
                    replacement += '<input type="checkbox" name="tab[' + dropTabId + '][layout][' + dropElemId + '][show_in_frontend]" value="1" checked="checked">&nbsp;|&nbsp;\n';
                    replacement += '<span>' + dropElemTxt + '</span>\n';
                    replacement += '<input type="hidden" name="tab[' + dropTabId + '][layout][' + dropElemId + '][field_id]" value="' + dropElemId + '">\n';
                    replacement += '<input type="hidden" name="tab[' + dropTabId + '][layout][' + dropElemId + '][tab_id]" value="' + dropTabId + '">\n';
                    replacement += '</div>\n';
                    $(ui.item).replaceWith(replacement);
                }
            });
            $('#available_fields').sortable({
                receive: function (event, ui) {
                    var dropElemTxt = $(ui.item).find('span').text();
                    var dropElemId = $(ui.item).attr('id');
                    var replacement = '';
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
          <h1><?php echo HEADING_TITLE . (isset($action) && $action != '' ? ' <i class="fa fa-angle-double-right" aria-hidden="true"></i> ' : '') . $actionTitle; ?></h1>
        </div>
        <div class="panel-body">
          <div id="actionPanel" class="panel col-sm-2">
            <h4><?php echo NAV_TITLE_FIELDS; ?></h4>
            <a href="<?php echo zen_href_link(FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'action=add_field' . (isset($selectedProductTypeId) && $selectedProductTypeId != '' ? '&set_product_type=' . $selectedProductTypeId : '')) ?>" class="btn btn-primary btn-block" role="button"><?php echo BUTTON_ADD_FIELD; ?></a>
            <a href="<?php echo zen_href_link(FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'action=edit_field' . (isset($selectedProductTypeId) && $selectedProductTypeId != '' ? '&set_product_type=' . $selectedProductTypeId : '')) ?>" class="btn btn-primary btn-block" role="button"><?php echo BUTTON_EDIT_FIELD; ?></a>
            <a href="<?php echo zen_href_link(FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'action=delete_field' . (isset($selectedProductTypeId) && $selectedProductTypeId != '' ? '&set_product_type=' . $selectedProductTypeId : '')) ?>" class="btn btn-primary btn-block" role="button"><?php echo BUTTON_DELETE_FIELD; ?></a>
            <hr>
            <h4><?php echo NAV_TITLE_TABS; ?></h4>
            <a href="<?php echo zen_href_link(FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'action=add_tab') ?>" class="btn btn-primary btn-block" role="button"><?php echo BUTTON_ADD_TAB; ?></a>
            <a href="<?php echo zen_href_link(FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'action=edit_tab') ?>" class="btn btn-primary btn-block" role="button"><?php echo BUTTON_EDIT_TAB; ?></a>
            <a href="<?php echo zen_href_link(FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'action=delete_tab') ?>" class="btn btn-primary btn-block" role="button"><?php echo BUTTON_DELETE_TAB; ?></a>
            <hr>
            <h4><?php echo NAV_TITLE_PRODUCT_TYPES; ?></h4>
            <a href="#" class="btn btn-primary btn-block" role="button"><?php echo BUTTON_ADD_PRODUCT_TYPE; ?></a>
            <a href="<?php echo zen_href_link(FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'action=edit_product_type' . '&type_id=' . $selectedProductTypeId) ?>" class="btn btn-primary btn-block" role="button"><?php echo BUTTON_EDIT_PRODUCT_TYPE; ?></a>
            <a href="#" class="btn btn-primary btn-block" role="button"><?php echo BUTTON_DELETE_PRODUCT_TYPE; ?></a>
          </div>
          <?php
          switch ($action) {
            case 'add_field':
              ?>
              <script>
                var row = 3;
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
                    <?php echo zen_draw_form('add_field', FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'set_product_type=' . (isset($_GET['set_product_type']) && $_GET['set_product_type'] != '' ? (int)$_GET['set_product_type'] . '&' : '') . 'action=insert_field', 'post', 'class="form-horizontal"'); ?>
                  <div id="field_name" class="form-group">
                      <?php echo zen_draw_label(TEXT_FIELD_NAME, 'name', 'class="col-sm-3 control-label"'); ?>
                    <i class="fa fa-lg fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo TEXT_INFO_FIELD_NAME; ?>"></i>
                    <div class="col-sm-9 col-md-6">
                        <?php echo zen_draw_input_field('name', '', 'placeholder="field_name" class="form-control"', true); ?>
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
                    <div class="col-sm-12 text-right"><button type="button" id="add_value" class="btn btn-info"><i class="fa fa-lg fa-plus"></i></button></div>
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
                    <button type="submit" class="btn btn-primary"><?php echo IMAGE_INSERT; ?></button> <a href="<?php echo zen_href_link(FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'set_product_type=' . (isset($_GET['set_product_type']) && $_GET['set_product_type'] != '' ? (int)$_GET['set_product_type'] : '')); ?>" class="btn btn-default" role="button"><?php echo TEXT_CANCEL; ?></a>
                  </div>
                  <?php echo '</form>'; ?>
                </div>
              </div>
              <?php
              break;
            case 'edit_field':
              ?>
              <div class="col-sm-10">
                <div class="row">
                    <?php
                    $availableFieldsArray = getAvailableFields($allFieldsArray, $fieldsToAllProductTypeArray);
                    $fieldsArray = [];
                    foreach ($availableFieldsArray as $availableField) {
                      $fieldsArray[] = [
                        'id' => $availableField['id'],
                        'text' => (!empty($availableField['description']) ? $availableField['description'] : $availableField['name'])];
                    }
                    ?>
                    <?php echo zen_draw_form('set_product_field_form', FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'action=edit_field&set_product_type=' . $selectedProductTypeId, 'get', 'class="form-horizontal"'); ?>
                  <div class="form-group">
                      <?php echo zen_draw_label(TEXT_SELECT_FIELD, 'set_field', 'class="col-sm-3 control-label"'); ?>
                    <div class="col-sm-9">
                        <?php $selectedFieldId = (isset($_GET['set_field']) && $_GET['set_field'] != '' ? $_GET['set_field'] : $fieldsArray[0]['id']); ?>
                        <?php echo zen_draw_pull_down_menu('set_field', $fieldsArray, $selectedFieldId, 'onchange="this.form.submit();" class="form-control"'); ?>
                    </div>
                    <?php echo zen_hide_session_id(); ?>
                    <?php echo zen_post_all_get_params(); ?>
                  </div>
                  <?php echo '</form>'; ?>
                </div>
                <?php
                $selectedFieldQuery = "SELECT *
                                       FROM " . TABLE_PRODUCT_TYPE_FIELDS . "
                                       WHERE id = " . (int)$selectedFieldId;
                $selectedField = $db->Execute($selectedFieldQuery);
                ?>
                <div class="row">
                    <?php echo zen_draw_form('edit_field', FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'set_product_type=' . (isset($_GET['set_product_type']) && $_GET['set_product_type'] != '' ? (int)$_GET['set_product_type'] . '&' : '') . 'action=save_field', 'post', 'class="form-horizontal"'); ?>
                  <div id="field_name" class="form-group">
                      <?php echo zen_draw_label(TEXT_FIELD_NAME, 'name', 'class="col-sm-3 control-label"'); ?>
                    <div class="col-sm-9">
                        <?php echo zen_draw_input_field('name', $selectedField->fields['name'], 'class="form-control" disabled'); ?>
                    </div>
                  </div>
                  <div id="field_description" class="form-group">
                      <?php echo zen_draw_label(TEXT_FIELD_DESCRIPTION, 'description', 'class="col-sm-3 control-label"'); ?>
                    <div class="col-sm-9">
                        <?php echo zen_draw_input_field('description', $selectedField->fields['description'], 'class="form-control"'); ?>
                    </div>
                  </div>
                  <div id="label_define" class="form-group">
                      <?php echo zen_draw_label(TEXT_FIELD_LABEL_DEFINE, 'label_define', 'class="col-sm-3 control-label"'); ?>
                    <div class="col-sm-9">
                        <?php echo zen_draw_input_field('label_define', $selectedField->fields['label_define'], 'placeholder="LABEL_DEFINE" class="form-control"'); ?>
                      <i class="fa fa-lg fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo TEXT_INFO_FIELD_LABEL_DEFINE; ?>"></i>
                    </div>
                  </div>
                  <div id="field_type" class="form-group">
                      <?php echo zen_draw_label(TEXT_FIELD_TYPE, 'type', 'class="col-sm-3 control-label"'); ?>
                    <div class="col-sm-9">
                        <?php echo zen_draw_pull_down_menu('type', $fieldTypesArray, $selectedField->fields['type'], 'class="form-control" disabled'); ?>
                    </div>
                  </div>
                  <div id="default_value" class="form-group">
                      <?php echo zen_draw_label(TEXT_FIELD_DEFAULT_VALUE, 'default_value', 'class="col-sm-3 control-label"'); ?>
                    <div class="col-sm-9">
                        <?php echo zen_draw_input_field('default_value', $selectedField->fields['default_value'], 'class="form-control"'); ?>
                    </div>
                  </div>
                  <div class="form-group">
                      <?php echo zen_draw_label(TEXT_FIELD_SELECT_VALUES, 'select_values', 'class="col-sm-3 control-label"'); ?>
                    <div class="col-sm-9">
                        <?php echo zen_draw_input_field('select_values', $selectedField->fields['select_values'], 'class="form-control"'); ?>
                    </div>
                  </div>
                  <div id="field_length" class="form-group">
                      <?php echo zen_draw_label(TEXT_FIELD_LENGTH, 'length', 'class="col-sm-3 control-label"'); ?>
                    <div class="col-sm-9">
                        <?php echo zen_draw_input_field('length', $selectedField->fields['length'], 'class="form-control"'); ?>
                    </div>
                  </div>
                  <div id="language_string" class="form-group">
                      <?php echo zen_draw_label(TEXT_FIELD_LANGUAGE_STRING, 'language_string', 'class="col-sm-3 control-label"'); ?>
                    <div class="col-sm-9">
                      <div class="radio-inline">
                        <label><?php echo zen_draw_radio_field('language_string', '0', ($selectedField->fields['language_string'] == 0 ? true : ''), '', 'disabled') . TEXT_NO; ?></label></div>
                      <div class="radio-inline">
                        <label><?php echo zen_draw_radio_field('language_string', '1', ($selectedField->fields['language_string'] == 1 ? true : ''), '', 'disabled') . TEXT_YES; ?></label></div>
                    </div>
                  </div>
                  <div class="form-group text-right">
                      <?php echo zen_draw_hidden_field('id', $selectedFieldId); ?>
                    <button type="submit" class="btn btn-primary"><?php echo IMAGE_SAVE; ?></button> <a href="<?php echo zen_href_link(FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'set_product_type=' . (isset($_GET['set_product_type']) && $_GET['set_product_type'] != '' ? (int)$_GET['set_product_type'] : '')); ?>" class="btn btn-default" role="button"><?php echo TEXT_CANCEL; ?></a>
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
                        'text' => (!empty($availableField['description']) ? $availableField['description'] : $availableField['name'])];
                    }
                    ?>
                    <?php echo zen_draw_form('set_product_field_form', FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'action=edit_field&set_product_type=' . $selectedProductTypeId, 'get', 'class="form-horizontal"'); ?>
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
                    <?php echo zen_draw_form('delete_field', FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'action=delete_field_confirm', 'post', 'class="form-horizontal"'); ?>
                    <div class="row">
                      <div class="text-center alert alert-danger"><?php echo TEXT_DELETE_FIELD; ?></div>
                    </div>
                    <div class="form-group text-right">
                        <?php echo zen_draw_hidden_field('field_id', $selectedFieldId); ?>
                      <button type="submit" class="btn btn-primary"><?php echo IMAGE_DELETE; ?></button> <a href="<?php echo zen_href_link(FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'set_product_type=' . (isset($_GET['set_product_type']) && $_GET['set_product_type'] != '' ? (int)$_GET['set_product_type'] : '')); ?>" class="btn btn-default" role="button"><?php echo TEXT_CANCEL; ?></a>
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
                <div class="row">
                    <?php echo zen_draw_form('add_tab', FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'action=insert_tab', 'post', 'class="form-horizontal"'); ?>
                  <div class="form-group">
                      <?php echo zen_draw_label(TEXT_TAB_DEFINE, 'define', 'class="col-sm-3 control-label"'); ?>
                    <div class="col-sm-9">
                        <?php echo zen_draw_input_field('define', (isset($_GET['tab-define']) && $_GET['tab-define'] != '' ? $_GET['tab-define'] : ''), 'class="form-control" placeholder="TAB_XXXXXX"', true); ?>
                    </div>
                  </div>
                  <div class="form-group">
                      <?php echo zen_draw_label(TEXT_TAB_SORT_ORDER, 'sort_order', 'class="col-sm-3 control-label"'); ?>
                    <div class="col-sm-9">
                        <?php echo zen_draw_input_field('sort_order', '', 'class="form-control"'); ?>
                    </div>
                  </div>
                  <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary"><?php echo IMAGE_INSERT; ?></button> <a href="<?php echo zen_href_link(FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR); ?>" class="btn btn-default" role="button"><?php echo TEXT_CANCEL; ?></a>
                  </div>
                  <?php echo '</form>'; ?>
                </div>
              </div>
              <?php
              break;
            case 'edit_tab':
              ?>
              <div class="col-sm-10">
                <div class="row">
                    <?php echo zen_draw_form('edit_tab', FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'action=save_tab', 'post', 'class="form-horizontal"'); ?>
                  <div class="col-sm-3">
                    <div id="tabs" class="connectedSortable">
                        <?php
                        $tabArray = getTabs();
                        foreach ($tabArray as $tab) {
                          ?>
                        <div id="<?php echo $tab['id']; ?>" class="ui-state-default <?php echo ($tab['core'] == 1 ? 'ui-state-disabled' : ''); ?>" role="button">
                          <i class="fa fa-arrows-v"></i>&nbsp;&nbsp;<?php echo (defined($tab['define']) && constant($tab['define']) != '' ? constant($tab['define']) : $tab['define']); ?>
                          <?php echo zen_draw_hidden_field('tab[' . $tab['id'] . ']', $tab['id']); ?>
                        </div>
                        <?php
                      }
                      ?>
                    </div>
                  </div>
                  <div class="row text-right">
                    <button type="submit" class="btn btn-primary"><?php echo IMAGE_SAVE; ?></button> <a href="<?php echo zen_href_link(FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR); ?>" class="btn btn-default" role="button"><?php echo TEXT_CANCEL; ?></a>
                  </div>
                  <?php echo '</form>'; ?>
                </div>
              </div>
              <?php
              break;
            case 'delete_tab':
              ?>
              <div class="col-sm-10">
                <div class="row">
                    <?php echo zen_draw_form('delete_tab', FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'action=delete_tab_confirm', 'post', 'class="form-horizontal"'); ?>
                  <div class="row text-right">
                    <button type="submit" class="btn btn-primary"><?php echo IMAGE_SAVE; ?></button> <a href="<?php echo zen_href_link(FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR); ?>" class="btn btn-default" role="button"><?php echo TEXT_CANCEL; ?></a>
                  </div>
                  <?php echo '</form>'; ?>
                </div>
              </div>
              <?php
              break;
            case 'add_product_type':
              ?>
              <div class="col-sm-10">
                <div class="row">
                    <?php echo zen_draw_form('add_product_type', FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'action=insert_product_type', 'post', 'class="form-horizontal"'); ?>
                    <?php echo '</form>'; ?>
                </div>
              </div>
              <?php
              break;
            case 'edit_product_type':
              $productTypeInfoArray = getProductTypeInfo($_GET['type_id']);
              ?>
              <div class="col-sm-10">
                <h4><?php echo TEXT_HEADING_EDIT_PRODUCT_TYPE; ?> :: <?php echo $productTypeInfoArray->type_name; ?></h4>
                <div class="row"><?php echo TEXT_EDIT_INTRO; ?></div>
                <div class="row">
                    <?php echo zen_draw_form('edit_product_type', FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'action=save_product_type', 'post', 'class="form-horizontal"'); ?>
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
                        <?php echo zen_draw_checkbox_field('catalog_add_to_cart', $productTypeInfoArray->allow_add_to_cart, ($productTypeInfoArray->allow_add_to_cart == 'Y' ? true : false), 'class="form-control"'); ?>
                    </div>
                  </div>
                  <div class="form-group">
                      <?php echo zen_draw_label(TEXT_MASTER_TYPE, 'master_type', 'class="control-label col-sm-3"'); ?>
                    <div class="col-sm-9 col-md-6">
                        <?php echo zen_draw_pull_down_menu('master_type', $productTypeArray, $productTypeInfoArray->type_master_type, 'class="form-control"'); ?>
                    </div>
                  </div>
                  <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary"><?php echo IMAGE_SAVE; ?></button> <a href="<?php echo zen_href_link(FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'set_product_type=' . $productTypeInfoArray->type_id); ?>" class="btn btn-default" role="button"><?php echo IMAGE_CANCEL; ?></a>
                  </div>
                  <?php echo '</form>'; ?>
                </div>
              </div>
              <?php
              break;
            case 'delete_product_type':
              ?>
              <div class="col-sm-10">
                <div class="row">
                    <?php echo zen_draw_form('delete_product_type', FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, 'action=delete_product_type_confirm', 'post', 'class="form-horizontal"'); ?>
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
                    <?php echo zen_draw_form('set_product_type_form', FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, '', 'get', 'class="form-horizontal"'); ?>
                  <div class="form-group">
                      <?php echo zen_draw_label(TEXT_SELECT_PRODUCT_TYPE, 'set_product_type', 'class="col-sm-3 control-label"'); ?>
                    <div class="col-sm-9">
                        <?php echo zen_draw_pull_down_menu('set_product_type', $productTypeArray, $selectedProductTypeId, 'onchange="this.form.submit();" class="form-control"'); ?>
                    </div>
                    <?php echo zen_hide_session_id(); ?>
                  </div>
                  <?php echo '</form>'; ?>
                </div>
                <div class="row">
                  <table class="table table-condensed">
                    <thead>
                      <tr>
                        <th><?php echo TEXT_PRODUCT_TYPES_NAME; ?></th>
                        <th><?php echo TEXT_PRODUCT_TYPES_IMAGE; ?></th>
                        <th><?php echo TEXT_PRODUCT_TYPES_HANDLER; ?></th>
                        <th><?php echo TEXT_PRODUCT_TYPES_ALLOW_ADD_CART; ?></th>
                        <th><?php echo TEXT_MASTER_TYPE; ?></th>
                        <th><?php echo TEXT_DATE_ADDED; ?></th>
                        <th><?php echo TEXT_LAST_MODIFIED; ?></th>
                        <th><?php echo TEXT_PRODUCTS; ?></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td><?php echo $productTypeInfoArray->type_name; ?></td>
                        <td><?php echo $productTypeInfoArray->default_image; ?></td>
                        <td><?php echo $productTypeInfoArray->type_handler; ?></td>
                        <td><?php echo ($productTypeInfoArray->allow_add_to_cart == 'Y' ? TEXT_YES : TEXT_NO); ?></td>
                        <td><?php echo zen_get_handler_from_type($productTypeInfoArray->type_master_type); ?></td>
                        <td><?php echo $productTypeInfoArray->date_added; ?></td>
                        <td><?php echo $productTypeInfoArray->last_modified; ?></td>
                        <td><?php echo $productTypeInfoArray->products_count; ?></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="row"><?php echo zen_draw_separator('pixel_black.gif', '100%', '1') ?></div>
                <div class="alert alert-info"><?php echo TEXT_INTRO_PRODUCT_TYPES; ?></div>
                <div class="row">
                  <div class="col-sm-3">
                    <h4><?php echo TEXT_AVAILABLE_FIELDS; ?></h4>
                    <div class="row">
                      <div id="available_fields" class="connectedSortable">
                          <?php $availableFieldsArray = getAvailableFields($allFieldsArray, $fieldsToProductTypeArray) ?>
                          <?php for ($i = 0, $n = sizeof($availableFieldsArray); $i < $n; $i++) {
                            ?>
                          <div id="<?php echo $availableFieldsArray[$i]['id']; ?>" class="ui-state-default" role="button"><span><?php echo (!empty($availableFieldsArray[$i]['description']) ? $availableFieldsArray[$i]['description'] : $availableFieldsArray[$i]['name']); ?></span></div>
                          <?php
                        }
                        ?>
                      </div>
                    </div>
                  </div>
                  <div>
                    <h4><?php echo TEXT_AVAILABLE_TABS; ?></h4>
                    <?php echo zen_draw_form('fields', FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, '&action=layout_save', 'post', 'class="form-horizontal"'); ?>
                    <?php
                    $availableTabsArray = getTabs();
                    for ($i = 0, $n = sizeof($availableTabsArray); $i < $n; $i++) {
                      ?>
                      <div class="col-sm-3">
                        <h3><?php echo ((defined($availableTabsArray[$i]['define']) && constant($availableTabsArray[$i]['define']) != '') ? constant($availableTabsArray[$i]['define']) : $availableTabsArray[$i]['define']); ?></h3>
                        <div class="row">
                          <div>show in front-end | drag</div>
                          <div id="tab-<?php echo $availableTabsArray[$i]['id']; ?>" class="connectedSortable" name="<?php echo $availableTabsArray[$i]['id']; ?>">
                              <?php
                              $fields = getFieldsInTab($selectedProductTypeId, $availableTabsArray[$i]['id']);
                              for ($j = 0, $m = sizeof($fields); $j < $m; $j++) {
                                $fieldIsCore = fieldIsCore($fields[$j]['fieldId']);
                                ?>
                              <div id="<?php echo $fields[$j]['fieldId']; ?>" class="ui-state-default" role="button">
                                <?php echo zen_draw_checkbox_field('tab[' . $fields[$j]['tabId'] . ']' . '[layout]' . '[' . $fields[$j]['fieldId'] . '][show_in_frontend]', '1', ($fields[$j]['showInFrontend'] == 1 ? true : false), 'class="ui-state-enabled"'); ?>&nbsp;|&nbsp;
                                <span><?php echo getFieldName($fields[$j]['fieldId']); ?></span>
                                <?php echo zen_draw_hidden_field('tab[' . $fields[$j]['tabId'] . ']' . '[layout]' . '[' . $fields[$j]['fieldId'] . '][field_id]', $fields[$j]['fieldId']); ?>
                                <?php echo zen_draw_hidden_field('tab[' . $fields[$j]['tabId'] . ']' . '[layout]' . '[' . $fields[$j]['fieldId'] . '][tab_id]', $fields[$j]['tabId']); ?>
                              </div>
                            <?php } ?>
                          </div>
                        </div>
                      </div>
                      <?php
                    }
                    ?>
                  </div>
                  <div class="col-sm-12 text-right">
                      <?php echo zen_draw_hidden_field('product_type_id', $selectedProductTypeId); ?>
                    <button type="submit" class="btn btn-primary"><?php echo IMAGE_SAVE; ?></button> <a href="<?php echo zen_href_link(FILENAME_Z4A_PRODUCT_LAYOUT_EDITOR, '', $request_type); ?>" class="btn btn-default" role="button">Reset</a>
                  </div>
                  <?php echo '</form>'; ?>
                  <!-- body_eof //-->
                </div>
              </div>
            <?php
          }
          ?>
        </div>
      </div>
    </div>
    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
  </body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>