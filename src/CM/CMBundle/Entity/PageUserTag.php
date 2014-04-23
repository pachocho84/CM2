<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

// TODO: reinsert unique constraint:
/*
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={
 *         "pageUser_id", "pageUser_tag_id"
 *     })}
 */
 
/**
 * PagePageTag
 *
 * @ORM\Entity
 * @ORM\Table(name="page_user_tag")
 */
class PageUserTag
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
     * @ORM\Column(name="page_user_id", type="integer")
     */
    private $pageUserId;

    /**
     * @ORM\ManyToOne(targetEntity="PageUser", inversedBy="pageUserTags")
     * @ORM\JoinColumn(name="page_user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $pageUser;

    /**
     * @ORM\Column(name="tag_id", type="integer")
     */
    private $tagId;
    
    /**
     * @ORM\ManyToOne(targetEntity="Tag", inversedBy="tagPageUsers")
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
     * Get pageUserId
     *
     * @return integer 
     */
    public function getPageUserId()
    {
        return $this->pageUserId;
    }

    /**
     * Set entity
     *
     * @param Page $entity
     * @return EntityPage
     */
    public function setPageUser(PageUser $pageUser = null)
    {
        $this->pageUser = $pageUser;
        $this->pageUserId = $pageUser->getId();
    
        return $this;
    }

    /**
     * Get entity
     *
     * @return Page 
     */
    public function getPageUser()
    {
        return $this->pageUser;
    }

    /**
     * Set entity
     *
     * @param PageTag $entity
     * @return PagePageTag
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
     * Get pageUserId
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
     * @return PageTag
     */
    public function getTag()
    {
        return $this->Tag;
    }

    /**
     * Set order
     *
     * @param integer $order
     * @return PagePageTag
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