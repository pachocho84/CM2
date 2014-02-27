<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Education
 *
 * @ORM\Entity(repositoryClass="CM\CMBundle\Entity\EducationRepository")
 * @ORM\Table(name="education")
 */
class Education
{
    const MARK_SCALE_10 = 0;
    const MARK_SCALE_30 = 1;
    const MARK_SCALE_60 = 2;
    const MARK_SCALE_100 = 3;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="school", type="string", length=150, nullable=false)
     */
    private $school;

    /**
     * @var string
     *
     * @ORM\Column(name="course", type="string", length=150, nullable=true)
     */
    private $course;

    /**
     * @var string
     *
     * @ORM\Column(name="teacher", type="string", length=100, nullable=true)
     */
    private $teacher;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_from", type="date", nullable=true)
     */
    private $dateFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_to", type="date", nullable=true)
     */
    private $dateTo;

    /**
     * @var integer
     *
     * @ORM\Column(name="mark", type="smallint", nullable=true)
     */
    private $mark;

    /**
     * @var integer
     *
     * @ORM\Column(name="mark_scale", type="smallint", nullable=true)
     */
    private $markScale;

    /**
     * @var boolean
     *
     * @ORM\Column(name="laude", type="boolean", nullable=true)
     */
    private $laude;

    /**
     * @var boolean
     *
     * @ORM\Column(name="honour", type="boolean", nullable=true)
     */
    private $honour;

    /**
     * @var integer
     *
     * @ORM\Column(name="course_type", type="smallint", nullable=true)
     */
    private $courseType;

    /**
     * @var integer
     *
     * @ORM\Column(name="degree_type", type="smallint", nullable=true)
     */
    private $degreeType;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     **/
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="relationsIncoming")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $user;

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
     * Set school
     *
     * @param string $school
     * @return Education
     */
    public function setSchool($school)
    {
        $this->school = $school;
    
        return $this;
    }

    /**
     * Get school
     *
     * @return string 
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * Set course
     *
     * @param string $course
     * @return Education
     */
    public function setCourse($course)
    {
        $this->course = $course;
    
        return $this;
    }

    /**
     * Get course
     *
     * @return string 
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * Set teacher
     *
     * @param string $teacher
     * @return Education
     */
    public function setTeacher($teacher)
    {
        $this->teacher = $teacher;
    
        return $this;
    }

    /**
     * Get teacher
     *
     * @return string 
     */
    public function getTeacher()
    {
        return $this->teacher;
    }

    /**
     * Set dateFrom
     *
     * @param \DateTime $dateFrom
     * @return Education
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;
    
        return $this;
    }

    /**
     * Get dateFrom
     *
     * @return \DateTime 
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * Set dateTo
     *
     * @param \DateTime $dateTo
     * @return Education
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = $dateTo;
    
        return $this;
    }

    /**
     * Get dateTo
     *
     * @return \DateTime 
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * Set mark
     *
     * @param integer $mark
     * @return Education
     */
    public function setMark($mark)
    {
        $this->mark = $mark;
    
        return $this;
    }

    /**
     * Get mark
     *
     * @return integer 
     */
    public function getMark()
    {
        return $this->mark;
    }

    /**
     * Set markScale
     *
     * @param integer $markScale
     * @return Education
     */
    public function setMarkScale($markScale)
    {
        $this->markScale = $markScale;
    
        return $this;
    }

    /**
     * Get markScale
     *
     * @return integer 
     */
    public function getMarkScale()
    {
        return $this->markScale;
    }

    /**
     * Set laude
     *
     * @param boolean $laude
     * @return Education
     */
    public function setLaude($laude)
    {
        $this->laude = $laude;
    
        return $this;
    }

    /**
     * Get laude
     *
     * @return boolean 
     */
    public function getLaude()
    {
        return $this->laude;
    }

    /**
     * Set honour
     *
     * @param boolean $honour
     * @return Education
     */
    public function setHonour($honour)
    {
        $this->honour = $honour;
    
        return $this;
    }

    /**
     * Get honour
     *
     * @return boolean 
     */
    public function getHonour()
    {
        return $this->honour;
    }

    /**
     * Set courseType
     *
     * @param integer $courseType
     * @return Education
     */
    public function setCourseType($courseType)
    {
        $this->courseType = $courseType;
    
        return $this;
    }

    /**
     * Get courseType
     *
     * @return integer 
     */
    public function getCourseType()
    {
        return $this->courseType;
    }

    /**
     * Set degreeType
     *
     * @param integer $degreeType
     * @return Education
     */
    public function setDegreeType($degreeType)
    {
        $this->degreeType = $degreeType;
    
        return $this;
    }

    /**
     * Get degreeType
     *
     * @return integer 
     */
    public function getDegreeType()
    {
        return $this->degreeType;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Education
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get user
     *
     * @return User 
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
}
