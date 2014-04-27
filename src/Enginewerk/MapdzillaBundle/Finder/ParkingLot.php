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
        $repositoryWays = $this->doctrine->getRepository('EnginewerkMapdzillaBundle:Way');
        /* @var $repositoryWays \Enginewerk\MapdzillaBundle\Entity\WayRepository */

        $ways = $repositoryWays->findAllJSON($lat, $lon, $radius);
        
        
        $repositoryNode = $this->doctrine->getRepository('EnginewerkMapdzillaBundle:Node');
        /* @var $repositoryNode \Enginewerk\MapdzillaBundle\Entity\NodeRepository */
        
        $nodes = $repositoryNode->findAllJSON($lat, $lon, $radius);
        
        $result = array();
        
        $template = array(
            'll' => array(),
            'capacity' => 0,
            'zone' => '-',
            'id' => 0
        );
        
        foreach ($ways as $way) {
            //$result[] = $this->formatWay($way, $template);
        }
        
        foreach ($nodes as $node) {
            $result[] = $this->formatNode($node, $template);
        }
        
        return $result;
    }
    
    protected function formatNode($node, $template)
    {
        $template['id'] = $node->getOSMNodeId();
        $template['capacity'] = rand(0, 69);
        $zone = array('A','B','-');
        $template['zone'] = $zone[rand(0, 2)];
        
        $template['ll'][] = array('lat' => $node->getLat(), 'lon' => $node->getLon());
        
        return $template;
    }
    
    protected function formatWay($way, $template)
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
