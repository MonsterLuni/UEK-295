<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFakeFixtures extends Fixture implements FixtureGroupInterface
{
    protected $faker;

    public function __construct(private UserPasswordHasherInterface $passwordHasher){

    }

    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();

            $userentity = new User();
            $userentity->setUsername("TestUser");
            $hashedPassword = $this->passwordHasher->hashPassword($userentity, "1234");
            $userentity->setPassword($hashedPassword);
            $userentity->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
            $manager->persist($userentity);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['fakedata'];
    }
}
