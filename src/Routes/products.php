<?php

Flight::route('GET /api/produits/@categorie', function($categorie) {
    $db = Flight::get('db');
    $stmt = $db->prepare("SELECT * FROM produits WHERE categories = ?");
    $stmt->execute([$categorie]);
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    Flight::json(array_values($produits));
});

Flight::route('GET /api/produits/detail/@name', function($name) {
    $db = Flight::get('db');

    $stmt = $db->prepare(
        "SELECT * FROM produits WHERE name = ?"
    );

    $stmt->execute([$name]);

    $produit = $stmt->fetch(PDO::FETCH_ASSOC);

    Flight::json($produit);
});