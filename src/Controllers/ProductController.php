<?php

require_once __DIR__ . '/../Models/Product.php';

class ProductController
{
    // Liste des produits
    public function getAll()
    {
        Flight::json(Product::listAll());
    }

    // Ajouter
    public function create()
    {
        $data = Flight::request()->data;

        $id = Product::create(
            $data->name,
            $data->description,
            $data->price,
            $data->quantity,
            $data->image,
            $data->categories
        );

        Flight::json([
            "message" => "Produit ajouté",
            "id"      => $id
        ]);
    }

    // Supprimer
    public function delete($id)
    {
        Product::delete($id);

        Flight::json([
            "message" => "Produit supprimé"
        ]);
    }

    // Modifier
    public function update($id)
    {
        $data = Flight::request()->data;

        Product::update(
            $id,
            $data->name,
            $data->description,
            $data->price,
            $data->quantity,
            $data->image,
            $data->categories
        );

        Flight::json([
            "message" => "Produit modifié"
        ]);
    }
}