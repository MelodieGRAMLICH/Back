<?php

require_once __DIR__ . '/../Models/User.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

class AuthController
{

    // ─── INSCRIPTION ─────────────────────────────────────────────────────────

    public static function register(): void
    {
        // 1. Récupère les données envoyées par React (en JSON)
        $data = Flight::request()->data;

        $username = trim($data->pseudo ?? '');
        $email    = trim($data->email ?? '');
        $password = $data->mot_de_passe ?? '';

        // 2. Validation : vérifie que tout est bien rempli
        if (empty($username) || empty($email) || empty($password)) {
            Flight::json(['erreur' => 'Tous les champs sont obligatoires'], 400);
            return;
        }

        // Vérifie que l'email a un format valide
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flight::json(['erreur' => 'Email invalide'], 400);
            return;
        }

        // Vérifie que le mot de passe fait au moins 8 caractères
        if (strlen($password) < 8) {
            Flight::json(['erreur' => 'Le mot de passe doit faire au moins 8 caractères'], 400);
            return;
        }

        // 3. Vérifie que cet email n'est pas déjà utilisé
        if (User::findByEmail($email)) {
            Flight::json(['erreur' => 'Cet email est déjà utilisé'], 409); // 409 = Conflit
            return;
        }

        // 4. Hash le mot de passe (on ne stocke JAMAIS le vrai mot de passe)
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // 5. Insère l'utilisateur dans la base
        $newId = User::create($username, $email, $hash);

        // 6. Récupère l'utilisateur qu'on vient de créer
        $user = [
            'id'     => $newId,
            'pseudo' => $username,
            'email'  => $email,
            'role'   => 'client',
        ];

        // 7. Crée un token JWT et le renvoie à React
        $token = createToken($user);

        Flight::json([
            'message'     => 'Inscription réussie !',
            'token'       => $token,
            'utilisateur' => $user,
        ], 201); // 201 = Created
    }

    // ─── CONNEXION ────────────────────────────────────────────────────────────

    public static function login(): void
    {
        // 1. Récupère les données envoyées par React
        $data = Flight::request()->data;

        $email    = trim($data->email ?? '');
        $password = $data->mot_de_passe ?? '';

        // 2. Validation basique
        if (empty($email) || empty($password)) {
            Flight::json(['erreur' => 'Email et mot de passe obligatoires'], 400);
            return;
        }

        // 3. Cherche l'utilisateur par son email
        $user = User::findByEmail($email);

        // 4. Vérifie que l'utilisateur existe ET que le mot de passe est correct
        // password_verify() compare le mot de passe saisi avec le hash stocké
        if (!$user || !password_verify($password, $user['mot_de_passe'])) {
            Flight::json(['erreur' => 'Email ou mot de passe incorrect'], 401);
            return;
        }

        // 5. Prépare les infos à mettre dans le token (sans le mot de passe !)
        $userInfo = [
            'id'     => $user['id'],
            'pseudo' => $user['pseudo'],
            'email'  => $user['email'],
            'role'   => $user['role'],
        ];

        // 6. Crée le token et répond à React
        $token = createToken($userInfo);

        Flight::json([
            'message'     => 'Connexion réussie !',
            'token'       => $token,
            'utilisateur' => $userInfo,
        ]);
    }

    // ─── PROFIL (route protégée – exemple) ───────────────────────────────────

    public static function myProfile(): void
    {
        // Vérifie le token envoyé par React
        $user = verifyToken();

        if (!$user) {
            Flight::json(['erreur' => 'Non connecté ou session expirée'], 401);
            return;
        }

        // Récupère les infos fraîches depuis la BDD (sub = l'id qu'on a mis dans le token)
        $profile = User::findById($user->sub);

        Flight::json($profile);
    }
}