<?php
/**
 * @package admin
 * @copyright Copyright 2003-2016 Zen Cart Development Team
 * @copyright Portions Copyright 2010 Kuroi Web Design
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Author: DrByte  Thu Jan 7 14:19:15 2016 -0500 New in v1.5.5 $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

$var = zen_get_languages();
$jsLanguageLookupArray = "var lang = new Array;\n";
foreach ($var as $key)
{
  $jsLanguageLookupArray .= "  lang[" . $key['id'] . "] = '" . $key['code'] . "';\n";
}
?>
<!--<script>window.jQuery || document.write('<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"><\/script>');</script>
<script>window.jQuery || document.write('<script src="includes/javascript/jquery-1.12.1.min.js"><\/script>');</script> -->
<!-- <script src="//cdn.ckeditor.com/4.7.2/standard/ckeditor.js"></script> -->
<script type="text/javascript" src="../<?php echo DIR_WS_EDITORS ?>ckeditor/ckeditor.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  <?php echo $jsLanguageLookupArray ?>
  $('textarea').each(function() {
    if ($(this).hasClass('editorHook') || ($(this).attr('name') != 'message' && ! $(this).hasClass('noEditor')))
    {
      index = $(this).attr('name').match(/\d+/);
      if (index == null) index = <?php echo $_SESSION['languages_id'] ?>;
      CKEDITOR.replace($(this).attr('name'),
        {
          coreStyles_underline : { element : 'u' },
          width : 760,
          language: lang[index]
        });
    }
  });
});
</script>
<!--<script type="text/javascript">
  $('.editorHook').each(function() {
      CKEDITOR.replace(this.id,
        {
          coreStyles_underline : { element : 'u' },
          width : 760,
          language: lang[index]
        });
    });
</script>-->
<!--<script type="text/javascript">
$(function(){
    $('.editorHook').each(function(e){
        CKEDITOR.replace( this.id,
        {
          coreStyles_underline : { element : 'u' },
          width : 760
        });
    });
});
</script>-->