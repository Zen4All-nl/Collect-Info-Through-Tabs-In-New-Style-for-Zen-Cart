<?php

$discount_qty = $_POST['discount_qty'];
for ($i = 0, $n = sizeof($discount_qty); $i < $n; $i++) {
  echo zen_draw_hidden_field('discount_qty[' . ($i + 1) . ']', $discount_qty[$i + 1]);
}
$discount_price = $_POST['discount_price'];
for ($i = 0, $n = sizeof($discount_price); $i < $n; $i++) {
  echo zen_draw_hidden_field('discount_price[' . ($i + 1) . ']', $discount_price[$i + 1]);
}