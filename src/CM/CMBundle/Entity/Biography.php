<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Biography
 *
 * @ORM\Entity(repositoryClass="BiographyRepository")
 * @ORM\Table(name="biography")
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

    public function __construct()
    {
        parent::__construct();
        
        $this->setVisible(true);
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
}
