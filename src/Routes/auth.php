<?php

require __DIR__ . '/../Controllers/AuthController.php';

Flight::route('POST /api/register', ['AuthController', 'register']);

Flight::route('POST /api/login', ['AuthController', 'login']);

Flight::route('GET /api/me', ['AuthController', 'myProfile']);