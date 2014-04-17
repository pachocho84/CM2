<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Fan
 *
 * @ORM\Entity(repositoryClass="FanRepository")
 * @ORM\Table(name="fan")
 */
class Fan
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="from_user_id", type="integer")
     */
    private $fromUserId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="fanOf")
     * @ORM\JoinColumn(name="from_user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $fromUser;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="fans")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     **/
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="page_id", type="integer", nullable=true)
     */
    private $pageId;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="fans")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)    
     */
    private $page;

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
     * Get fromUserId
     *
     * @return integer 
     */
    public function getFromUserId()
    {
        return $this->fromUserId;
    }

    /**
     * Set fromUser
     *
     * @param User $fromUser
     * @return Request
     */
    public function setFromUser(User $fromUser)
    {
        $this->fromUser = $fromUser;
        $this->fromUserId = $fromUser->getId();
    
        return $this;
    }

    /**
     * Get fromUser
     *
     * @return \CM\UserBundle\Entity\User 
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Request
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        $this->userId = $user->getId();
    
        return $this;
    }

    /**
     * Get user
     *
     * @return User 
     */
    public function getUser()
    {
        return $this->user;
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
     * Set page
     *
     * @param Page $page
     * @return Image
     */
    public function setPage(Page $page = null)
    {
        $this->page = $page;
        $this->pageId = $page->getId();
    
        return $this;
    }

    /**
     * Get page
     *
     * @return Page 
     */
    public function getPage()
    {
        return $this->page;
    }
}
