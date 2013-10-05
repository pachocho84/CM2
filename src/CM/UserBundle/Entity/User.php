<?php

namespace CM\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use CM\CMBundle\Entity\Post;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="CM\UserBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseUser
{
	const SEX_M = true;
	const SEX_F = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=50, nullable=false)
     * @Assert\NotBlank(message="Please enter your name.", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=2,
     *     max="50",
     *     minMessage="The name is too short.",
     *     maxMessage="The name is too long.",
     *     groups={"Registration", "Profile"}
     * )
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=50, nullable=false)
     * @Assert\NotBlank(message="Please enter your last name.", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=2,
     *     max="50",
     *     minMessage="The last name is too short.",
     *     maxMessage="The last name is too long.",
     *     groups={"Registration", "Profile"}
     * )
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=100)
     */
    private $img;

    /**
     * @var integer
     *
     * @ORM\Column(name="img_offset", type="smallint", nullable=true)
     */
    private $imgOffset;

    /**
     * @var string
     *
     * @ORM\Column(name="cover_img", type="string", length=100, nullable=true)
     */
    private $coverImg;

    /**
     * @var integer
     *
     * @ORM\Column(name="cover_img_offset", type="smallint", nullable=true)
     */
    private $coverImgOffset;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sex", type="boolean", nullable=false)
     * @Assert\Choice(choices = {User::SEX_M, User::SEX_F}, message = "Choose a valid gender.")
     */
    private $sex;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birth_date", type="date", nullable=false)
     * @Assert\NotBlank
     * @Assert\Date
     */
    private $birthDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="birth_date_visible", type="boolean")
     * @Assert\Choice(choices = {true, false})
     */
    private $birthDateVisible;

    /**
     * @var string
     *
     * @ORM\Column(name="city_birth", type="string", length=50)
     * @Assert\NotBlank
     */
    private $cityBirth;

    /**
     * @var string
     *
     * @ORM\Column(name="city_current", type="string", length=50)
     * @Assert\NotBlank
     */
    private $cityCurrent;

    /**
     * @var boolean
     *
     * @ORM\Column(name="newsletter", type="boolean")
     * @Assert\Choice(choices = {true, false})
     */
    private $newsletter = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vip", type="boolean")
     * @Assert\Choice(choices = {true, false})
     */
    private $vip = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="notify_email", type="boolean")
     * @Assert\Choice(choices = {true, false})
     */
    private $notifyEmail = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="request_email", type="boolean")
     * @Assert\Choice(choices = {true, false})
     */
    private $requestEmail = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="message_email", type="boolean")
     * @Assert\Choice(choices = {true, false})
     */
    private $messageEmail = true;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;
        
    /**
     * @ORM\OneToMany(targetEntity="CM\CMBundle\Entity\Post", mappedBy="user", cascade={"persist", "remove"})
	 */
	private $posts;

    /**
     * @Assert\NotBlank
     * @Assert\Image(
     *     minWidth = 250,
     *     maxWidth = 750,
     *     minHeight = 250,
     *     maxHeight = 750,
     *     maxSize = "8M",
     *     mimeTypes = {"image/png", "image/jpeg", }
     * )
     */
    private $imgFile;

	public function __construct()
	{
		parent::__construct();
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
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    
        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    
        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set img
     *
     * @param string $img
     * @return User
     */
    public function setImg($img)
    {
        $this->img = $img;
    
        return $this;
    }

    /**
     * Get img
     *
     * @return string 
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * Set imgOffset
     *
     * @param integer $imgOffset
     * @return User
     */
    public function setImgOffset($imgOffset)
    {
        $this->imgOffset = $imgOffset;
    
        return $this;
    }

    /**
     * Get imgOffset
     *
     * @return integer 
     */
    public function getImgOffset()
    {
        return $this->imgOffset;
    }

    /**
     * Set coverImg
     *
     * @param string $coverImg
     * @return User
     */
    public function setCoverImg($coverImg)
    {
        $this->coverImg = $coverImg;
    
        return $this;
    }

    /**
     * Get coverImg
     *
     * @return string 
     */
    public function getCoverImg()
    {
        return $this->coverImg;
    }

    /**
     * Set coverImgOffset
     *
     * @param integer $coverImgOffset
     * @return User
     */
    public function setCoverImgOffset($coverImgOffset)
    {
        $this->coverImgOffset = $coverImgOffset;
    
        return $this;
    }

    /**
     * Get coverImgOffset
     *
     * @return integer 
     */
    public function getCoverImgOffset()
    {
        return $this->coverImgOffset;
    }

    /**
     * Set sex
     *
     * @param boolean $sex
     * @return User
     */
    public function setSex($sex)
    {
        if (!in_array($status, array(self::SEX_M, self::SEX_F))) {
            throw new \InvalidArgumentException("Invalid sex");
        }

        $this->sex = $sex;
            
        return $this;
    }

    /**
     * Get sex
     *
     * @return boolean 
     */
    public function getSex()
    {
        return $this->sex ? 'M' : 'F';
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     * @return User
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
    
        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime 
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set birthDateVisible
     *
     * @param boolean $birthDateVisible
     * @return User
     */
    public function setBirthDateVisible($birthDateVisible)
    {
        $this->birthDateVisible = $birthDateVisible;
    
        return $this;
    }

    /**
     * Get birthDateVisible
     *
     * @return boolean 
     */
    public function getBirthDateVisible()
    {
        return $this->birthDateVisible;
    }

    /**
     * Set cityBirth
     *
     * @param string $cityBirth
     * @return User
     */
    public function setCityBirth($cityBirth)
    {
        $this->cityBirth = $cityBirth;
    
        return $this;
    }

    /**
     * Get cityBirth
     *
     * @return string 
     */
    public function getCityBirth()
    {
        return $this->cityBirth;
    }

    /**
     * Set cityCurrent
     *
     * @param string $cityCurrent
     * @return User
     */
    public function setCityCurrent($cityCurrent)
    {
        $this->cityCurrent = $cityCurrent;
    
        return $this;
    }

    /**
     * Get cityCurrent
     *
     * @return string 
     */
    public function getCityCurrent()
    {
        return $this->cityCurrent;
    }

    /**
     * Set newsletter
     *
     * @param boolean $newsletter
     * @return User
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;
    
        return $this;
    }

    /**
     * Get newsletter
     *
     * @return boolean 
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * Set vip
     *
     * @param boolean $vip
     * @return User
     */
    public function setVip($vip)
    {
        $this->vip = $vip;
    
        return $this;
    }

    /**
     * Get vip
     *
     * @return boolean 
     */
    public function getVip()
    {
        return $this->vip;
    }

    /**
     * Set notifyEmail
     *
     * @param boolean $notifyEmail
     * @return User
     */
    public function setNotifyEmail($notifyEmail)
    {
        $this->notifyEmail = $notifyEmail;
    
        return $this;
    }

    /**
     * Get notifyEmail
     *
     * @return boolean 
     */
    public function getNotifyEmail()
    {
        return $this->notifyEmail;
    }

    /**
     * Set requestEmail
     *
     * @param boolean $requestEmail
     * @return User
     */
    public function setRequestEmail($requestEmail)
    {
        $this->requestEmail = $requestEmail;
    
        return $this;
    }

    /**
     * Get requestEmail
     *
     * @return boolean 
     */
    public function getRequestEmail()
    {
        return $this->requestEmail;
    }

    /**
     * Set messageEmail
     *
     * @param boolean $messageEmail
     * @return User
     */
    public function setMessageEmail($messageEmail)
    {
        $this->messageEmail = $messageEmail;
    
        return $this;
    }

    /**
     * Get messageEmail
     *
     * @return boolean 
     */
    public function getMessageEmail()
    {
        return $this->messageEmail;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return User
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    
        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes()
    {
        return $this->notes;
    }
    
    /**
     * Set file.
     *
     * @param UploadedFile $file
     */
    public function setImgFile(UploadedFile $file = null)
    {
        $this->imgFile = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getImgFile()
    {
        return $this->imgFile;
    }    

    public function getImgAbsolutePath()
    {
        return null === $this->img
            ? null
            : $this->getUploadRootDir().'/'.$this->img;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded images should be saved
        return __DIR__.'/../Resources/public/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
   		// if you change this, change it also in the config.yml file!
        return 'uploads/images/full';
    }

    /**
     * @ORM\PrePersist()
     */
    public function sanitizeImgFileName()
    {
        if (null !== $this->getImgFile()) {
        	$fileName = md5($this->getImgFile()->getClientOriginalName().time().uniqid());
            $this->img = $fileName.'.'.$this->getImgFile()->guessExtension(); // FIXME: doesn't work with bmp files
        }
    }

    /**
     * @ORM\PostPersist()
     */
    public function upload()
    {
        if (null === $this->getImgFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getImgFile()->move($this->getUploadRootDir(), $this->img);

   		// clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getImgAbsolutePath()) {
            unlink($file);
        }
    }

    /**
     * @param \CM\CMBundle\Entity\Image $images
     * @return Entity
     */
    public function addPost(Post $post)
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }
    
        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\Image $images
     * @return Entity
     */
    public function addPosts(ArrayCollection $posts)
    {
    	foreach ($images->toArray()['posts'] as $post) {
    		$this->addPost($post);
    	}
    
        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\Image $images
     */
    public function removePost(Post $post)
    {
        $this->posts->removeElement($post);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPosts()
    {
        return $this->posts;
    }
}
