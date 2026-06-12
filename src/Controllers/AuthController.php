<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

class AuthController
{
    

    // ─── INSCRIPTION ─────────────────────────────────────────────────────────

    public static function inscription(): void
    {
        // 1. Récupère les données envoyées par React (en JSON)


        $donnees = Flight::request()->data;

        $pseudo   = trim($donnees->pseudo ?? '');
        $email    = trim($donnees->email ?? '');
        $motDePasse = $donnees->mot_de_passe ?? '';

        // 2. Validation : vérifie que tout est bien rempli
        if (empty($pseudo) || empty($email) || empty($motDePasse)) {
            Flight::json(['erreur' => 'Tous les champs sont obligatoires'], 400);
            return;
        }

        // Vérifie que l'email a un format valide
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flight::json(['erreur' => 'Email invalide'], 400);
            return;
        }

        // Vérifie que le mot de passe fait au moins 8 caractères
        if (strlen($motDePasse) < 8) {
            Flight::json(['erreur' => 'Le mot de passe doit faire au moins 8 caractères'], 400);
            return;
        }

        $db = Flight::get('db');

        // 3. Vérifie que cet email n'est pas déjà utilisé
        $stmt = $db->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            Flight::json(['erreur' => 'Cet email est déjà utilisé'], 409); // 409 = Conflit
            return;
        }

        // 4. Hash le mot de passe (on ne stocke JAMAIS le vrai mot de passe)
        $hash = password_hash($motDePasse, PASSWORD_BCRYPT);

        // 5. Insère l'utilisateur dans la base
        $stmt = $db->prepare("
            INSERT INTO utilisateurs (pseudo, email, mot_de_passe)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$pseudo, $email, $hash]);

        // 6. Récupère l'utilisateur qu'on vient de créer
        $nouvelId = $db->lastInsertId();
        $utilisateur = [
            'id'     => $nouvelId,
            'pseudo' => $pseudo,
            'email'  => $email,
            'role'   => 'client',
        ];

        // 7. Crée un token JWT et le renvoie à React
        
        $token = creerToken($utilisateur);

        

        Flight::json([
            'message'     => 'Inscription réussie !',
            'token'       => $token,
            'utilisateur' => $utilisateur,
        ], 201); // 201 = Created
    }



    // ─── CONNEXION ────────────────────────────────────────────────────────────



    public static function connexion(): void
    {
        // 1. Récupère les données envoyées par React
        $donnees = Flight::request()->data;

        $email      = trim($donnees->email ?? '');
        $motDePasse = $donnees->mot_de_passe ?? '';

        // 2. Validation basique
        if (empty($email) || empty($motDePasse)) {
            Flight::json(['erreur' => 'Email et mot de passe obligatoires'], 400);
            return;


    


            
        }

        $db = Flight::get('db');

        // 3. Cherche l'utilisateur par son email
        $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $utilisateur = $stmt->fetch();

        // 4. Vérifie que l'utilisateur existe ET que le mot de passe est correct
        // password_verify() compare le mot de passe saisi avec le hash stocké
        if (!$utilisateur || !password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
            // Message volontairement vague : on ne dit pas si c'est l'email ou le mdp qui est faux
            Flight::json(['erreur' => 'Email ou mot de passe incorrect'], 401);
            return;
        }

        // 5. Prépare les infos à mettre dans le token (sans le mot de passe !)
        $infosUtilisateur = [
            'id'     => $utilisateur['id'],
            'pseudo' => $utilisateur['pseudo'],
            'email'  => $utilisateur['email'],
            'role'   => $utilisateur['role'],
        ];

        // 6. Crée le token et répond à React
        $token = creerToken($infosUtilisateur);

        Flight::json([
            'message'     => 'Connexion réussie !',
            'token'       => $token,
            'utilisateur' => $infosUtilisateur,
        ]);
    }

    // ─── PROFIL (route protégée – exemple) ───────────────────────────────────

    
    public static function monProfil(): void
    {
        // Vérifie le token envoyé par React
        $utilisateur = verifierToken();

        if (!$utilisateur) {
            Flight::json(['erreur' => 'Non connecté ou session expirée'], 401);
            return;
        }

        // Récupère les infos fraîches depuis la BDD
        $db = Flight::get('db');
        $stmt = $db->prepare("
            SELECT id, pseudo, email, role, date_creation
            FROM utilisateurs
            WHERE id = ?
        ");
        $stmt->execute([$utilisateur->sub]); // sub = l'id qu'on a mis dans le token
        $profil = $stmt->fetch();

        Flight::json($profil);
    }
}