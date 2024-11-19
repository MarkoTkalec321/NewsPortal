<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const TECHNOLOGY_CATEGORY_REFERENCE = 'category-technology';
    public const FASHION_CATEGORY_REFERENCE = 'category-fashion';
    public const TRAVEL_CATEGORY_REFERENCE = 'category-travel';

    public function load(ObjectManager $manager): void
    {

        $categoryTechnology  = new Category();
        $categoryTechnology->setName("Technology");
        $manager->persist($categoryTechnology);
        $this->addReference(self::TECHNOLOGY_CATEGORY_REFERENCE, $categoryTechnology);

        $categoryFashion = new Category();
        $categoryFashion->setName("Fashion");
        $manager->persist($categoryFashion);
        $this->addReference(self::FASHION_CATEGORY_REFERENCE, $categoryFashion);

        $categoryTravel = new Category();
        $categoryTravel->setName("Travel");
        $manager->persist($categoryTravel);
        $this->addReference(self::TRAVEL_CATEGORY_REFERENCE, $categoryTravel);

        $manager->flush();
    }
}