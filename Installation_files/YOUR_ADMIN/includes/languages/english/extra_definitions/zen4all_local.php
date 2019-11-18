<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('DATE_FORMAT')) {
 define('DATE_FORMAT', 'm/d/Y'); // wordt gebruikt voor date()
 }
define('DATE_FORMAT_DATEPICKER_ADMIN', zen_date_datepicker(DATE_FORMAT)); // Alternatively skip the function and specify a string of 'dd', 'mm' and 'yy' in any order

define('_SUNDAY_SHORT','Su');
define('_MONDAY_SHORT','Mo');
define('_TUESDAY_SHORT','Tu');
define('_WEDNESDAY_SHORT','We');
define('_THURSDAY_SHORT','Th');
define('_FRIDAY_SHORT','Fr');
define('_SATURDAY_SHORT','Sa');

// Converts dsmnyY chars to accepted values because the datepicker widget accepts only 'dd', 'mm' and 'yy' (in any order)
function zen_date_datepicker($format)
{
  $date = preg_replace('/[ds]/', 'dd', $format);
  $date = preg_replace('/[mn]/', 'mm', $date);
  $date = preg_replace('/[yY]/', 'yy', $date);
  return $date;
}