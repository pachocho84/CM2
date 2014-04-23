<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * UserTag
 *
 * @ORM\Entity(repositoryClass="TagRepository")
 * @ORM\Table(name="tag")
 */
class Tag
{
    use ORMBehaviors\Translatable\Translatable;

    const TYPE_USER = 1;
    const TYPE_PAGE = 2;
    const TYPE_PAGE_USER = 4;
    const TYPE_ENTITY = 8;
    const TYPE_ENTITY_USER = 16;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean")
     */
    private $visible;

    /**
     * @ORM\OneToMany(targetEntity="UserTag", mappedBy="tag", cascade={"persist", "remove"})
     */
    protected $tagUsers;

    /**
     * @ORM\OneToMany(targetEntity="PageTag", mappedBy="tag", cascade={"persist", "remove"})
     */
    protected $tagPages;

    /**
     * @ORM\OneToMany(targetEntity="PageUserTag", mappedBy="tag", cascade={"persist", "remove"})
     */
    protected $tagPageUsers;

    /**
     * @ORM\OneToMany(targetEntity="EntityUserTag", mappedBy="tag", cascade={"persist", "remove"})
     */
    protected $tagEntityUsers;

    /**
     * @var boolean
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

    public function __construct()
    {
        $this->tagUsers = new ArrayCollection;
        $this->tagPages = new ArrayCollection;
        $this->tagPageUsers = new ArrayCollection;
        $this->tagEntityUsers = new ArrayCollection;
    }

    public function __call($method, $arguments)
    {
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }
    
    public function __toString()
    {
        return $this->proxyCurrentLocaleTranslation('__toString');
    }

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
     * Set visible
     *
     * @param boolean $visible
     * @return UserTag
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    
        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * @param \CM\CMBundle\Entity\Entity\EntityUser $comment
     * @return Entity
     */
    public function addTagUser(UserTag $userTag)
    {
        if (!$this->userTags->contains($userTag)) {
            $this->userTags[] = $userTag;
            $userTag->setUser($this);
        }
    
        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\Entity\EntityUser $users
     */
    public function removeUserTag(UserTag $userTag)
    {
        $this->userTags->removeElement($userTag);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserTags()
    {
        return $this->userTags;
    }

    /**
     * @param \CM\CMBundle\Entity\Entity\EntityPage $comment
     * @return Entity
     */
    public function addTagPage(PageTag $pageTag)
    {
        if (!$this->pageTags->contains($pageTag)) {
            $this->pageTags[] = $pageTag;
            $pageTag->setPage($this);
        }
    
        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\Entity\EntityPage $pages
     */
    public function removePageTag(PageTag $pageTag)
    {
        $this->pageTags->removeElement($pageTag);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPageTags()
    {
        return $this->pageTags;
    }

    /**
     * @param \CM\CMBundle\Entity\Entity\EntityPageUser $comment
     * @return Entity
     */
    public function addTagPageUser(PageUserTag $pageUserTag)
    {
        if (!$this->pageUserTags->contains($pageUserTag)) {
            $this->pageUserTags[] = $pageUserTag;
            $pageUserTag->setPageUser($this);
        }
    
        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\Entity\EntityPageUser $pageUsers
     */
    public function removePageUserTag(PageUserTag $pageUserTag)
    {
        $this->pageUserTags->removeElement($pageUserTag);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPageUserTags()
    {
        return $this->pageUserTags;
    }

    /**
     * @param \CM\CMBundle\Entity\Entity\EntityEntityUser $comment
     * @return Entity
     */
    public function addTagEntityUser(EntityUserTag $entityUserTag)
    {
        if (!$this->entityUserTags->contains($entityUserTag)) {
            $this->entityUserTags[] = $entityUserTag;
            $entityUserTag->setEntityUser($this);
        }
    
        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\Entity\EntityEntityUser $entityUsers
     */
    public function removeEntityUserTag(EntityUserTag $entityUserTag)
    {
        $this->entityUserTags->removeElement($entityUserTag);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEntityUserTags()
    {
        return $this->entityUserTags;
    }

    /**
     * Set isUser
     *
     * @param boolean $isUser
     * @return UserTag
     */
    public function setType($type)
    {
        if (is_array($type)) {
            $type = array_reduce($type, function($a, $b) { return $a | $b; });
        }
        $this->type = $type;
    
        return $this;
    }

    /**
     * Set isUser
     *
     * @param boolean $isUser
     * @return UserTag
     */
    public function addType($type)
    {
        $this->type |= $type;
    
        return $this;
    }

    /**
     * Set isUser
     *
     * @param boolean $isUser
     * @return UserTag
     */
    public function removeType($type)
    {
        $this->type = $this->type && (-$type);
    
        return $this;
    }

    /**
     * Set isUser
     *
     * @param boolean $isUser
     * @return UserTag
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get isUser
     *
     * @return boolean 
     */
    public function isUser()
    {
        return $this->type & self::TYPE_USER;
    }

    /**
     * Get isPage
     *
     * @return boolean 
     */
    public function isPage()
    {
        return $this->type & self::TYPE_PAGE;
    }

    /**
     * Get isPage
     *
     * @return boolean 
     */
    public function isPageUser()
    {
        return $this->type & self::TYPE_PAGE_USER;
    }

    /**
     * Get isPage
     *
     * @return boolean 
     */
    public function isEntity()
    {
        return $this->type & self::TYPE_ENTITY;
    }

    /**
     * Get isPage
     *
     * @return boolean 
     */
    public function isEntityUser()
    {
        return $this->type & self::TYPE_ENTITY_USER;
    }
}
