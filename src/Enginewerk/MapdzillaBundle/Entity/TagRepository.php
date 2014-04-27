<?php

namespace Enginewerk\MapdzillaBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TagRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TagRepository extends EntityRepository
{
    public function findOneByWayAndKeyAndValue($way, $keyAttribute, $valueAttribute)
    {
        $queryBuilder = $this->createQueryBuilder('t');
        $queryBuilder
                ->where(
                    $queryBuilder->expr()->eq('t.way', ':way')
                )
                ->andWhere('t.key = :key')
                ->andWhere('t.value = :value')
                ->setParameter('way', $way)
                ->setParameter('key', $keyAttribute)
                ->setParameter('value', $valueAttribute)
                ;

        return $queryBuilder
                ->getQuery()
                ->getFirstResult();
    }
    
    public function findOneByNodeAndKeyAndValue($node, $keyAttribute, $valueAttribute)
    {
        $queryBuilder = $this->createQueryBuilder('t');
        $queryBuilder
                ->where(
                    $queryBuilder->expr()->eq('t.node', ':node')
                )
                ->andWhere('t.key = :key')
                ->andWhere('t.value = :value')
                ->setParameter('node', $node)
                ->setParameter('key', $keyAttribute)
                ->setParameter('value', $valueAttribute)
                ;

        return $queryBuilder
                ->getQuery()
                ->getFirstResult();
    }
}