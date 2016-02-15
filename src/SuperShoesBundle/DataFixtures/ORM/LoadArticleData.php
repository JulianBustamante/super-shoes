<?php

namespace SuperShoesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SuperShoesBundle\Entity\Article;

class LoadArticleData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $article1 = new Article();
        $article1->setName('Green Shoes');
        $article1->setDescription('The best quality of shoes in a green color');
        $article1->setPrice(20.15);
        $article1->setTotalInShelf(25);
        $article1->setTotalInVault(40);
        $article1->setStore($this->getReference('store1'));

        $manager->persist($article1);
        $manager->flush();

        $this->addReference('article1', $article1);
    }

    public function getOrder()
    {
        return 2;
    }
}
