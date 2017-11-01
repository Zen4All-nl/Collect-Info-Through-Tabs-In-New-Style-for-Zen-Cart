<?php
$categories_add = $_POST['categories_add'];
for ($i = 0, $n = sizeof($categories_add); $i < $n; $i++) {
  echo zen_draw_hidden_field('categories_add[' . $i . ']', $categories_add[$i]);
}