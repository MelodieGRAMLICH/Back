<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

require __DIR__ . '/../src/config/database.php';
require __DIR__ . '/../src/config/jwt.php';
require __DIR__ . '/../src/Routes/status.php';
require __DIR__ . '/../src/Routes/auth.php';
require __DIR__ . '/../src/Routes/products.php';
require __DIR__ . '/../src/Routes/admin.php';

Flight::before('start', function() {
    $allowedOrigins = [
        'http://localhost:5173',
        'https://TON-PROJET.vercel.app',
    ];

    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

    if (in_array($origin, $allowedOrigins)) {
        header('Access-Control-Allow-Origin: ' . $origin);
    }

    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, ngrok-skip-browser-warning');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
});

Flight::set('flight.allow_overrides', true);

Flight::route('GET /images/@file', function($file) {
    $path = __DIR__ . '/images/' . $file;
    if (file_exists($path)) {
        $mime = mime_content_type($path);
        header('Content-Type: ' . $mime);
        readfile($path);
    } else {
        Flight::halt(404, 'Image non trouvée');
    }
});

Flight::start();