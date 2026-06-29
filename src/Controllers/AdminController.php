<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

class AdminController
{
 
    private static function requireAdmin(): mixed
    {
        $admin = verifierAdmin();

        if (!$admin) {
            Flight::json(['erreur' => 'Accès refusé : réservé aux administrateurs'], 403);
            return null;
        }

        return $admin;
    }

    
    public static function listerUtilisateurs(): void
    {
        if (!($admin = self::requireAdmin())) return;

        $db   = Flight::get('db');
        $stmt = $db->prepare("
            SELECT id, pseudo, email, role, date_creation
            FROM utilisateurs
            ORDER BY date_creation DESC
        ");
        $stmt->execute();

        Flight::json($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function changerRole(int $id): void
    {
        if (!($admin = self::requireAdmin())) return;

        if ((int)$admin->sub === $id) {
            Flight::json(['erreur' => 'Tu ne peux pas modifier ton propre rôle'], 400);
            return;
        }

        $nouveauRole = trim(Flight::request()->data->role ?? '');

        if (!in_array($nouveauRole, ['client', 'admin'])) {
            Flight::json(['erreur' => 'Rôle invalide'], 400);
            return;
        }

        $db   = Flight::get('db');
        $stmt = $db->prepare("UPDATE utilisateurs SET role = ? WHERE id = ?");
        $stmt->execute([$nouveauRole, $id]);

        Flight::json(['message' => 'Rôle mis à jour avec succès']);
    }

    public static function supprimerUtilisateur(int $id): void
    {
        if (!($admin = self::requireAdmin())) return;

        if ((int)$admin->sub === $id) {
            Flight::json(['erreur' => 'Tu ne peux pas supprimer ton propre compte'], 400);
            return;
        }

        $db   = Flight::get('db');
        $stmt = $db->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id]);

        Flight::json(['message' => 'Utilisateur supprimé avec succès']);
    }

    public static function listerProduits(): void
    {
        if (!self::requireAdmin()) return;

        $db   = Flight::get('db');
        $stmt = $db->prepare("SELECT * FROM products ORDER BY id DESC");
        $stmt->execute();

        Flight::json($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function creerProduit(): void
    {
        if (!self::requireAdmin()) return;

        $donnees     = Flight::request()->data;
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

        $db   = Flight::get('db');
        $stmt = $db->prepare("
            INSERT INTO products (name, description, price, quantity, image, categories)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$name, $description, $price, $quantity, $image, $categories]);

        Flight::json(['message' => 'Produit créé avec succès', 'id' => $db->lastInsertId()], 201);
    }

    public static function modifierProduit(int $id): void
    {
        if (!self::requireAdmin()) return;

        $donnees     = Flight::request()->data;
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

        $db   = Flight::get('db');
        $stmt = $db->prepare("
            UPDATE products
            SET name = ?, description = ?, price = ?, quantity = ?, image = ?, categories = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $description, $price, $quantity, $image, $categories, $id]);

        Flight::json(['message' => 'Produit modifié avec succès']);
    }

    public static function supprimerProduit(int $id): void
    {
        if (!self::requireAdmin()) return;

        $db   = Flight::get('db');
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);

        Flight::json(['message' => 'Produit supprimé avec succès']);
    }
}