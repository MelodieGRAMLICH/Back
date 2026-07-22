<?php

class Product
{
    public static function listAll(): array
    {
        $db   = Flight::get('db');
        $stmt = $db->prepare("SELECT * FROM produits ORDER BY id DESC");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function byCategory(string $category): array
    {
        $db   = Flight::get('db');
        $stmt = $db->prepare("SELECT * FROM produits WHERE categories = ?");
        $stmt->execute([$category]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function byName(string $name): array|false
    {
        $db   = Flight::get('db');
        $stmt = $db->prepare("SELECT * FROM produits WHERE name = ?");
        $stmt->execute([$name]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create(
        string $name,
        string $description,
        $price,
        $quantity,
        string $image,
        string $categories
    ): string {
        $db   = Flight::get('db');
        $stmt = $db->prepare("
            INSERT INTO produits (name, description, price, quantity, image, categories)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$name, $description, $price, $quantity, $image, $categories]);

        return $db->lastInsertId();
    }

    public static function update(
        int $id,
        string $name,
        string $description,
        $price,
        $quantity,
        string $image,
        string $categories
    ): bool {
        $db   = Flight::get('db');
        $stmt = $db->prepare("
            UPDATE produits
            SET name = ?, description = ?, price = ?, quantity = ?, image = ?, categories = ?
            WHERE id = ?
        ");

        return $stmt->execute([$name, $description, $price, $quantity, $image, $categories, $id]);
    }

    public static function delete(int $id): bool
    {
        $db   = Flight::get('db');
        $stmt = $db->prepare("DELETE FROM produits WHERE id = ?");

        return $stmt->execute([$id]);
    }
}