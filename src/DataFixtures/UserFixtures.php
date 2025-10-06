<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    public const ADMIN = 'ADMIN_USER';

    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {

    }
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user
            ->setRoles(['ROLE_ADMIN'])
            ->setEmail('admin@doe.fr')
            ->setIsVerified(true)
            ->setUsername('admin')
            ->setPassword($this->hasher->hashPassword($user, 'admin'))
            ->setApiToken('admin_token');
        $this->addReference(self::ADMIN, $user);
        $manager->persist($user);

        for($i = 0; $i <= 10; $i++){
            $user = new User();
            $user
                ->setRoles([])
                ->setEmail("user{$i}@test.fr")
                ->setIsVerified(true)
                ->setUsername("user{$i}")
                ->setPassword($this->hasher->hashPassword($user, '0000'))
                ->setApiToken("user{$i}");
            $this->addReference('USER' . $i, $user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
