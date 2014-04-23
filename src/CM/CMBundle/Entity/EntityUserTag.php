<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

// TODO: reinsert unique constraint:
/*
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={
 *         "entity_id", "entity_tag_id"
 *     })}
 */
 
/**
 * EntityEntityTag
 *
 * @ORM\Entity
 * @ORM\Table(name="entity_user_tag")
 */
class EntityUserTag
{     
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="entity_user_id", type="integer")
     */
    private $entityUserId;

    /**
     * @ORM\ManyToOne(targetEntity="EntityUser", inversedBy="entityUserTags")
     * @ORM\JoinColumn(name="entity_user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $entityUser;

    /**
     * @ORM\Column(name="tag_id", type="integer")
     */
    private $tagId;
    
    /**
     * @ORM\ManyToOne(targetEntity="Tag", inversedBy="tagEntityUsers")
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $tag;

    /**
     * @var integer
     *
     * @ORM\Column(name="`order`", type="smallint", nullable=true)
     */
    private $order;

    public static function className()
    {
        return get_class();
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
     * Get entityId
     *
     * @return integer 
     */
    public function getEntityUserId()
    {
        return $this->entityUserId;
    }

    /**
     * Set entity
     *
     * @param Entity $entity
     * @return EntityEntity
     */
    public function setEntityUser(EntityUser $entityUser = null)
    {
        $this->entityUser = $entityUser;
        if (!is_null($entityUser)) {
            $this->entityUserId = $entityUser->getId();
        }
    
        return $this;
    }

    /**
     * Get entity
     *
     * @return Entity 
     */
    public function getEntityUser()
    {
        return $this->entityUser;
    }

    /**
     * Set entity
     *
     * @param EntityTag $entity
     * @return EntityEntityTag
     */
    public function setTag(Tag $tag = null)
    {
        if (!$tag->isUser()) throw new \Exception("Not a user tag (id: ".$tag->getId().')', 1);
        $this->tag = $tag;
        if (!is_null($tag)) {
            $this->tagId = $tag->getId();
        }
    
        return $this;
    }

    /**
     * Get entityId
     *
     * @return integer 
     */
    public function getTagId()
    {
        return $this->tagId;
    }

    /**
     * Get entity
     *
     * @return EntityTag
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set order
     *
     * @param integer $order
     * @return EntityEntityTag
     */
    public function setOrder($order)
    {
        $this->order = $order;
    
        return $this;
    }

    /**
     * Get order
     *
     * @return integer 
     */
    public function getOrder()
    {
        return $this->order;
    }
}