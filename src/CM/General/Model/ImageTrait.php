<?php

namespace CM\General\Model;

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
     * @ORM\Column(name="img", type="string", length=100)
     */
    private $img;

    /**
     * @var integer
     *
     * @ORM\Column(name="offset", type="smallint", nullable=true)
     */
    private $offset;

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
    private $file;

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
     * Set offset
     *
     * @param integer $offset
     * @return Image
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    
        return $this;
    }

    /**
     * Get offset
     *
     * @return integer 
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Set file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }    

    public function getAbsolutePath()
    {
        return null === $this->img
            ? null
            : $this->getUploadRootDir().$this->img;
    }

    protected abstract function getRootDir(); // return __DIR__ or similar

    protected static function getImageDir()
    {
   		// if you change this, change it also in the config.yml file!
        return $this->getUploadDir().'full/';
    }
    protected static function getUploadDir()
    {
        // if you change this, change it also in the config.yml file!
        return 'uploads/images/';
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded images should be saved
        return $this->getRootDir().$this->getImageDir();
    }

    /**
     * @ORM\PrePersist()
     */
    public function sanitizeFileName()
    {
        if (null !== $this->getFile()) {
        	$fileName = md5(uniqid().$this->getFile()->getClientOriginalName().time());
            $this->img = $fileName.'.'.$this->getFile()->guessExtension(); // FIXME: doesn't work with bmp files
        }
    }

    /**
     * @ORM\PostPersist()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->img);

   		// clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }
}