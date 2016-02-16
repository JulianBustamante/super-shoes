<?php

namespace SuperShoesBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * StoreRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StoreRepository extends EntityRepository
{
    public function findAllArticles($store_id)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT a FROM SupershoesBundle:Article a WHERE a.store_id = :store_id')
            ->setParameter('store_id', $store_id)
            ->getResult();
    }
}
