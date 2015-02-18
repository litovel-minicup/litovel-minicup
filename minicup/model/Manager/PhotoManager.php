<?php
namespace Minicup\Model\Manager;


use LeanMapper\Events;
use Minicup\Model\Entity\Photo;
use Minicup\Model\Repository\PhotoRepository;
use Nette\Http\FileUpload;
use Nette\InvalidArgumentException;
use Nette\Object;
use Nette\Utils\Image;
use Nette\Utils\Random;

class PhotoManager extends Object
{
    /** @var PhotoRepository */
    private $PR;

    /** @var string */
    private $wwwPath;

    /** @internal */
    const PHOTO_ORIGINAL = "original";

    const PHOTO_THUMB = "thumb";
    const PHOTO_MEDIUM = "medium";
    const PHOTO_FULL = "full";

    /**
     * type => (width, height, flags)
     * @var array
     */
    public static $resolutions = array(
        PhotoManager::PHOTO_THUMB => array(300, 300, Image::FILL),
        PhotoManager::PHOTO_MEDIUM => array(),
        PhotoManager::PHOTO_FULL => array(),
    );

    /**
     * @param string $wwwPath
     * @param PhotoRepository $PR
     */
    public function __construct($wwwPath, PhotoRepository $PR)
    {
        $this->PR = $PR;
        $this->wwwPath = $wwwPath;
        $PM = $this;
        $this->PR->registerCallback(Events::EVENT_BEFORE_DELETE, function (Photo $photo) use ($PM) {
            $PM->delete($photo);
        });
    }

    /***/
    private function formatPhotoPath($format, $filename)
    {
        // TODO FIX IMAGE EXTENSIONS!
        return "$this->wwwPath/media/" . $format . "/$filename.png";
    }

    /**
     * @param FileUpload[] $files
     * @param string|NULL $prefix
     * @return Photo[]
     */
    public function save($files, $prefix = NULL)
    {
        if (!$prefix) {
            $prefix = Random::generate(20);
        }
        $photos = array();
        foreach ($files as $file) {
            if (!$file->isOk() || !$file->isImage()) {
                continue;
            }
            $photo = new Photo();
            $filename = substr(md5($prefix . $file->name . time()), 0, 8);
            $photo->filename = (string)$filename;
            $file->move($this->formatPhotoPath($this::PHOTO_ORIGINAL, $photo->filename));
            $this->PR->persist($photo);
            $photos[] = $photo;
        }

        return $photos;
    }

    /**
     * @param Photo $photo
     * @return bool
     */
    public function delete(Photo $photo)
    {
        return unlink($this->formatPhotoPath($this::PHOTO_ORIGINAL, $photo->filename));
    }

    /**
     * @param string|Photo $photo
     * @param string $format
     */
    public function getInFormat($photo, $format)
    {
        if (!in_array($format, array_keys($this::$resolutions))) {
            throw new InvalidArgumentException('Unknown photo format!');
        }

        if (is_string($photo)) {

        }


    }
}