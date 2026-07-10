<?php

class ProductController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Liste des produits
    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM produits");
        Flight::json($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // Ajouter
    public function create()
    {
        $data = Flight::request()->data;

        $sql = "
            INSERT INTO produits
            (name, description, price, quantity, image, categories)
            VALUES (?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $data->name,
            $data->description,
            $data->price,
            $data->quantity,
            $data->image,
            $data->categories
        ]);

        Flight::json([
            "message" => "Produit ajouté"
        ]);
    }

    // Supprimer
    public function delete($id)
    {
        $stmt = $this->db->prepare(
            "DELETE FROM produits WHERE id = ?"
        );

        $stmt->execute([$id]);

        Flight::json([
            "message" => "Produit supprimé"
        ]);
    }

    // Modifier
    public function update($id)
    {
        $data = Flight::request()->data;

        $sql = "
            UPDATE produits
            SET
            name=?,
            description=?,
            price=?,
            quantity=?,
            image=?,
            categories=?
            WHERE id=?
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $data->name,
            $data->description,
            $data->price,
            $data->quantity,
            $data->image,
            $data->categories,
            $id
        ]);

        Flight::json([
            "message" => "Produit modifié"
        ]);
    }
}