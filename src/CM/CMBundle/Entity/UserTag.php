<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * UserTag
 *
 * @ORM\Entity(repositoryClass="UserTagRepository")
 * @ORM\Table(name="user_tag")
 */
class UserTag
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
     * @var boolean
     *
     * @ORM\Column(name="is_user", type="boolean")
     */
    private $isUser;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_group", type="boolean")
     */
    private $isGroup;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_page", type="boolean")
     */
    private $isPage;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_protagonist", type="boolean")
     */
    private $isProtagonist;

    public function __call($method, $arguments)
    {
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }
    
    public function __toString()
    {
        return $this->getName();
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
     * Set isGroup
     *
     * @param boolean $isGroup
     * @return UserTag
     */
    public function setIsGroup($isGroup)
    {
        $this->isGroup = $isGroup;
    
        return $this;
    }

    /**
     * Get isGroup
     *
     * @return boolean 
     */
    public function getIsGroup()
    {
        return $this->isGroup;
    }

    /**
     * Set isPage
     *
     * @param boolean $isPage
     * @return UserTag
     */
    public function setIsPage($isPage)
    {
        $this->isPage = $isPage;
    
        return $this;
    }

    /**
     * Get isPage
     *
     * @return boolean 
     */
    public function getIsPage()
    {
        return $this->isPage;
    }

    /**
     * Set isProtagonist
     *
     * @param boolean $isProtagonist
     * @return UserTag
     */
    public function setIsProtagonist($isProtagonist)
    {
        $this->isProtagonist = $isProtagonist;
    
        return $this;
    }

    /**
     * Get isProtagonist
     *
     * @return boolean 
     */
    public function getIsProtagonist()
    {
        return $this->isProtagonist;
    }
}
