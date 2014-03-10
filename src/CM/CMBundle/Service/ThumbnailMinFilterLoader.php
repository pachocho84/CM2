<?php

namespace CM\CMBundle\Service;

use Imagine\Filter\Basic\Thumbnail;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;

class ThumbnailMinFilterLoader implements LoaderInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ImageInterface $image, array $options = array())
    {
        $mode = ImageInterface::THUMBNAIL_OUTBOUND;
        if (!empty($options['mode']) && 'inset' === $options['mode']) {
            $mode = ImageInterface::THUMBNAIL_INSET;
        }

        $size = $options['size'];

        $imageSize = $image->getSize();
        $origWidth = $imageSize->getWidth();
        $origHeight = $imageSize->getHeight();

        if ($origWidth / $origHeight < 1) {
        	$width = $size;
        	$height = ceil(($size / $origWidth) * $origHeight);
        } else {
        	$width = ceil(($size / $origHeight) * $origWidth);
        	$height = $size;
        }

        if (($origWidth > $width || $origHeight > $height)
            || (!empty($options['allow_upscale']) && ($origWidth !== $width || $origHeight !== $height))
        ) {
            $filter = new Thumbnail(new Box($width, $height), $mode);
            $image = $filter->apply($image);
        }

        return $image;
    }
}
