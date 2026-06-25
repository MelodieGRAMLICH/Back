<?php

require __DIR__ . '/../Controllers/AuthController.php';

Flight::route('POST /api/register', ['AuthController', 'inscription']);

Flight::route('POST /api/login', ['AuthController', 'connexion']);

Flight::route('GET /api/me', ['AuthController', 'monProfil']);