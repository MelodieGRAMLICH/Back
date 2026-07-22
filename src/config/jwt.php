<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


function createToken(array $user): string
{
    $secretKey = $_ENV['JWT_SECRET'];
    $duration  = (int) $_ENV['JWT_EXPIRATION'];
    $payload = [
        'iat'   => time(),                    // "issued at" : quand le token a été créé
        'exp'   => time() + $duration,        // "expires" : quand il expire
        'sub'   => $user['id'],               // "subject" : l'id de l'utilisateur
        'email' => $user['email'],
        'role'  => $user['role'],
        'pseudo'=> $user['pseudo'],
    ];


    // JWT::encode() fabrique le token signé avec notre clé secrète
    $jwt = JWT::encode($payload, $secretKey, 'HS256');
    return $jwt;
}


// ─── Vérifie et décode un token reçu ─────────────────────────────────────────
// Retourne les infos de l'utilisateur, ou null si le token est invalide/expiré
function verifyToken(): ?object
{
    // Le token est envoyé dans le header HTTP : "Authorization: Bearer montoken..."
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

    if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
        return null; // pas de token
    }

    $token = substr($authHeader, 7); // on enlève "Bearer " pour garder juste le token

    try {
        $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        return $decoded; // retourne un objet avec id, email, role, pseudo
    } catch (Exception $e) {
        return null; // token invalide ou expiré
    }
}


function verifyAdmin(): ?object
{
    // 1. On réutilise la fonction qui existe déjà : est-ce que la personne est connectée ?
    $user = verifyToken();

    // 2. Si pas connecté du tout, ou si le rôle n'est pas "admin" → on bloque
    if (!$user || $user->role !== 'admin') {
        return null;
    }

    // 3. Tout est bon : on renvoie les infos (id, email, role, pseudo)
    return $user;
}