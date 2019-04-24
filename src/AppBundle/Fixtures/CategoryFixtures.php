<?php

namespace AppBundle\Fixtures;

use AppBundle\Entity\Category;
use AppBundle\Entity\Tag;
use AppBundle\Entity\Transaction;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < 6; ++$i) {
            $category = new Category();
            $category->setTitle('Category '.$i);
            $manager->persist($category);
            $this->addReference('Category '.$i, $category);
        }

        $manager->flush();
    }
}
