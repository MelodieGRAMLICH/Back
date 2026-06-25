<?php

require __DIR__ . '/../Controllers/AdminController.php';
Flight::route('GET /api/admin/utilisateurs', ['AdminController', 'listerUtilisateurs']);
Flight::route('PUT /api/admin/utilisateurs/@id/role', ['AdminController', 'changerRole']);
Flight::route('DELETE /api/admin/utilisateurs/@id', ['AdminController', 'supprimerUtilisateur']);
Flight::route('GET /api/admin/produits', ['AdminController', 'listerProduits']);
Flight::route('POST /api/admin/produits', ['AdminController', 'creerProduit']);
Flight::route('PUT /api/admin/produits/@id', ['AdminController', 'modifierProduit']);
Flight::route('DELETE /api/admin/produits/@id', ['AdminController', 'supprimerProduit']);