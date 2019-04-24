<?php

namespace AppBundle\Fixtures;

use AppBundle\Entity\Category;
use AppBundle\Entity\Tag;
use AppBundle\Entity\Transaction;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;

class TransactionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < 50; ++$i) {
            $transaction = new Transaction();
            $transaction->setTitle('Transaction '.$i);
            $transaction->setDescription('Transaction '.$i.' Description');
            $transaction->setAmount(rand(100, 1000) / 10);
            $transaction->setIsInput((bool) rand(0, 1));
            $transaction->setIsValid((bool) rand(0, 1));
            $transaction->setCategory($this->getReference('Category '.random_int(1, 5)));
            $rightNow = new \DateTime();
            $transaction->setCreatedAt($rightNow);
            $transaction->setUpdatedAt($rightNow);
            $tags = new ArrayCollection();

            $tags->add($this->getReference('Tag '.random_int(1, 3)));
            $tags->add($this->getReference('Tag '.random_int(4, 5)));

            $transaction->setTags($tags);
            $manager->persist($transaction);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            TagFixtures::class,
            CategoryFixtures::class,
        );
    }
}
