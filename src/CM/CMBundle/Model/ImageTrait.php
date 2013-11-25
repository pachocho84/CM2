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
    private $img = "default.png";

    /**
     * @var integer
     *
     * @ORM\Column(name="img_offset", type="smallint", nullable=true)
     */
    private $imgOffset;

    /**
     * @Assert\Image(
     *     minWidth = 150,
     *     maxWidth = 5000,
     *     minHeight = 150,
     *     maxHeight = 5000,
     *     maxSize = "8M",
     *     mimeTypes = {"image/png", "image/jpeg", }
     * )
     */
    private $imgFile;

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
        $this->setImg($this->img.'.old'); // trigger update
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

    protected abstract function getRootDir(); // return __DIR__ or similar

    protected static function getUploadDir()
    {
        // if you change this, change it also in the config.yml file!
        return 'uploads/images/';
    }

    protected static function getImageDir()
    {
        // if you change this, change it also in the config.yml file!
        return self::getUploadDir().'full/';
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded images should be saved
        return $this->getRootDir().$this->getImageDir();
    }

    public function getAbsolutePath()
    {
        return is_null($this->img) ? null : $this->getUploadRootDir().$this->img;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function sanitizeFileName()
    {
        if (!is_null($this->getImgFile())) {
            $fileName = md5(uniqid().$this->getImgFile()->getClientOriginalName().time());
            $this->img = $fileName.'.'.$this->getImgFile()->guessExtension(); // FIXME: doesn't work with bmp files
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (is_null($this->getImgFile())) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getImgFile()->move($this->getUploadRootDir(), $this->img);

        // clean up the file property as you won't need it anymore
        $this->imgFile = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($imgFile = $this->getAbsolutePath()) {
            unlink($imgFile);
        }
    }
}