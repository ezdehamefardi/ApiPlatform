<?php

namespace App\Factory;

use App\Constant\UserRoles;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use function Zenstruck\Foundry\lazy;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    private UserPasswordHasherInterface $hasher;
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->hasher = $encoder;
    }

    public static function class(): string
    {
        return User::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'email' => self::faker()->unique()->safeEmail(),
            'name' => self::faker()->text(100),
            'password' => $this->hasher->hashPassword(new User(), self::faker()->password(5, 20)),
            'roles' => [self::faker()->randomElement([UserRoles::ROLE_USER, UserRoles::ROLE_SUPER_ADMIN, UserRoles::ROLE_SUPER_ADMIN])],
            'company' => lazy(fn() => CompanyFactory::randomOrCreate())
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(User $user): void {})
        ;
    }
}
