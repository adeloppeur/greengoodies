<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Un produit du catalogue GreenGoodies.
 *
 * Créée avec make:entity Product. Les noms des champs reprennent exactement
 * le schéma Product des spécifications de l'API (GET /api/products).
 * Pas de création des produits dans ce projet : ils viennent des fixtures.
 */
#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    // Clé primaire, numérotée automatiquement par la base.
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    // Description courte, affichée sous chaque produit de la page d'accueil.
    #[ORM\Column(length: 255)]
    private ?string $shortDescription = null;

    // Description longue de la page produit : type text car elle peut dépasser 255 caractères.
    #[ORM\Column(type: Types::TEXT)]
    private ?string $fullDescription = null;

    // Prix en euro, en float comme le demande le schéma de l'API.
    #[ORM\Column]
    private ?float $price = null;

    // Nom du fichier image (ex. gourde.webp), pas l'image elle-même.
    #[ORM\Column(length: 255)]
    private ?string $picture = null;

    // Getters / setters générés par make:entity : formulaires, Twig et Doctrine passent par eux.
    // Les propriétés sont private : ces méthodes sont les seules portes pour les lire ou les modifier.
    // ?string = un texte ou null (vide tant que le produit n'est pas rempli).
    // static + return $this = on peut enchaîner : $product->setName('Gourde')->setPrice(16.90);

    public function getId(): ?int                       // "donne-moi le numéro du produit"
    {
        return $this->id;                               // pas de setId() : c'est la base qui numérote
    }

    public function getName(): ?string                  // "donne-moi le nom"
    {
        return $this->name;                             // renvoie la valeur rangée dans l'objet
    }

    public function setName(string $name): static       // "range ce nom"
    {
        $this->name = $name;                            // on range la valeur reçue dans l'objet

        return $this;                                   // renvoie l'objet pour enchaîner
    }

    public function getShortDescription(): ?string      // "donne-moi la description courte"
    {
        return $this->shortDescription;                 // renvoie la valeur rangée dans l'objet
    }

    public function setShortDescription(string $shortDescription): static  // "range cette description courte"
    {
        $this->shortDescription = $shortDescription;    // on range la valeur reçue dans l'objet

        return $this;                                   // renvoie l'objet pour enchaîner
    }

    public function getFullDescription(): ?string       // "donne-moi la description longue"
    {
        return $this->fullDescription;                  // renvoie la valeur rangée dans l'objet
    }

    public function setFullDescription(string $fullDescription): static  // "range cette description longue"
    {
        $this->fullDescription = $fullDescription;      // on range la valeur reçue dans l'objet

        return $this;                                   // renvoie l'objet pour enchaîner
    }

    public function getPrice(): ?float                  // "donne-moi le prix"
    {
        return $this->price;                            // renvoie la valeur rangée dans l'objet
    }

    public function setPrice(float $price): static      // "range ce prix"
    {
        $this->price = $price;                          // on range la valeur reçue dans l'objet

        return $this;                                   // renvoie l'objet pour enchaîner
    }

    public function getPicture(): ?string               // "donne-moi le nom du fichier image"
    {
        return $this->picture;                          // renvoie la valeur rangée dans l'objet
    }

    public function setPicture(string $picture): static // "range ce nom de fichier image"
    {
        $this->picture = $picture;                      // on range la valeur reçue dans l'objet

        return $this;                                   // renvoie l'objet pour enchaîner
    }
}
