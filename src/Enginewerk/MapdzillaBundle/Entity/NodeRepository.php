<?php

namespace Enginewerk\MapdzillaBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * NodeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NodeRepository extends EntityRepository
{
    public function findWithNoWays()
    {
        $queryBuilder = $this->createQueryBuilder('n');
        $queryBuilder->where('n.way IS NULL');
        
        return $queryBuilder
                ->getQuery()
                ->getResult();
    }
    
    public function findWithWays()
    {
        $queryBuilder = $this->createQueryBuilder('n');
        $queryBuilder
                ->where('n.way IS NOT NULL')
                ->groupBy('n.way');
        
        return $queryBuilder
                ->getQuery()
                ->getResult();
    }
    
    
    public function findByLatLonRadius($lat, $lng, $radius)
    {
        // Constants related to the surface of the Earth
        $earths_radius = 6371;
        $surface_distance_coeffient = 111.320;

        // Spherical Law of Cosines
        $distance_formula = ":earths_radius * ACOS( SIN(RADIANS(lat)) * SIN(RADIANS(:lat)) + COS(RADIANS(lon - :lng)) * COS(RADIANS(lat)) * COS(RADIANS(:lat)) )";

        // Create a bounding box to reduce the scope of our search
        $lng_b1 = $lng - $radius / abs(cos(deg2rad($lat)) * $surface_distance_coeffient);
        $lng_b2 = $lng + $radius / abs(cos(deg2rad($lat)) * $surface_distance_coeffient);
        $lat_b1 = $lat - $radius / $surface_distance_coeffient;
        $lat_b2 = $lat + $radius / $surface_distance_coeffient;

        $rsm = new ResultSetMapping();
        // build rsm here

        // Construct our sql statement
        $sql = 
        "SELECT *, ($distance_formula) AS distance
        FROM node
        WHERE (lat BETWEEN :lat_b1 AND :lat_b2) AND (lon BETWEEN :lng_b1 AND :lng_b2)
        HAVING distance < :radius
        ORDER BY distance ASC";
        
        $rsm->addEntityResult('Enginewerk\MapdzillaBundle\Entity\Node', 'n');
        $rsm->addFieldResult('n', 'id', 'id');
        $rsm->addFieldResult('n', 'lat', 'lat');
        $rsm->addMetaResult('n', 'way_id', 'way_id');
        $rsm->addFieldResult('n', 'lon', 'lon');

        $em = $this->getEntityManager();
        $query = $em->createNativeQuery($sql, $rsm);
        
        $parameters = array(
            'earths_radius' => $earths_radius,
            'lat' => $lat,
            'lng' => $lng,
            'lat_b1' => $lat_b1,
            'lat_b2' => $lat_b2,
            'lng_b1' => $lng_b1,
            'lng_b2' => $lng_b2,
            'radius' => $radius
        );

        $query->setParameters($parameters);

        return $query->getResult();
    }
}
