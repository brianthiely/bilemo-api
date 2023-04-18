<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Nelmio\Alice\Loader\NativeLoader;
use Nelmio\Alice\Throwable\LoadingThrowable;

class ProductFixtures extends Fixture
{
    /**
     * @throws LoadingThrowable
     */
    public function load(ObjectManager $manager): void
    {
        $loader = new NativeLoader();
        $objects = $loader->loadFile(__DIR__.'/product.yml');

        foreach ($objects->getObjects() as $object) {
            $manager->persist($object);
        }

        $manager->flush();
    }
}
