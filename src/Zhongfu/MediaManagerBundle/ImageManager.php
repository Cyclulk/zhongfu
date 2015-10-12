<?php
/**
 * Service for making images miniatures and generating valid filenames
 */

namespace Zhongfu\MediaManagerBundle;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\DependencyInjection\ContainerAware;
use Zhongfu\BlogBundle\Entity\Image;


class ImageManager extends ContainerAware
{

    const ACCEPTED_IMAGE_EXTENTIONS = "png.jpg.jpeg.gif";

    private $sUploadDir;

    public function __construct($upload_dir)
    {
        $this->sUploadDir = $upload_dir;
    }


    /**
     * @param $sImagePath
     * Make a miniature from an image, need an image path, accept jpg/png/gif/jpeg
     */
    public function makeMiniature($sImagePath, $iHeight = 150)
    {

        $sExtension = strtolower(substr(strrchr($sImagePath, '.'), 1));
        $sFileName = substr(strrchr($sImagePath, '/'), 1);
        $sFilePath = str_replace($sFileName, "", $sImagePath);

        $aAcceptedExtentions = explode(".", $this::ACCEPTED_IMAGE_EXTENTIONS);
        if (!in_array($sExtension, $aAcceptedExtentions)) {
            throw new \Exception("Invalid image extention " . __FILE__ . " at line " . __LINE__);
        }

        //Getting the image from the given path
        if ($sExtension == "jpg" || $sExtension == "jpeg") {
            $oExtractedImage = imagecreatefromjpeg($sImagePath);
        }
        if ($sExtension == "png") {
            $oExtractedImage = imagecreatefrompng($sImagePath);
        }
        if ($sExtension == "gif") {
            $oExtractedImage = imagecreatefromgif($sImagePath);
        }

        if ($oExtractedImage == false) {
            throw new \Exception("Could not create mini image in " . __FILE__ . " at line " . __LINE__);
        }

        //calcul of the width with the good aspect ratio
        $iSourceWidth = imagesx($oExtractedImage);
        $iSourceHeight = imagesy($oExtractedImage);
        $iWidth = ($iSourceWidth * $iHeight) / $iSourceHeight;

        //Create the miniature
        $oMiniature = imagecreatetruecolor($iWidth, $iHeight);

        //fixing transparency for png
        if ($sExtension == "png") {
            imagealphablending($oMiniature, false);
            imagesavealpha($oMiniature, true);
        }

        imagecopyresampled($oMiniature, $oExtractedImage, 0, 0, 0, 0, $iWidth, $iHeight, $iSourceWidth, $iSourceHeight);

        if ($sExtension == "jpg" || $sExtension == "jpeg") {
            imagejpeg($oMiniature, $sFilePath . "mini_" . $sFileName);
        }
        if ($sExtension == "png") {
            imagepng($oMiniature, $sFilePath . "mini_" . $sFileName);
        }
        if ($sExtension == "gif") {
            imagegif($oMiniature, $sFilePath . "mini_" . $sFileName);
        }
    }

    public function resize($sImagePath, $iHeight = 600)
    {

        $sExtension = strtolower(substr(strrchr($sImagePath, '.'), 1));
        $sFileName = substr(strrchr($sImagePath, '/'), 1);
        $sFilePath = str_replace($sFileName, "", $sImagePath);

        $aAcceptedExtentions = explode(".", $this::ACCEPTED_IMAGE_EXTENTIONS);
        if (!in_array($sExtension, $aAcceptedExtentions)) {
            throw new \Exception("Invalid image extention " . __FILE__ . " at line " . __LINE__);
        }

        //Getting the image from the given path
        if ($sExtension == "jpg" || $sExtension == "jpeg") {
            $oExtractedImage = imagecreatefromjpeg($sImagePath);
        }
        if ($sExtension == "png") {
            $oExtractedImage = imagecreatefrompng($sImagePath);
        }
        if ($sExtension == "gif") {
            $oExtractedImage = imagecreatefromgif($sImagePath);
        }

        if ($oExtractedImage == false) {
            throw new \Exception("Could not create mini image in " . __FILE__ . " at line " . __LINE__);
        }

        //calcul of the width with the good aspect ratio
        $iSourceWidth = imagesx($oExtractedImage);
        $iSourceHeight = imagesy($oExtractedImage);

        if ($iSourceHeight > $iHeight) {
            $iWidth = ($iSourceWidth * $iHeight) / $iSourceHeight;

            //Create the miniature
            $oMiniature = imagecreatetruecolor($iWidth, $iHeight);

            //fixing transparency for png
            if ($sExtension == "png") {
                imagealphablending($oMiniature, false);
                imagesavealpha($oMiniature, true);
            }

            imagecopyresampled($oMiniature, $oExtractedImage, 0, 0, 0, 0, $iWidth, $iHeight, $iSourceWidth, $iSourceHeight);
            unlink($sFilePath . $sFileName);

            if ($sExtension == "jpg" || $sExtension == "jpeg") {
                imagejpeg($oMiniature, $sFilePath . $sFileName);
            }
            if ($sExtension == "png") {
                imagepng($oMiniature, $sFilePath . $sFileName);
            }
            if ($sExtension == "gif") {
                imagegif($oMiniature, $sFilePath . $sFileName);
            }
        }
    }

    public function generateRandomPrefix($sBaseImageName)
    {

        $sImageName = rand(1, 9999) . '_' . $sBaseImageName;
        $sImageName = strtolower(str_replace(" ", "_", $sImageName));

        return $sImageName;
    }

    public function deleteImageAndMiniAction(Image $oImage)
    {
        if (file_exists($this->sUploadDir . $oImage->getPath() . '/' . $oImage->getFile())) {
            unlink($this->container->getParameter('upload_base_dir') . $oImage->getPath() . '/' . $oImage->getFile());
        }
        if (file_exists($this->sUploadDir . $oImage->getPath() . '/mini_' . $oImage->getFile())) {
            unlink($this->container->getParameter('upload_base_dir') . $oImage->getPath() . '/mini_' . $oImage->getFile());
        }
    }
}
