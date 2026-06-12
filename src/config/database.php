<?php

try {
    $dsn = "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'] . ";charset=utf8mb4";

    $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    Flight::set('db', $pdo);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erreur' => 'Connexion base de données impossible : ' . $e->getMessage()]);
    exit();
}