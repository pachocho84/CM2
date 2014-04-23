<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

// TODO: reinsert unique constraint:
/*
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={
 *         "page_id", "page_tag_id"
 *     })}
 */
 
/**
 * PagePageTag
 *
 * @ORM\Entity
 * @ORM\Table(name="page_tag")
 */
class PageTag
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
     * @ORM\Column(name="page_id", type="integer")
     */
    private $pageId;

    /**
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="pageTags")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $page;

    /**
     * @ORM\Column(name="tag_id", type="integer")
     */
    private $tagId;
    
    /**
     * @ORM\ManyToOne(targetEntity="Tag", inversedBy="tagPages")
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
     * Get pageId
     *
     * @return integer 
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * Set entity
     *
     * @param Page $entity
     * @return EntityPage
     */
    public function setPage(Page $page = null)
    {
        $this->page = $page;
        $this->pageId = $page->getId();
    
        return $this;
    }

    /**
     * Get entity
     *
     * @return Page 
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set entity
     *
     * @param PageTag $entity
     * @return PagePageTag
     */
    public function setTag(Tag $tag = null)
    {
        if (!$tag->isPage()) throw new \Exception("Not a page tag (id: ".$tag->getId().')', 1);
        $this->tag = $tag;
        if (!is_null($tag)) {
            $this->tagId = $tag->getId();
        }
    
        return $this;
    }

    /**
     * Get pageId
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