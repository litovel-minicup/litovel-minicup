<?php
namespace Minicup\Model\Manager;


use LeanMapper\Events;
use Minicup\Model\Entity\Photo;
use Minicup\Model\Repository\PhotoRepository;
use Nette\FileNotFoundException;
use Nette\Http\FileUpload;
use Nette\InvalidArgumentException;
use Nette\InvalidStateException;
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

    const PHOTO_SMALL = "small";
    const PHOTO_THUMB = "thumb";
    const PHOTO_MEDIUM = "medium";
    const PHOTO_FULL = "full";

    const DEFAULT_FLAG = Image::FILL;

    /**
     * type => (width, height, flags)
     * @var array
     */
    public static $resolutions = array(
        PhotoManager::PHOTO_SMALL => array(100, 100, Image::FILL),
        PhotoManager::PHOTO_THUMB => array(300, 300, Image::FILL),
        PhotoManager::PHOTO_MEDIUM => array(1200, 1200),
        PhotoManager::PHOTO_FULL => array(2000, 2000),
    );

    /**
     * mime type => file extension
     * @var array
     */
    public static $extensions = array(
        'image/png' => 'png',
        'image/jpeg' => 'jpg'
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

    /**
     * @param string $format
     * @param string $filename
     * @return string
     */
    public function formatPhotoPath($format, $filename)
    {
        // TODO FIX IMAGE EXTENSIONS!
        @mkdir("$this->wwwPath/media/" . $format . "/");
        return "$this->wwwPath/media/" . $format . "/$filename.jpg";
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
            $filename = substr(md5($prefix . $file->sanitizedName . time()), 0, 10) . '.' . static::$extensions[$file->contentType];
            $photo->filename = (string)$filename;
            $path = $this->formatPhotoPath($this::PHOTO_ORIGINAL, $photo->filename);
            $file->move($path);

            $exif = exif_read_data($path);
            $taken = new \DibiDateTime();
            if (isset($exif["DateTimeOriginal"])) {
                try {
                    $taken = new \DibiDateTime($exif["DateTimeOriginal"]);
                } catch (\Exception $e) {}
            }
            $photo->taken = $taken;
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
     * @throws InvalidArgumentException
     * @throws FileNotFoundException
     * @throws InvalidStateException
     * @return string
     */
    public function getInFormat($photo, $format)
    {
        if (!in_array($format, array_keys($this::$resolutions))) {
            throw new InvalidArgumentException('Unknown photo format!');
        }

        $filename = $photo;
        if (is_string($photo)) {
            $photo = $this->PR->getByFilename($photo);
        }

        if (!$photo) {
            throw new FileNotFoundException("Photo {$filename} not found!");
        }

        $requested = $this->formatPhotoPath($format, $photo->filename);
        if (file_exists($requested)) {
            throw new InvalidStateException('Apache fails with ' . $requested);
        }

        $original = $this->formatPhotoPath($this::PHOTO_ORIGINAL, $filename);
        $flag = $this::DEFAULT_FLAG;
        if (isset($this::$resolutions[$format][2])) {
            $flag = $this::$resolutions[$format][2];
        }
        $image = Image::fromFile($original)->resize($this::$resolutions[$format][0], $this::$resolutions[$format][0], $flag);
        $image->sharpen()->save($requested);
        return $requested;
    }
}