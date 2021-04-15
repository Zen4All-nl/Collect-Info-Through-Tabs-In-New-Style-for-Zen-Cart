<?php
/*
 *
 * @copyright Copyright 2008-2021 Zen4All
 * @license https://github.com/Zen4All-nl/Collect-Info-Through-Tabs-In-New-Style-for-Zen-Cart/blob/stable/LICENSE GNU Public License V2.0
 * @version Cittins 2.0.0 by Zen4All
 * 
 */

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

$autoLoadConfig[999][] = [
  'autoType' => 'init_script',
  'loadFile' => 'init_cittins.php'
];
