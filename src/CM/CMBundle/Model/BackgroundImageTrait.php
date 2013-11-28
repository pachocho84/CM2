<?php

namespace CM\CMBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

trait BackgroundImageTrait
{
    /**
     * @var string
     *
     * @ORM\Column(name="background_img", type="string", length=100, nullable=true)
     */
    private $backgroundImg;

    /**
     * @Assert\Image(
     *     minWidth = 1600,
     *     minHeight = 1000,
     *     maxSize = "8M",
     *     mimeTypes = {"image/png", "image/jpeg", "image/jpg"}
     * )
     */
    private $backgroundImgFile;

    /**
     * Set backgroundImg
     *
     * @param string $backgroundImg
     * @return User
     */
    public function setBackgroundImg($backgroundImg)
    {
        $this->backgroundImg = $backgroundImg;
    
        return $this;
    }

    /**
     * Get backgroundImg
     *
     * @return string 
     */
    public function getBackgroundImg()
    {
        return $this->backgroundImg;
    }

    /**
     * Set file.
     *
     * @param UploadedFile $file
     */
    public function setBackgroundImgFile(UploadedFile $file = null)
    {
        $this->backgroundImgFile = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getBackgroundImgFile()
    {
        return $this->backgroundImgFile;
    }

    public function getBackgroundAbsolutePath()
    {
        return is_null($this->backgroundImg) ? null : $this->getUploadRootDir().$this->backgroundImg;
    }
    
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function sanitizeBackgroundFileName()
    {
        if (null !== $this->getBackgroundImgFile()) {
            $fileName = md5(uniqid().$this->getBackgroundImgFile()->getClientOriginalName().time());
            $this->backgroundImg = $fileName.'.'.$this->getBackgroundImgFile()->guessExtension(); // FIXME: doesn't work with bmp files
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function uploadBackground()
    {
        if (is_null($this->getBackgroundImgFile())) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getBackgroundImgFile()->move($this->getUploadRootDir(), $this->backgroundImg);

        // clean up the file property as you won't need it anymore
        $this->backgroundImgFile = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeBackgroundUpload()
    {
        if ($file = $this->getBackgroundAbsolutePath()) {
            unlink($file);
        }
    }
}