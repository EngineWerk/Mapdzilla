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
        $repositoryNode = $this->doctrine->getRepository('EnginewerkMapdzillaBundle:Node');
        /* @var $repositoryNode \Enginewerk\MapdzillaBundle\Entity\NodeRepository */
        
        $nodes = $repositoryNode->findByLatLonRadius($lat, $lon, $radius);
        
        $result = array();
        
        $template = array(
            'll' => array(),
//            'capacity' => 0,
            'zone' => '-',
            'id' => 0
        );
        
        $waysRegister = array();
        
        foreach ($nodes as $node) {
            //var_dump($nodes);die();
            $template['distance'] = $node->getId();
            if ($node->getWay() == null) {
                $result[] = $this->formatNode($node, $template);
            } else {
                if (!isset($waysRegister[$node->getWay()->getId()])) {
                    $result[] = $this->formatWay($node->getWay(), $template);
                    $waysRegister[$node->getWay()->getId()] = $node->getWay()->getId();
                }
            }
        }
        
        return $result;
    }
    
    protected function formatNode($node, $template)
    {
        $template['id'] = $node->getOSMNodeId();
        
        $tags = $node->getTags();
        
        foreach ($tags as $tag) {
            if ($tag->getKey() != 'amenity') {
                $template[$tag->getKey()] = $tag->getValue();
            }
        }
        
        $template['ll'][] = array('lat' => $node->getLat(), 'lon' => $node->getLon());
        
        return $template;
    }
    
    protected function formatWay($way, $template)
    {
        $template['id'] = $way->getOsmWayId();
        /*$template['capacity'] = rand(0, 69);
        $zone = array('A','B','-');
        //$template['zone'] = $zone[rand(0, 2)];*/
        
        $tags = $way->getTags();
        
        foreach ($tags as $tag) {
            if ($tag->getKey() != 'amenity') {
                $template[$tag->getKey()] = $tag->getValue();
            }
        }
        
        foreach($way->getNodes() as $node) {
            $template['ll'][] = array('lat' => $node->getLat(), 'lon' => $node->getLon());
        }
        
        return $template;
    }
    
    public function getWithDescription()
    {
        return $this->getNodeRepository()->findWithNoWays();
    }
    
    public function getWithNoDescription()
    {
        return $this->getNodeRepository()->findWithWays();
    }
    
    /**
     * 
     * @return \Enginewerk\MapdzillaBundle\Entity\NodeRepository
     */
    protected function getNodeRepository()
    {
        return $this
                ->doctrine
                ->getRepository('EnginewerkMapdzillaBundle:Node');
    }
}
