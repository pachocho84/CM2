<?php

namespace CM\CMBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

trait AudioTrait
{
    /**
     * @var string
     *
     * @ORM\Column(name="audio", type="string", length=100, nullable=true)
     */
    private $audio;

    /**
     * @var integer
     *
     * @ORM\Column(name="extract", type="boolean")
     */
    private $extract = false;

    /**
     */
    private $audioFile;

    /**
     * Set audio
     *
     * @param string $audio
     * @return Image
     */
    public function setAudio($audio)
    {
        $this->audio = $audio;
    
        return $this;
    }

    /**
     * Get audio
     *
     * @return string 
     */
    public function getAudio()
    {
        return empty($this->audio) ? '' : $this->audio;
    }

    /**
     * Set extract
     *
     * @param integer $extract
     * @return Image
     */
    public function setExtract($extract)
    {
        $this->extract = $extract;
    
        return $this;
    }

    /**
     * Get extract
     *
     * @return integer 
     */
    public function getExtract()
    {
        return $this->extract;
    }

    /**
     * Set audioFile.
     *
     * @param UploadedFile $audioFile
     */
    public function setAudioFile(UploadedFile $audioFile)
    {
        $this->audioFile = $audioFile;
        $this->setAudio($this->audio.'.old'); // trigger update
    }

    /**
     * Get audioFile.
     *
     * @return UploadedFile
     */
    public function getAudioFile()
    {
        return $this->audioFile;
    }

    protected static function getImageDir()
    {
        // if you change this, change it also in the config.yml file!
        return 'uploads/audio/';
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded images should be saved
        return $this->getRootDir().$this->getImageDir();
    }

    public function getAudioAbsolutePath()
    {
        return is_null($this->audio) ? null : $this->getUploadRootDir().$this->audio;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function sanitizeFileName()
    {
        if (!is_null($this->getAudioFile())) {
            $fileName = md5(uniqid().$this->getAudioFile()->getClientOriginalName().time());
            $this->audio = $fileName.'.'.$this->getAudioFile()->guessExtension(); // FIXME: doesn't work with bmp files
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (is_null($this->getAudioFile())) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getAudioFile()->move($this->getUploadRootDir(), $this->audio);

        // clean up the file property as you won't need it anymore
        $this->audioFile = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($audioFile = $this->getAbsolutePath()) {
            unlink($audioFile);
        }
    }
}