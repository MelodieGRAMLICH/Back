<?php

require_once __DIR__ . '/../Models/Product.php';

Flight::route('GET /api/produits/@category', function ($category) {
    $products = Product::byCategory($category);

    Flight::json(array_values($products));
});

Flight::route('GET /api/produits/detail/@name', function ($name) {
    $product = Product::byName($name);

    Flight::json($product);
});