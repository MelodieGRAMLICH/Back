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
    header('Access-Control-Allow-Origin: http://localhost:5173');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
});

Flight::set('flight.allow_overrides', true);

Flight::route('GET /images/@fichier', function($fichier) {
    $path = __DIR__ . '/images/' . $fichier;
    if (file_exists($path)) {
        $mime = mime_content_type($path);
        header('Content-Type: ' . $mime);
        readfile($path);
    } else {
        Flight::halt(404, 'Image non trouvée');
    }
});

Flight::start();