<?php

namespace Enginewerk\MapdzillaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tag
 *
 * @ORM\Entity
 * @ORM\Table(name="tag")
 * @ORM\Entity(repositoryClass="Enginewerk\MapdzillaBundle\Entity\TagRepository")
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=64, name="k")
     */
    protected $key;

    /**
     * @ORM\Column(type="string", length=64, name="v")
     */
    protected $value;

    /**
     * @ORM\ManyToOne(targetEntity="Node", inversedBy="tags")
     * @ORM\JoinColumn(name="node_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $node;

    /**
     * @ORM\ManyToOne(targetEntity="Way", inversedBy="tags")
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
     * Set key
     *
     * @param  string $key
     * @return Tag
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set value
     *
     * @param  string $value
     * @return Tag
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set node
     *
     * @param  \Enginewerk\MapdzillaBundle\Entity\Node $node
     * @return Tag
     */
    public function setNode(\Enginewerk\MapdzillaBundle\Entity\Node $node = null)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * Get node
     *
     * @return \Enginewerk\MapdzillaBundle\Entity\Node
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Set way
     *
     * @param  \Enginewerk\MapdzillaBundle\Entity\Way $way
     * @return Tag
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
