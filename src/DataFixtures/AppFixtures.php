<?php
// src\DataFixtures\AppFixtures.php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $userAdmin = new Client();
        $userAdmin->setEmail("admin@bilemo.com");
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "password"));
        $manager->persist($userAdmin);


        for ($i = 1; $i <= 5; $i++) {
            $client = new Client();
            $client->setEmail($faker->email);
            $client->setRoles(["ROLE_CLIENT"]);
            $client->setPassword($this->userPasswordHasher->hashPassword($client, "password"));
            $manager->persist($client);

            for ($j = 1; $j <= 10; $j++) {
                $user = new User();
                $user->setFirstname($faker->firstName);
                $user->setLastname($faker->lastName);
                $client->addUser($user);
                $manager->persist($user);
            }

        }


        for ($i = 0; $i < 50; $i++) {
            $product = new Product();
            $product->setName("Product $i");
            $product->setBrand("Brand $i");
            $product->setPrice(rand(10, 100));
            $product->setDescription("Description $i");
            $product->setPicture("https://picsum.photos/200/300");
            $product->setScreenSize(rand(10, 100));
            $product->setColor("Color $i");
            $product->setStorageCapacity(rand(10, 100));
            $manager->persist($product);
        }

        $manager->flush();
    }
}