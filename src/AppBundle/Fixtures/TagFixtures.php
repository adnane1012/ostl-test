<?php

namespace AppBundle\Fixtures;

use AppBundle\Entity\Category;
use AppBundle\Entity\Tag;
use AppBundle\Entity\Transaction;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < 6; ++$i) {
            $tag = new Tag();
            $tag->setName('Tag '.$i);
            $manager->persist($tag);
            $this->addReference('Tag '.$i, $tag);
        }

        $manager->flush();
    }
}
