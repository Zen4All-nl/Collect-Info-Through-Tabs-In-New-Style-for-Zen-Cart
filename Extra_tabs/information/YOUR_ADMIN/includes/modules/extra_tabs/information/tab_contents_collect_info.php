<?php
if (isset($_GET['pID']) && $_GET['pID'] != '') {
  $languages = zen_get_languages();

  $orderedQuery = "SELECT sum(products_quantity) as products_ordered
                   FROM " . TABLE_ORDERS_PRODUCTS . "
                   WHERE products_id = " . $_GET['pID'];
  $ordered = $db->Execute($orderedQuery);

  $viewQuery = "SELECT language_id, products_viewed
                FROM " . TABLE_PRODUCTS_DESCRIPTION . "
                WHERE products_id = " . $_GET['pID'];
  $view = $db->Execute($viewQuery);

  $viewArray = array();
  foreach ($view as $item) {
    $viewArray[] = array(
      'language_id' => $item['language_id'],
      'views' => $item['products_viewed']);
  }
  ?>
  <div class="col-sm-12">
    <ul class="list-group col-sm-3">
      <li class="list-group-item justify-content-between">Numbers in stock:<span class="badge badge-default badge-pill"><?php echo $productInfo['products_quantity']['value']; ?></span></li>
      <li class="list-group-item justify-content-between">Numbers sold: <span class="badge badge-default badge-pill"><?php echo $ordered->fields['products_ordered']; ?></span></li>
      <li class="list-group-item justify-content-between">Times viewed: 
        <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
          <span class="badge badge-default badge-pill"><?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . $viewArray[$i]['views']; ?></span>
        <?php } ?>
      </li>
    </ul>
  </div>
  <?php
} else {
  echo '<p class="bg-info">Please save the new product first.</p>';
}