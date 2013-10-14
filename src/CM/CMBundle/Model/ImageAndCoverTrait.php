<?php

namespace CM\CMBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

trait ImageAndCoverTrait
{
	use ImageTrait;

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
     * @Assert\Image(
     *     minWidth = 250,
     *     maxWidth = 750,
     *     minHeight = 250,
     *     maxHeight = 750,
     *     maxSize = "8M",
     *     mimeTypes = {"image/png", "image/jpeg", }
     * )
     */
    private $coverFile;

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
     * Set file.
     *
     * @param UploadedFile $file
     */
    public function setCoverFile(UploadedFile $file = null)
    {
        $this->coverFile = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getCoverFile()
    {
        return $this->coverFile;
    }    

    public function getCoverAbsolutePath()
    {
        return null === $this->coverImg
            ? null
            : $this->getUploadRootDir().'/'.$this->coverImg;
    }

    protected function getCoverRootDir()
    {
    	return $this->getRootDir();
    }

    protected static function getCoverUploadDir()
    {
   		// if you change this, change it also in the config.yml file!
        return 'uploads/images/full';
    }

    protected function getCoverUploadRootDir()
    {
        // the absolute directory path where uploaded images should be saved
        return $this->getRootDir().$this->getUploadDir();
    }

    /**
     * @ORM\PrePersist()
     */
    public function sanitizeCoverFileName()
    {
        if (null !== $this->getCoverFile()) {
        	$fileName = md5(uniqid().$this->getCoverFile()->getClientOriginalName().time());
            $this->coverImg = $fileName.'.'.$this->getCoverFile()->guessExtension(); // FIXME: doesn't work with bmp files
        }
    }

    /**
     * @ORM\PostPersist()
     */
    public function uploadCover()
    {
        if (is_null($this->getCoverFile())) {
        	return;
        }

        // if there is an error when moving the file, an exception will
		// be automatically thrown by move(). This will properly prevent
		// the entity from being persisted to the database on error
		$this->getCoverFile()->move($this->getUploadRootDir(), $this->coverImg);

		// clean up the file property as you won't need it anymore
		$this->coverFile = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeCoverUpload()
    {
        if ($file = $this->getCoverAbsolutePath()) {
            unlink($file);
        }
    }
}