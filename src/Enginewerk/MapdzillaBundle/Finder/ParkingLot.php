<?php

namespace Enginewerk\MapdzillaBundle\Finder;

use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Description of ParkingLot
 *
 * @author pczyzewski
 */
class ParkingLot 
{
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    
    public function find($lat, $lon, $radius) 
    {
        /** @var $repository \Enginewerk\MapdzillaBundle\Entity\WayRepository **/
        $repositoryWays = $this->doctrine->getRepository('EnginewerkMapdzillaBundle:Way');
        //$result = $repository->findAllJSON($lat, $lon, $radius);
        $ways = $repositoryWays->findAllJSON($lat, $lon, $radius);
        /*
        $repositoryNode = $this->doctrine->getRepository('EnginewerkMapdzillaBundle:Node');
        $nodes = $repository->findAll();*/
        
        $result = array();
        
        $template = array(
            'll' => array(),
            'capacity' => 0,
            'zone' => '-',
            'id' => 0
        );
        
        foreach ($ways as $way) {
            $result[] = $this->format($way, $template);
        }
        
        return $result;
    }
    
    protected function format($way, $template)
    {
        $template['id'] = $way->getOsmWayId();
        $template['capacity'] = rand(0, 69);
        $zone = array('A','B','-');
        $template['zone'] = $zone[rand(0, 2)];
        
        foreach($way->getNodes() as $node) {
            $template['ll'][] = array('lat' => $node->getLat(), 'lon' => $node->getLon());
        }
        
        return $template;
    }
}
