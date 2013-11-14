<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Biography
 *
 * @ORM\Table(name="biography")
 * @ORM\Entity
 */
class Biography extends Entity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\OneToOne(targetEntity="User", inversedBy="biography")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)    
     */
    private $user;

    public function __construct()
    {
        parent::__construct();
        
        $this->setTitle('bio');
    }

    public function __toString()
    {
        return is_null($this->getText()) ? '' : $this->getText();
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
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set entity
     *
     * @param Entity $entity
     * @return Image
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
        $this->userId = $user->getId();
    
        return $this;
    }

    /**
     * Get entity
     *
     * @return Entity 
     */
    public function getUser()
    {
        return $this->user;
    }
}
