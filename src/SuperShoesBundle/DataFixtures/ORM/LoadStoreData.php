<?php

namespace SuperShoesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SuperShoesBundle\Entity\Store;

class LoadStoreData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $store1 = new Store();
        $store1->setName('Super Store');
        $store1->setAddress('Somewhere over the rainbow');

        $manager->persist($store1);
        $manager->flush();

        $this->addReference('store1', $store1);
    }

    public function getOrder()
    {
        return 1;
    }
}
