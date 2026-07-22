<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Product.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

class AdminController
{

    private static function requireAdmin(): mixed
    {
        $admin = verifyAdmin();

        if (!$admin) {
            Flight::json(['erreur' => 'Accès refusé : réservé aux administrateurs'], 403);
            return null;
        }

        return $admin;
    }

    public static function listUsers(): void
    {
        if (!self::requireAdmin()) return;

        Flight::json(User::listAll());
    }

    public static function changeRole(int $id): void
    {
        if (!($admin = self::requireAdmin())) return;

        if ((int)$admin->sub === $id) {
            Flight::json(['erreur' => 'Tu ne peux pas modifier ton propre rôle'], 400);
            return;
        }

        $newRole = trim(Flight::request()->data->role ?? '');

        if (!in_array($newRole, ['client', 'admin'])) {
            Flight::json(['erreur' => 'Rôle invalide'], 400);
            return;
        }

        User::changeRole($id, $newRole);

        Flight::json(['message' => 'Rôle mis à jour avec succès']);
    }

    public static function deleteUser(int $id): void
    {
        if (!($admin = self::requireAdmin())) return;

        if ((int)$admin->sub === $id) {
            Flight::json(['erreur' => 'Tu ne peux pas supprimer ton propre compte'], 400);
            return;
        }

        User::delete($id);

        Flight::json(['message' => 'Utilisateur supprimé avec succès']);
    }

    public static function listProducts(): void
    {
        if (!self::requireAdmin()) return;

        Flight::json(Product::listAll());
    }

    public static function createProduct(): void
    {
        if (!self::requireAdmin()) return;

        $data        = Flight::request()->data;
        $name        = trim($data->name ?? '');
        $description = trim($data->description ?? '');
        $price       = $data->price ?? null;
        $quantity    = $data->quantity ?? null;
        $image       = trim($data->image ?? '');
        $categories  = trim($data->categories ?? '');

        if (empty($name) || $price === null || $quantity === null) {
            Flight::json(['erreur' => 'Le nom, le prix et la quantité sont obligatoires'], 400);
            return;
        }

        $id = Product::create($name, $description, $price, $quantity, $image, $categories);

        Flight::json(['message' => 'Produit créé avec succès', 'id' => $id], 201);
    }

    public static function updateProduct(int $id): void
    {
        if (!self::requireAdmin()) return;

        $data        = Flight::request()->data;
        $name        = trim($data->name ?? '');
        $description = trim($data->description ?? '');
        $price       = $data->price ?? null;
        $quantity    = $data->quantity ?? null;
        $image       = trim($data->image ?? '');
        $categories  = trim($data->categories ?? '');

        if (empty($name) || $price === null || $quantity === null) {
            Flight::json(['erreur' => 'Le nom, le prix et la quantité sont obligatoires'], 400);
            return;
        }

        Product::update($id, $name, $description, $price, $quantity, $image, $categories);

        Flight::json(['message' => 'Produit modifié avec succès']);
    }

    public static function deleteProduct(int $id): void
    {
        if (!self::requireAdmin()) return;

        Product::delete($id);

        Flight::json(['message' => 'Produit supprimé avec succès']);
    }
}