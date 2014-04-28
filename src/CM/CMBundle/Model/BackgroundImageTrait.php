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
        $this->oldBackgroundImg = $this->backgroundImg;
        $this->setBackgroundImg(uniqid()); // trigger update
        
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
}