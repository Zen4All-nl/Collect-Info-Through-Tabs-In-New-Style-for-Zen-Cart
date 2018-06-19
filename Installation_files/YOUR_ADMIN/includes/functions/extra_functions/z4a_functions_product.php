<?php

/*
 * @package admin
 * @copyright Copyright 2008-2017 Zen4All
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: functionsCollectProductInfoExtra.php Zen4All $
 */

/**
 * search directories for the needed files
 * @param string $dir
 * @param string $prefix
 * @return array <p>directories and files</p>
*/
function recursiveDirList($dir, $prefix = '') {
  $dir = rtrim($dir, DIRECTORY_SEPARATOR);
  $result = [];

  foreach (glob($dir . DIRECTORY_SEPARATOR . '*', GLOB_MARK) as &$f) {
    if (substr($f, -1) === DIRECTORY_SEPARATOR) {
      $result = array_merge($result, recursiveDirList($f, $prefix . basename($f) . DIRECTORY_SEPARATOR));
    } else {
      $result[] = $prefix . basename($f);
    }
  }

  return $result;
}

/** The HTML form submit button wrapper function
 * Outputs a button in the selected language
 * @param string $text
 * @param string $type
 * @param string $parameters
 * @param string $icon
 * @return string
 */
function zen_button($text, $type = 'button', $parameters = '', $icon = '') {

  $button = '<button type="' . $type . '">';

  if (zen_not_null($text)) {
    $button .= ' title=" ' . zen_output_string($text) . ' "';
  }

  if (zen_not_null($parameters)) {
    $button .= ' ' . $parameters;
  }
  if (zen_not_null($icon)) {
    $button .= '<i class"fa ' . $icon . '"></i>';
  }
  if (zen_not_null($text)) {
    $button .= $text;
  }
  $button .= '</button>';

  return $button;
}

/**
 * Return all files with a certain name in a directory including sub-directories
 * @param string $path <p>
 * This parameter specifies the directory you wish to search trough.
 * It can reference a local directory
 * </p>
 * @param string $file <p>
 * This parameter specifies the file you wish to retrieve information
 * about. It can reference a local file
 * </p>
 * @return array <p>An array wich contains the retrieved files, including their paths</p>
 */
function dirList($path, $file) {
  $dirs = glob($path . '*', GLOB_ONLYDIR);
  $files = array();
//--- search through each folder for the file
//--- append results to $files
  foreach ($dirs as $d) {
    $f = glob($d . '/' . $file);
    if (count($f)) {
      $files = array_merge($files, $f);
    }
  }
  if (count($files)) {
    foreach ($files as $f) {
      $results[] = $f;
    }
    asort($results);
  } else {
    $results = '';
  }

  return $results;
}

/**
 * Returns an array of data needed for the product info
 * @param string $directory <p>Directory to be searched for files</p>
 * @return array
 */
function dirListProductFields($directory) {
    // create an array to hold directory list
    $results = array();
    
    if (is_dir($directory)){
        // create a handler for the directory
        if ($handler = opendir($directory)){
            // keep going until all files in directory have been read
            while ($file = readdir($handler)) {
                // if $file isn't this directory or its parent, 
                // add it to the results array
                if ($file != '.' && $file != '..')
                    $results[] = $file;
            }
            asort($results);
            // tidy up: close the handler
            closedir($handler);
            // done!
        }
    }
    
    return $results;
}

/**
 * Get the contents of the image folder
 * @return array
 */
  function getAdditionalImages($product_id, $products_image) {
   // $product_id = (int)zen_db_prepare_input($_POST['product_id']);
    $result['products_id'] = $product_id;
   // $products_image = $this->getProductMainImage($product_id);
    $result['products_image'] = DIR_WS_IMAGES . $products_image;

    // prepare image name
    $products_image_extension = substr($products_image, strrpos($products_image, '.'));
    $products_image_base = str_replace($products_image_extension, '', $products_image);

    $images_array = array();

    if (strrpos($products_image, '/')) {
      $products_image_match = substr($products_image, strrpos($products_image, '/') + 1);
      $products_image_match = str_replace($products_image_extension, '', $products_image_match);
      $products_image_base = $products_image_match;
    }

    $products_image_directory = str_replace($products_image, '', substr($products_image, strrpos($products_image, '/')));
    if ($products_image_directory != '') {
      $products_image_directory = DIR_WS_IMAGES . str_replace($products_image_directory, '', $products_image) . '/';
    } else {
      $products_image_directory = DIR_WS_IMAGES;
    }
    $image_count = 1;
    $search_directory = DIR_FS_CATALOG . $products_image_directory;
    $glob_search = $search_directory . $products_image_base . '_[0-9][0-9]' . $products_image_extension;
    $files = glob($glob_search);
    $result['glob_search'] = $glob_search;
    $result['image_base'] = $products_image_base;
    $result['extension'] = $products_image_extension;
    $result['search_dir'] = $search_directory;
    $images_array[] = array('filepath' => HTTPS_CATALOG_SERVER . DIR_WS_HTTPS_CATALOG . $products_image_directory . $products_image_base . $products_image_extension);
    $result['files'] = $files;
    if (is_array($files)) {
      foreach ($files as $file) {
        $image_count++;
        $image_suffix_number = str_replace($search_directory . $products_image_base . '_', '', $file);
        $image_suffix_number = str_replace($products_image_extension, '', $image_suffix_number);
        $images_array[] = array(
          'filepath' => str_replace(DIR_FS_CATALOG, HTTPS_CATALOG_SERVER . DIR_WS_HTTPS_CATALOG, $file),
          'filename' => str_replace(DIR_FS_CATALOG_IMAGES, '', $file),
          'count' => $image_count,
          'suffix_number' => $image_suffix_number
        );
      }
    }
    $result['last_image_suffix'] = (int)$image_suffix_number;
    $next_image_suffix = (int)$image_suffix_number + 1;
    $text_image_suffix = str_pad($next_image_suffix, 2, '0', STR_PAD_LEFT);
    $new_img_dir = str_replace(DIR_WS_IMAGES, '', $products_image_directory);
    $result['new_filename'] = $products_image_base . '_' . $text_image_suffix . $products_image_extension;
    $result['destination'] = $new_img_dir;
    $result['next_image_number'] = $image_count;
    $result['success'] = 'Success: Found Images';
    $result['images'] = $images_array;

    return $result;
  }

/**
 * Return the information for a product field
 * @param integer $id <p>
 * This parameter specifies the id from the field you want queried.
 * </p>
 * @return array <p>An array which contains the retrieved field information</p>
 */
function getFieldInformation($id) {
  global $db;
  $fieldQuery = "SELECT id, name, description, core
                 FROM " . TABLE_PRODUCT_TYPE_FIELDS . "
                 WHERE id = " . $id;
  $field = $db->Execute($fieldQuery);
  $fieldInformation = [
    'id' => $field->fields['id'],
    'name' => $field->fields['name'],
    'description' => $field->fields['description'],
    'core' => $field->fields['core']
  ];
  return $fieldInformation;
}

function getProductTypeInfo ($ProductTypeId) {
  global $db;
  $productTypeInfoQuery = "SELECT pt.*, COUNT(p.products_id) AS products_count
                           FROM " . TABLE_PRODUCT_TYPES . " pt
                           LEFT JOIN " . TABLE_PRODUCTS . " p ON p.products_type = pt.type_id
                           WHERE pt.type_id = " . (int)$ProductTypeId;
$productTypeInfo = $db->Execute($productTypeInfoQuery);

return $productTypeInfoArray = new objectInfo($productTypeInfo->fields);
}