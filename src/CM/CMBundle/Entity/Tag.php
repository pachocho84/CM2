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
     * @ORM\Column(name="is_user", type="boolean")
     */
    private $isUser;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_page_user", type="boolean")
     */
    private $isPageUser;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_entity_user", type="boolean")
     */
    private $isEntityUser;

    public function __construct()
    {
        $this->tagUsers = new ArrayCollection;
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
    public function setIsUser($isUser)
    {
        $this->isUser = $isUser;
    
        return $this;
    }

    /**
     * Get isUser
     *
     * @return boolean 
     */
    public function getIsUser()
    {
        return $this->isUser;
    }

    /**
     * Set isPage
     *
     * @param boolean $isPage
     * @return UserTag
     */
    public function setIsPageUser($isPageUser)
    {
        $this->isPageUser = $isPageUser;
    
        return $this;
    }

    /**
     * Get isPage
     *
     * @return boolean 
     */
    public function getIsPageUser()
    {
        return $this->isPageUser;
    }

    /**
     * Set isEntityUser
     *
     * @param boolean $isEntityUser
     * @return UserTag
     */
    public function setIsEntityUser($isEntityUser)
    {
        $this->isEntityUser = $isEntityUser;
    
        return $this;
    }

    /**
     * Get isEntityUser
     *
     * @return boolean 
     */
    public function getIsEntityUser()
    {
        return $this->isEntityUser;
    }
}
