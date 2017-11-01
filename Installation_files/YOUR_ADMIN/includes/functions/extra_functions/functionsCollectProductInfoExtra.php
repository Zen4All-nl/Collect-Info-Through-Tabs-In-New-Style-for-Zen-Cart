<?php
/*
 * @package admin
 * @copyright Copyright 2008-2017 Zen4All
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: functionsCollectProductInfoExtra.php Zen4All $
 */


/*
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


function getImageFolderContents() {

  $directory = DIR_FS_CATALOG_IMAGES;
  $directories = array();
  $files = array();
  $page = 1;
  $data['images'] = array();
  // Get directories
  $directories = glob($directory . '*', GLOB_ONLYDIR);

  if (!$directories) {
    $directories = array();
  }

  // Get files
  $files = glob($directory . '*.{jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF}', GLOB_BRACE);

  if (!$files) {
    $files = array();
  }
  // Merge directories and files
  $images = array_merge($directories, $files);
  // Get total number of files and directories
  $image_total = count($images);

  // Split the array based on current page number and max number of items per page of 10
  $pageImages = array_splice($images, ($page - 1) * 16, 16);
  foreach ($pageImages as $image) {
    $name = str_split(basename($image), 14);

    if (is_dir($image)) {
      $url = '';

      if (isset($x['thumb'])) {
        $url .= '&thumb=' . $x['thumb'];
      }

      $data['images'][] = array(
        'thumb' => '',
        'name' => implode(' ', $name),
        'type' => 'directory',
        'path' => '',
        'href' => DIR_WS_CATALOG_IMAGES . ''
      );
    } elseif (is_file($image)) {
      $data['images'][] = array(
        'thumb' => DIR_WS_CATALOG_IMAGES . substr($image, strlen(DIR_FS_CATALOG_IMAGES)),
        'name' => implode(' ', $name),
        'type' => 'image',
        'path' => substr($image, strlen(DIR_FS_CATALOG_IMAGES)),
        'href' => DIR_WS_CATALOG_IMAGES . substr($image, strlen(DIR_FS_CATALOG_IMAGES))
      );
    }
  }
  return $data;
}
