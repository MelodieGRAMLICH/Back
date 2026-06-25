<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

class AdminController
{

    public static function listerUtilisateurs(): void
    {
        $admin = verifierAdmin();

        if (!$admin) {
            Flight::json(['erreur' => 'Accès refusé : réservé aux administrateurs'], 403);
            return;
        }

        $db = Flight::get('db');
        $stmt = $db->prepare("
            SELECT id, pseudo, email, role, date_creation
            FROM utilisateurs
            ORDER BY date_creation DESC
        ");
        $stmt->execute();
        $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        Flight::json($utilisateurs);
    }

    public static function changerRole($id): void
    {
        $admin = verifierAdmin();

        if (!$admin) {
            Flight::json(['erreur' => 'Accès refusé : réservé aux administrateurs'], 403);
            return;
        }

        if ((int)$admin->sub === (int)$id) {
            Flight::json(['erreur' => 'Tu ne peux pas modifier ton propre rôle'], 400);
            return;
        }

        $donnees = Flight::request()->data;
        $nouveauRole = trim($donnees->role ?? '');

        $rolesAutorises = ['client', 'admin'];
        if (!in_array($nouveauRole, $rolesAutorises)) {
            Flight::json(['erreur' => 'Rôle invalide'], 400);
            return;
        }

        $db = Flight::get('db');
        $stmt = $db->prepare("UPDATE utilisateurs SET role = ? WHERE id = ?");
        $stmt->execute([$nouveauRole, $id]);

        Flight::json(['message' => 'Rôle mis à jour avec succès']);
    }

        public static function supprimerUtilisateur($id): void
    {
        
        $admin = verifierAdmin();

        if (!$admin) {
            Flight::json(['erreur' => 'Accès refusé : réservé aux administrateurs'], 403);
            return;
        }

        
        if ((int)$admin->sub === (int)$id) {
            Flight::json(['erreur' => 'Tu ne peux pas supprimer ton propre compte'], 400);
            return;
        }

        
        $db = Flight::get('db');
        $stmt = $db->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id]);

        Flight::json(['message' => 'Utilisateur supprimé avec succès']);
    }

        public static function listerProduits(): void
    {
        $admin = verifierAdmin();

        if (!$admin) {
            Flight::json(['erreur' => 'Accès refusé : réservé aux administrateurs'], 403);
            return;
        }

        $db = Flight::get('db');
        $stmt = $db->prepare("SELECT * FROM products ORDER BY id DESC");
        $stmt->execute();
        $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

        Flight::json($produits);
    }

        public static function creerProduit(): void
    {
        $admin = verifierAdmin();

        if (!$admin) {
            Flight::json(['erreur' => 'Accès refusé : réservé aux administrateurs'], 403);
            return;
        }


        $donnees = Flight::request()->data;

        $name        = trim($donnees->name ?? '');
        $description = trim($donnees->description ?? '');
        $price       = $donnees->price ?? null;
        $quantity    = $donnees->quantity ?? null;
        $image       = trim($donnees->image ?? '');
        $categories  = trim($donnees->categories ?? '');


        if (empty($name) || $price === null || $quantity === null) {
            Flight::json(['erreur' => 'Le nom, le prix et la quantité sont obligatoires'], 400);
            return;
        }

        $db = Flight::get('db');
        $stmt = $db->prepare("
            INSERT INTO products (name, description, price, quantity, image, categories)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$name, $description, $price, $quantity, $image, $categories]);

        $nouvelId = $db->lastInsertId();

        Flight::json([
            'message' => 'Produit créé avec succès',
            'id'      => $nouvelId,
        ], 201);
    }

    // ─── MODIFIER UN PRODUIT (route protégée admin) ──────────────────────────

    public static function modifierProduit($id): void
    {
        $admin = verifierAdmin();

        if (!$admin) {
            Flight::json(['erreur' => 'Accès refusé : réservé aux administrateurs'], 403);
            return;
        }

        $donnees = Flight::request()->data;

        $name        = trim($donnees->name ?? '');
        $description = trim($donnees->description ?? '');
        $price       = $donnees->price ?? null;
        $quantity    = $donnees->quantity ?? null;
        $image       = trim($donnees->image ?? '');
        $categories  = trim($donnees->categories ?? '');

        if (empty($name) || $price === null || $quantity === null) {
            Flight::json(['erreur' => 'Le nom, le prix et la quantité sont obligatoires'], 400);
            return;
        }

        $db = Flight::get('db');
        $stmt = $db->prepare("
            UPDATE products
            SET name = ?, description = ?, price = ?, quantity = ?, image = ?, categories = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $description, $price, $quantity, $image, $categories, $id]);

        Flight::json(['message' => 'Produit modifié avec succès']);
    }

    public static function supprimerProduit($id): void
    {
        $admin = verifierAdmin();

        if (!$admin) {
            Flight::json(['erreur' => 'Accès refusé : réservé aux administrateurs'], 403);
            return;
        }

        $db = Flight::get('db');
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);

        Flight::json(['message' => 'Produit supprimé avec succès']);
    }
}