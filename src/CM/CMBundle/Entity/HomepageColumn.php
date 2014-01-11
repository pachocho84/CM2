<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * HomepageColumn
 *
 * @ORM\Entity(repositoryClass="HomepageColumnRepository")
 * @ORM\Table(name="homepage_column")
 */
class HomepageColumn
{
    use ORMBehaviors\Timestampable\Timestampable;

    const TYPE_ARTICLE = 0;
    const TYPE_BOX = 1;

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
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="`order`", type="integer")
     */
    private $order;

    /**
     * @ORM\Column(name="row_id", type="integer")
     */
    private $rowId;

    /**
     * @ORM\ManyToOne(targetEntity="HomepageRow", inversedBy="columns")
     * @ORM\JoinColumn(name="row_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $row;

    /**
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $user;

    /**
     * @ORM\Column(name="archive_id", type="integer", nullable=true)
     */
    private $archiveId;

    /**
     * @ORM\ManyToOne(targetEntity="HomepageArchive")
     * @ORM\JoinColumn(name="archive_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     **/
    private $archive;

    /**
     * @ORM\Column(name="box_id", type="integer", nullable=true)
     */
    private $boxId;

    /**
     * @ORM\ManyToOne(targetEntity="HomepageBox")
     * @ORM\JoinColumn(name="box_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     **/
    private $box;

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
     * Set type
     *
     * @param integer $type
     * @return HomepageColumn
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set order
     *
     * @param integer $order
     * @return HomepageColumn
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

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getRowId()
    {
        return $this->rowId;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Like
     */
    public function setRow(HomepageRow $row)
    {
        $this->row = $row;
        if (!is_null($row)) {
            $this->rowId = $row->getId();
        }
    
        return $this;
    }

    /**
     * Get user
     *
     * @return User 
     */
    public function getRow()
    {
        return $this->row;
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
     * @return Like
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        if (!is_null($user)) {
            $this->userId = $user->getId();
        }
    
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
     * Get userId
     *
     * @return integer 
     */
    public function getArchiveId()
    {
        return $this->archiveId;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Like
     */
    public function setArchive(HomepageArchive $archive)
    {
        $this->archive = $archive;
        if (!is_null($archive)) {
            $this->archiveId = $archive->getId();
        }
    
        return $this;
    }

    /**
     * Get user
     *
     * @return User 
     */
    public function getArchive()
    {
        return $this->archive;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getBoxId()
    {
        return $this->boxId;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Like
     */
    public function setBox(HomepageBox $box)
    {
        $this->box = $box;
        if (!is_null($box)) {
            $this->boxId = $box->getId();
        }
    
        return $this;
    }

    /**
     * Get user
     *
     * @return User 
     */
    public function getBox()
    {
        return $this->box;
    }
}
