<?php

namespace App\Factory;

use App\Entity\Product;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * Le "moule" à produits : fabrique des objets Product et les enregistre en base.
 * Les vrais produits du site sont donnés un par un dans AppFixtures,
 * les valeurs ci-dessous ne servent que pour les champs non précisés.
 *
 * @extends PersistentProxyObjectFactory<Product>
 */
final class ProductFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()                       // rien à recevoir pour les produits
    {
    }

    #[\Override]
    public static function class(): string              // "quelle entité ce moule fabrique ?"
    {
        return Product::class;                          // des Product
    }

    /**
     * Valeurs par défaut, inventées par Faker.
     *
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->words(2, true),             // 2 mots inventés
            'shortDescription' => self::faker()->sentence(),      // une phrase courte
            'fullDescription' => self::faker()->paragraph(),      // un paragraphe
            'price' => self::faker()->randomFloat(2, 1, 50),     // prix entre 1 et 50 €, 2 chiffres après la virgule
            'picture' => 'produit.webp',                          // nom de fichier de la photo
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Product $product): void {})
        ;
    }
}
