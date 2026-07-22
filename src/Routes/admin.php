<?php

require __DIR__ . '/../Controllers/AdminController.php';

Flight::route('GET /api/admin/utilisateurs', ['AdminController', 'listUsers']);
Flight::route('PUT /api/admin/utilisateurs/@id/role', ['AdminController', 'changeRole']);
Flight::route('DELETE /api/admin/utilisateurs/@id', ['AdminController', 'deleteUser']);
Flight::route('GET /api/admin/produits', ['AdminController', 'listProducts']);
Flight::route('POST /api/admin/produits', ['AdminController', 'createProduct']);
Flight::route('PUT /api/admin/produits/@id', ['AdminController', 'updateProduct']);
Flight::route('DELETE /api/admin/produits/@id', ['AdminController', 'deleteProduct']);