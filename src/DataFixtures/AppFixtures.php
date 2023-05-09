<?php
// src\DataFixtures\AppFixtures.php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {


        // Création d'un user "normal"
        $user = new Client();
        $user->setEmail("client@bookapi.com");
        $user->setRoles(["ROLE_CLIENT"]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
        $manager->persist($user);

        // Création d'un user admin
        $userAdmin = new Client();
        $userAdmin->setEmail("admin@bookapi.com");
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "password"));
        $manager->persist($userAdmin);


        for ($i = 0; $i < 20; $i++) {
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