<?php

class User
{
    public static function listAll(): array
    {
        $db   = Flight::get('db');
        $stmt = $db->prepare("
            SELECT id, pseudo, email, role, date_creation
            FROM utilisateurs
            ORDER BY date_creation DESC
        ");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function changeRole(int $id, string $newRole): bool
    {
        $db   = Flight::get('db');
        $stmt = $db->prepare("UPDATE utilisateurs SET role = ? WHERE id = ?");

        return $stmt->execute([$newRole, $id]);
    }

    public static function delete(int $id): bool
    {
        $db   = Flight::get('db');
        $stmt = $db->prepare("DELETE FROM utilisateurs WHERE id = ?");

        return $stmt->execute([$id]);
    }

    public static function findByEmail(string $email): array|false
    {
        $db   = Flight::get('db');
        $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findById(int $id): array|false
    {
        $db   = Flight::get('db');
        $stmt = $db->prepare("
            SELECT id, pseudo, email, role, date_creation
            FROM utilisateurs
            WHERE id = ?
        ");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create(string $username, string $email, string $passwordHash): string
    {
        $db   = Flight::get('db');
        $stmt = $db->prepare("
            INSERT INTO utilisateurs (pseudo, email, mot_de_passe)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$username, $email, $passwordHash]);

        return $db->lastInsertId();
    }
}