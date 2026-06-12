<?php

require __DIR__ . '/../Controllers/AuthController.php';

// ── POST /api/register ───────────────────────────────────────────────────────
// React envoie : { pseudo, email, mot_de_passe }
// Réponse     : { token, utilisateur }
Flight::route('POST /api/register', ['AuthController', 'inscription']);

// ── POST /api/login ──────────────────────────────────────────────────────────
// React envoie : { email, mot_de_passe }
// Réponse     : { token, utilisateur }
Flight::route('POST /api/login', ['AuthController', 'connexion']);

// ── GET /api/me ──────────────────────────────────────────────────────────────
// React envoie le token dans le header
// Réponse     : les infos du profil
Flight::route('GET /api/me', ['AuthController', 'monProfil']);