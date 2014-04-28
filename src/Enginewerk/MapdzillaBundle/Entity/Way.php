<?php

namespace Enginewerk\MapdzillaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Way
 *
 * @ORM\Entity
 * @ORM\Table(name="way")
 * @ORM\Entity(repositoryClass="Enginewerk\MapdzillaBundle\Entity\WayRepository")
 */
class Way
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(name="osm_way_id", type="integer", options={"unsigned"=true})
     * @var integer
     */
    protected $osmWayId;

    /**
     * @ORM\OneToMany(targetEntity="Node", mappedBy="way")
     */
    protected $nodes;

    /**
     * @ORM\OneToMany(targetEntity="Tag", mappedBy="way")
     */
    protected $tags;

    public function __construct()
    {
        $this->nodes = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

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
     * Set osmWayId
     *
     * @param  integer $osmWayId
     * @return Way
     */
    public function setOsmWayId($osmWayId)
    {
        $this->osmWayId = $osmWayId;

        return $this;
    }

    /**
     * Get osmWayId
     *
     * @return integer
     */
    public function getOsmWayId()
    {
        return $this->osmWayId;
    }

    /**
     * Add nodes
     *
     * @param  \Enginewerk\MapdzillaBundle\Entity\Node $nodes
     * @return Way
     */
    public function addNode(\Enginewerk\MapdzillaBundle\Entity\Node $nodes)
    {
        $this->nodes[] = $nodes;

        return $this;
    }

    /**
     * Remove nodes
     *
     * @param \Enginewerk\MapdzillaBundle\Entity\Node $nodes
     */
    public function removeNode(\Enginewerk\MapdzillaBundle\Entity\Node $nodes)
    {
        $this->nodes->removeElement($nodes);
    }

    /**
     * Get nodes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * Add tags
     *
     * @param  \Enginewerk\MapdzillaBundle\Entity\Tag $tags
     * @return Way
     */
    public function addTag(\Enginewerk\MapdzillaBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \Enginewerk\MapdzillaBundle\Entity\Tag $tags
     */
    public function removeTag(\Enginewerk\MapdzillaBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }
}
