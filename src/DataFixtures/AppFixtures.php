<?php

namespace App\DataFixtures;

use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Remplit la base avec les données de test : les 9 produit de la maquette et un compte de test.
 * Lancer par : symfony console doctrine:fixtures:load (vide la base avant de la remplir).
 */
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Pas de persist() ni de flush() : chaque factory enregistre elle même en base.

        // Les produits de la page d'accueil (noms, description courtes et prix repris de la maquette Figma).
        ProductFactory::createOne([
            'name' => "Kit d'hygiène recyclable",
            'shortDescription' => 'Pour une salle de bain éco-friendly',
            'fullDescription' => "Un kit complet pour la salle de bain, fait de matériaux recyclables. De quoi remplacer les accessoires jetables du quotidien.",
            'price' => 24.99,
            'picture' => 'kit-hygiene-recyclable.webp',
        ]);

        ProductFactory::createOne([
            'name' => 'Shot Tropical',
            'shortDescription' => 'Fruits frais, pressés à froid',
            'fullDescription' => "Un shot de fruits frais pressés à froid, pour garder un maximum de goût et de vitamines.",
            'price' => 4.50,
            'picture' => 'shot-tropical.webp',
        ]);

        ProductFactory::createOne([
            'name' => 'Gourde en bois',
            'shortDescription' => "50cl, bois d'olivier",
            'fullDescription' => "Une gourde de 50 cl à l'aspect bois d'olivier. Réutilisable, elle remplace les bouteilles en plastique.",
            'price' => 16.90,
            'picture' => 'gourde-en-bois.webp',
        ]);

        ProductFactory::createOne([
            'name' => 'Disques Démaquillants x3',
            'shortDescription' => 'Solution efficace pour vous démaquiller en douceur',
            'fullDescription' => "Trois disques démaquillants lavables et réutilisables, à la place des cotons jetables.",
            'price' => 19.90,
            'picture' => 'disques-demaquillants.webp',
        ]);

        ProductFactory::createOne([
            'name' => 'Bougie Lavande & Patchouli',
            'shortDescription' => 'Cire naturelle',
            'fullDescription' => "Une bougie en cire naturelle, parfumée à la lavande et au patchouli.",
            'price' => 32.00,
            'picture' => 'bougie-lavande-patchouli.webp',
        ]);

        ProductFactory::createOne([
            'name' => 'Brosse à dent',
            'shortDescription' => 'Bois de hêtre rouge issu de forêts gérées durablement',
            'fullDescription' => "Une brosse à dent en bois de hêtre rouge, issu de forêts gérées durablement. Une alternative aux brosses en plastique.",
            'price' => 5.40,
            'picture' => 'brosse-a-dent.webp',
        ]);

        ProductFactory::createOne([
            'name' => 'Kit couvert en bois',
            'shortDescription' => 'Revêtement Bio en olivier & sac de transport',
            'fullDescription' => "Un kit de couverts en bois avec son sac de transport, pour manger partout sans couverts jetables.",
            'price' => 12.30,
            'picture' => 'kit-couvert-en-bois.webp',
        ]);

        // Seul produit dont la maquette donne la description complete (page produit).
        ProductFactory::createOne([
            'name' => 'Nécessaire, déodorant Bio',
            'shortDescription' => "50ml déodorant à l'eucalyptus",
            'fullDescription' => "Déodorant Nécessaire, une formule révolutionnaire composée exclusivement d'ingrédients naturels pour une protection efficace et bienfaisante.\n\n"
                ."Chaque flacon de 50 ml renferme le secret d'une fraîcheur longue durée, sans compromettre votre bien-être ni l'environnement. Conçu avec soin, ce déodorant allie le pouvoir antibactérien des extraits de plantes aux vertus apaisantes des huiles essentielles, assurant une sensation de confort toute la journée.\n\n"
                ."Grâce à sa formule non irritante et respectueuse de votre peau, Nécessaire offre une alternative saine aux déodorants conventionnels, tout en préservant l'équilibre naturel de votre corps.",
            'price' => 8.50,
            'picture' => 'necessaire-deodorant-bio.webp',
        ]);

        ProductFactory::createOne([
            'name' => 'Savon Bio',
            'shortDescription' => 'Thé, Orange & Girofle',
            'fullDescription' => "Un savon bio parfumé au thé, à l'orange et au clou de girofle.",
            'price' => 18.90,
            'picture' => 'savon-bio.webp',
        ]);

        // Un compte connu pour se connecter pendant tests.
        // Mot de passe pas précisé : il reste "password", hacher par UserFactory.
        UserFactory::createOne([
            'email' => 'test@greengoodies.fr',
            'firstname' => 'Test',
            'lastname' => 'GreenGoodies',
        ]);
    }
}
