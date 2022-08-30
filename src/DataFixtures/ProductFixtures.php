<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for($i=0; $i <= mt_rand(8, 10); $i++)
        {
            $product = new Product();

            $product->setTitle($faker->sentence(3, false))
                    ->setImage($faker->imageUrl)
                    ->setPrice($faker->randomFloat(2, 10, 100));
            
            $manager->persist($product);
        }

        $manager->flush();
    }
}
