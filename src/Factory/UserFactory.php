<?php

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * Le "moule" à utilisateurs : fabrique des comptes de test.
 * Tous ont le mot de passe "password", haché comme lors d'une vraie inscription.
 *
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    // Le moule reçoit le service de hachage (le même que dans l'inscription).
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    #[\Override]
    public static function class(): string              // "quelle entité ce moule fabrique ?"
    {
        return User::class;                             // des User
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
            'email' => self::faker()->unique()->safeEmail(),     // unique : la colonne email n'accepte pas de doublon
            'firstname' => self::faker()->firstName(),            // ex. "Camille"
            'lastname' => self::faker()->lastName(),              // ex. "Lefèvre"
            'password' => 'password',                             // en clair ici, haché juste après
            'roles' => [],                                        // getRoles() ajoute toujours ROLE_USER
            'apiActive' => false,                                 // accès API désactivé par défaut, comme dans les specs
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        // Juste après la création de chaque utilisateur : on hache son mot de passe.
        // Sans ça, la connexion échouerait (Symfony compare des mots de passe hachés).
        return $this->afterInstantiate(function (User $user): void {
            $user->setPassword($this->hasher->hashPassword($user, $user->getPassword()));
        });
    }
}
