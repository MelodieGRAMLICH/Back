<?php

Flight::route('GET /api/products/@categorie', function($categorie) {
    $db = Flight::get('db');
    $stmt = $db->prepare("SELECT * FROM products WHERE categories = ?");
    $stmt->execute([$categorie]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    Flight::json(array_values($products));
});