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
            'distance' => 0,
            'zone' => '-'
        );

        $waysRegister = array();

        foreach ($nodes as $node) {

            $template['distance'] = round($node['distance'], 3);
            
            if (!isset($node['way_id']) || $node['way_id'] == null) {
                $result[] = $this->formatArrayNode($node, $template);
            } else {
                if (!isset($waysRegister[$node['way_id']])) {
                    $way = $this->getWayRepository()->find($node['way_id']);
                    $result[] = $this->formatWay($way, $template);
                    $waysRegister[$node['way_id']] = $node['way_id'];
                }
            }
        }

        return $result;
    }
    
    protected function formatArrayNode($node, $template)
    {
        $tags = $this->getTagRepository()->findByNode($node['id']);

        foreach ($tags as $tag) {
            if ($tag->getKey() != 'amenity') {
                $template[$tag->getKey()] = $tag->getValue();
            }
        }

        $template['ll'][] = array('lat' => $node['lat'], 'lon' => $node['lon']);

        return $template;
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
        $tags = $way->getTags();

        foreach ($tags as $tag) {
            if ($tag->getKey() != 'amenity') {
                $template[$tag->getKey()] = $tag->getValue();
            }
        }

        foreach ($way->getNodes() as $node) {
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
    
    /**
     *
     * @return \Enginewerk\MapdzillaBundle\Entity\WayRepository
     */
    protected function getWayRepository()
    {
        return $this
                ->doctrine
                ->getRepository('EnginewerkMapdzillaBundle:Way');
    }
    
    /**
     *
     * @return \Enginewerk\MapdzillaBundle\Entity\TagRepository
     */
    protected function getTagRepository()
    {
        return $this
                ->doctrine
                ->getRepository('EnginewerkMapdzillaBundle:Tag');
    }
}
