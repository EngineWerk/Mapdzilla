<?php

namespace Enginewerk\MapdzillaBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * NodeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class WayRepository extends EntityRepository
{
    public function findAllJSON($lat, $lon, $radius)
    {
        $queryBuilder = $this->createQueryBuilder('s');
        /*$queryBuilder
                ->select('s.website AS name')
                ->where(
                    $queryBuilder->expr()->like('s.website', ':website')
                )
                ->setParameter('website', '%' . $term . '%')
                ->orderBy('s.website', 'ASC');*/

        return $queryBuilder
                ->getQuery()
                ->getArrayResult();
    }

}