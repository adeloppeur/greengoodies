<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Une ligne d'une commande : un produit, sa quantité et son prix au moment de l'achat.
 *
 * Exemple : la commande 12 contient 2 lignes (1 gourde + 2 shots tropicaux).
 */
#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    // Clé primaire, numérotée automatiquement par la base.
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // La commande à laquelle appartient la ligne ("order" est refusé par le Maker : mot réservé).
    // ManyToOne : plusieurs lignes pour une commande. Crée la colonne parent_order_id.
    // onDelete: 'CASCADE' = si la commande est supprimée, la base supprime aussi ses lignes.
    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Order $parentOrder = null;

    // Le produit commandé. Crée la colonne product_id.
    // Pas de propriété en face dans Product : on n'a jamais besoin de partir d'un produit pour trouver ses commandes.
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column]
    private ?int $quantity = null;

    // Prix du produit recopié au moment de l'ajout au panier :
    // si le prix du produit change plus tard, les anciennes commandes gardent le bon montant.
    #[ORM\Column]
    private ?float $unitPrice = null;

    // Getters / setters générés par make:entity : formulaires, Twig et Doctrine passent par eux.

    public function getId(): ?int                       // "donne-moi le numéro de la ligne"
    {
        return $this->id;                               // pas de setId() : c'est la base qui numérote
    }

    public function getParentOrder(): ?Order            // "donne-moi la commande de cette ligne"
    {
        return $this->parentOrder;                      // renvoie l'objet Order
    }

    public function setParentOrder(?Order $parentOrder): static  // "range cette commande"
    {
        $this->parentOrder = $parentOrder;              // on range la commande reçue dans l'objet

        return $this;                                   // renvoie l'objet pour enchaîner
    }

    public function getProduct(): ?Product              // "donne-moi le produit de cette ligne"
    {
        return $this->product;                          // renvoie l'objet Product (nom, photo...)
    }

    public function setProduct(?Product $product): static  // "range ce produit"
    {
        $this->product = $product;                      // on range le produit reçu dans l'objet

        return $this;                                   // renvoie l'objet pour enchaîner
    }

    public function getQuantity(): ?int                 // "donne-moi la quantité"
    {
        return $this->quantity;                         // renvoie la valeur rangée dans l'objet
    }

    public function setQuantity(int $quantity): static  // "range cette quantité"
    {
        $this->quantity = $quantity;                    // on range la valeur reçue dans l'objet

        return $this;                                   // renvoie l'objet pour enchaîner
    }

    public function getUnitPrice(): ?float              // "donne-moi le prix unitaire"
    {
        return $this->unitPrice;                        // le prix payé, pas forcément le prix actuel du produit
    }

    public function setUnitPrice(float $unitPrice): static  // "range ce prix unitaire"
    {
        $this->unitPrice = $unitPrice;                  // on range la valeur reçue dans l'objet

        return $this;                                   // renvoie l'objet pour enchaîner
    }

    // Ajouté à la main : sous-total de la ligne, affiché dans le panier ("prix total").
    public function getTotal(): float                   // "combien coûte cette ligne ?"
    {
        return $this->quantity * $this->unitPrice;      // quantité x prix unitaire
    }
}
