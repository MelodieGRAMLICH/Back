<?php
require_once __DIR__ . '/../Controllers/statusController.php';
Flight::route('GET /', [StatusController::class, 'home']);
Flight::route('GET /api/status', [StatusController::class, 'status']);