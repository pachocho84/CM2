<?php

namespace CM\CMBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

trait ImageTrait
{
    public static $thumbnails = array(
        array('dir' => '70', 'width' => 70, 'height' => 175),
        array('dir' => '170', 'width' => 170, 'height' => 425),
        array('dir' => '370', 'width' => 370, 'height' => 925),
        array('dir' => '770', 'width' => 770, 'height' => 1925),
        array('dir' => 'full', 'width' => null, 'height' => 5000)
    );

    /**
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=100, nullable=true)
     */
    private $img;

    private $oldImg;

    /**
     * @var integer
     *
     * @ORM\Column(name="img_offset", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $imgOffset;

    /**
     * @Assert\Image(
     *     minWidth = 150,
     *     maxWidth = 5000,
     *     minHeight = 150,
     *     maxHeight = 5000,
     *     maxSize = "8M",
     *     mimeTypes = {"image/png", "image/jpeg", "image/jpg"}
     * )
     */
    private $imgFile;

    public function __toString()
    {
        return $this->img;
    }

    public static function defaultImg()
    {
        return 'default.png';
    }

    /**
     * Set img
     *
     * @param string $img
     * @return Image
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
     * Get img
     *
     * @return string 
     */
    public function getOldImg()
    {
        return $this->oldImg;
    }

    /**
     * Set imgOffset
     *
     * @param integer $imgOffset
     * @return Image
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
     * Set imgFile.
     *
     * @param UploadedFile $imgFile
     */
    public function setImgFile(UploadedFile $imgFile)
    {
        $this->imgFile = $imgFile;
        $this->oldImg = $this->img;
        $this->setImg(uniqid()); // trigger update
    }

    /**
     * Get imgFile.
     *
     * @return UploadedFile
     */
    public function getImgFile()
    {
        return $this->imgFile;
    }
}