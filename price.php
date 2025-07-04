<?php

$products = json_decode(file_get_contents('products.json'), true);

if (!is_array($products)) {
    echo "Помилка JSON: " . json_last_error_msg();
    $products = [];
}