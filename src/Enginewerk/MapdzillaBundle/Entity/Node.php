<?php

namespace Enginewerk\MapdzillaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Node
 * 
 * @ORM\Entity
 * @ORM\Table(name="node")
 * @ORM\Entity(repositoryClass="Enginewerk\MapdzillaBundle\Entity\NodeRepository")
 */
class Node 
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var integer
     */
    protected $id;
    
    /**
     * @ORM\Column(name="osm_node_id", type="integer", options={"unsigned"=true})
     * @var integer
     */
    protected $osmNodeId;
    
    /**
     * @var decimal
     *
     * @ORM\Column(name="lat", type="float", scale=2, precision=7)
     */
    protected $lat;
    
    /**
     * @var decimal
     *
     * @ORM\Column(name="lon", type="float", scale=3, precision=7)
     */
    protected $lon;
    
    /**
     * @ORM\ManyToOne(targetEntity="Way", inversedBy="nodes")
     * @ORM\JoinColumn(name="way_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $way;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set osmNodeId
     *
     * @param integer $osmNodeId
     * @return Node
     */
    public function setOsmNodeId($osmNodeId)
    {
        $this->osmNodeId = $osmNodeId;

        return $this;
    }

    /**
     * Get osmNodeId
     *
     * @return integer 
     */
    public function getOsmNodeId()
    {
        return $this->osmNodeId;
    }

    /**
     * Set lat
     *
     * @param float $lat
     * @return Node
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return float 
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lon
     *
     * @param float $lon
     * @return Node
     */
    public function setLon($lon)
    {
        $this->lon = $lon;

        return $this;
    }

    /**
     * Get lon
     *
     * @return float 
     */
    public function getLon()
    {
        return $this->lon;
    }

    /**
     * Set way
     *
     * @param \Enginewerk\MapdzillaBundle\Entity\Way $way
     * @return Node
     */
    public function setWay(\Enginewerk\MapdzillaBundle\Entity\Way $way = null)
    {
        $this->way = $way;

        return $this;
    }

    /**
     * Get way
     *
     * @return \Enginewerk\MapdzillaBundle\Entity\Way 
     */
    public function getWay()
    {
        return $this->way;
    }
}
