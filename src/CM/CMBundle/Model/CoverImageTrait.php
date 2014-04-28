<?php

namespace CM\CMBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

trait CoverImageTrait
{
    /**
     * @var string
     *
     * @ORM\Column(name="cover_img", type="string", length=100, nullable=true)
     */
    private $coverImg;

    /**
     * @var integer
     *
     * @ORM\Column(name="cover_img_offset", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $coverImgOffset;

    /**
     * @Assert\Image(
     *     minWidth = 900,
     *     maxWidth = 1000,
     *     minHeight = 250,
     *     maxHeight = 750,
     *     maxSize = "8M",
     *     mimeTypes = {"image/png", "image/jpeg", "image/jpg"}
     * )
     */
    private $coverImgFile;

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
    public function setCoverImgFile(UploadedFile $file = null)
    {
        $this->coverImgFile = $file;
        $this->oldCoverImg = $this->coverImg;
        $this->setCoverImg(uniqid()); // trigger update
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getCoverImgFile()
    {
        return $this->coverImgFile;
    }
}