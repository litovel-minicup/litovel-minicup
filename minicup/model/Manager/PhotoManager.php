<?php

namespace Minicup\Model\Manager;


use Dibi\DateTime;
use Minicup\Model\Entity\Photo;
use Minicup\Model\Entity\Year;
use Minicup\Model\Repository\PhotoRepository;
use Minicup\Model\Repository\YearRepository;
use Nette\FileNotFoundException;
use Nette\Http\FileUpload;
use Nette\InvalidArgumentException;
use Nette\InvalidStateException;
use Nette\Utils\FileSystem;
use Nette\Utils\Image;
use Nette\Utils\ImageException;
use Nette\Utils\Random;
use Tracy\Debugger;

use Nette\SmartObject;

class PhotoManager
{
    use SmartObject;
    /** @internal */
    const PHOTO_ORIGINAL = '_original';
    const PHOTO_MINI = 'mini';
    const PHOTO_SMALL = 'small';
    const PHOTO_THUMB = 'thumb';
    const PHOTO_MEDIUM = 'medium';
    const PHOTO_LARGE = 'large';
    const PHOTO_FULL = 'full';
    const DEFAULT_FLAG = Image::FILL;
    /**
     * type => (width, height, flags)
     * @var array
     */
    public static $resolutions = [
        PhotoManager::PHOTO_MINI => [50, 50, Image::FILL],
        PhotoManager::PHOTO_SMALL => [100, 100, Image::FILL],
        PhotoManager::PHOTO_THUMB => [300, 300, Image::FIT | Image::EXACT],
        PhotoManager::PHOTO_MEDIUM => [750, 750, Image::FILL],
        PhotoManager::PHOTO_LARGE => [1200, 1200],
        PhotoManager::PHOTO_FULL => [2000, 2000],
    ];
    /**
     * mime type => file extension
     * @var array
     */
    public static $extensions = [
        'image/png' => 'png',
        'image/jpeg' => 'jpg'
    ];

    /** @var Image[] */
    private $watermarks;

    /** @var PhotoRepository */
    private $PR;

    /** @var YearRepository */
    private $YR;

    /** @var string */
    private $wwwPath;

    /**
     * @param string          $wwwPath
     * @param PhotoRepository $PR
     * @param YearRepository  $YR
     * @throws \Nette\Utils\UnknownImageFileException
     */
    public function __construct($wwwPath, PhotoRepository $PR, YearRepository $YR)
    {
        $this->PR = $PR;
        $this->YR = $YR;
        $this->wwwPath = $wwwPath;
        /** @var Year $year */
        foreach ($YR->findAll(FALSE) as $year) {
            try {
                $this->watermarks[$year->id] = Image::fromFile(
                    $this->wwwPath . "/assets/img/watermarks/{$year->slug}.png"
                );
            } catch (ImageException $e) {
                Debugger::log($e);
            }
        }
    }

    /**
     * @param FileUpload[] $files
     * @param string|NULL  $prefix
     * @param string|NULL  $author
     * @return \Minicup\Model\Entity\Photo[]
     * @throws \LeanMapper\Exception\InvalidArgumentException
     */
    public function saveFromUpload($files, $prefix = NULL, $author = NULL)
    {
        if (!$prefix) {
            $prefix = Random::generate(20);
        }
        $photos = [];
        bdump(__METHOD__);
        bdump($files);
        foreach ($files as $file) {

            bdump($file->isOk() && $file->isImage());
            if (!$file->isOk() || !$file->isImage()) {
                continue;
            }
            $photo = new Photo();
            $filename = substr(md5($prefix . $file->sanitizedName . time()), 0, 10) . '.' . static::$extensions[$file->contentType];
            $photo->filename = (string)$filename;
            $path = $this->formatPhotoPath($this::PHOTO_ORIGINAL, $photo->filename);
            $file->move($path);

            $exif = @exif_read_data($path);
            $taken = new DateTime();
            bdump($exif);
            if (isset($exif['DateTimeOriginal'])) {
                try {
                    $taken = new DateTime($exif['DateTimeOriginal']);
                } catch (\Exception $e) {
                    dump($exif, $e);
                    exit;
                }
            }
            $photo->taken = $taken;
            $photo->author = $author;
            $this->PR->persist($photo);
            $photos[] = $photo;
        }
        /**
         * FileName => "81a3467ec3.jpg" (14)
        FileDateTime => 1559738922
        FileSize => 143535
        FileType => 2
        MimeType => "image/jpeg" (10)
        SectionsFound => "COMMENT" (7)
        COMPUTED =>
        html => "width="800" height="1200"" (25)
        Height => 1200
        Width => 800
        IsColor => 1
        COMMENT =>

         */

        return $photos;
    }


    /**
     * @param Image       $image
     * @param string|NULL $prefix
     * @param string|NULL $author
     * @return Photo
     * @throws \LeanMapper\Exception\InvalidArgumentException
     */
    public function saveImage(Image $image, $prefix = NULL, $author = NULL): Photo
    {
        if (!$prefix) {
            $prefix = Random::generate(20);
        }

        $photo = new Photo();
        $filename = substr(md5($prefix . time()), 0, 10) . '.jpg';
        $photo->filename = (string)$filename;
        $path = $this->formatPhotoPath($this::PHOTO_ORIGINAL, $photo->filename);
        $image->save($path);

        $exif = @exif_read_data($path);
        $taken = new DateTime();
        bdump($exif);
        if (isset($exif['DateTimeOriginal'])) {
            try {
                $taken = new DateTime($exif['DateTimeOriginal']);
            } catch (\Exception $e) {
                dump($exif, $e);
                exit;
            }
        }
        $photo->taken = $taken;
        $photo->author = $author;
        $this->PR->persist($photo);

        return $photo;
    }

    /**
     * @param string $format
     * @param string $filename
     * @return string
     */
    public function formatPhotoPath($format, $filename)
    {
        @mkdir("$this->wwwPath/media/$format/");
        return "$this->wwwPath/media/$format/$filename";
    }

    /**
     * @param Photo $photo
     * @param bool  $lazy
     * @throws \LeanMapper\Exception\InvalidStateException
     */
    public function delete(Photo $photo, $lazy = FALSE)
    {
        if ($lazy) {
            $photo->active = 0;
            $this->PR->persist($photo);
        } else {
            foreach ($this::$resolutions as $format => $val) {
                $path = $this->formatPhotoPath($format, $photo->filename);
                if (file_exists($path)) {
                    FileSystem::delete($path);
                }
            }
            FileSystem::delete($this->formatPhotoPath($this::PHOTO_ORIGINAL, $photo->filename));
            $this->PR->delete($photo);
        }
    }

    /**
     * @param string|Photo|NULL $photo
     * @param string            $format
     * @return string
     * @throws FileNotFoundException
     * @throws InvalidStateException
     * @throws InvalidArgumentException
     * @throws \Nette\Utils\UnknownImageFileException
     */
    public function getInFormat($photo, $format)
    {
        if (!in_array($format, array_keys($this::$resolutions), TRUE)) {
            throw new InvalidArgumentException('Unknown photo format!');
        }

        $filename = $photo;
        if (is_string($photo)) {
            /** @var Photo $photo */
            $photo = $this->PR->getByFilename($photo);
        }


        if (!$photo) {
            throw new FileNotFoundException("Photo {$filename} not found!");
        }

        $requested = $this->formatPhotoPath($format, $photo->filename);
        if (file_exists($requested)) {
            throw new FileNotFoundException('Apache fails with ' . $requested);
        }

        $original = $this->formatPhotoPath($this::PHOTO_ORIGINAL, $filename);
        $flag = $this::DEFAULT_FLAG;
        if (isset($this::$resolutions[$format][2])) {
            $flag = $this::$resolutions[$format][2];
        }

        $image = Image::fromFile($original)->resize($this::$resolutions[$format][0], $this::$resolutions[$format][1], $flag);
        // dump($image);
        $exif = exif_read_data($original);
        // dump($exif);
        // dump($exif['Orientation']);
        if (isset($exif['Orientation'])) {
            try {
                $orientation = $exif['Orientation'];
                dump($orientation);
                switch ($orientation) {
                    case 3:
                        $image->rotate(180, Image::rgb(0, 0, 0));
                        break;
                    case 6:
                        $image->rotate(-90, Image::rgb(0, 0, 0));
                        break;
                    case 8:
                        $image->rotate(90, Image::rgb(0, 0, 0));
                        break;
                }
            } catch (\Exception $e) {
                dump($e);
            }
        }
        // dump($image);
        $image->sharpen();
        if ($this->placeWatermark($photo, $image, $format)) {
            $image->save($requested);
        }
        return $requested;
    }


    /**
     * @param Photo  $photo
     * @param Image  $image
     * @param string $format
     * @return bool
     */
    protected function placeWatermark(Photo $photo, Image $image, $format)
    {
        if (!count($photo->tags))
            return FALSE;
        $year = $photo->tags[0]->year;
        if (!$this->watermarks[$year->id])
            return FALSE;

        $watermark = clone $this->watermarks[$year->id];
        $watermark = $watermark->resize(
            self::$resolutions[$format][0] / 6,
            self::$resolutions[$format][1] / 6,
            Image::FIT | Image::SHRINK_ONLY
        );

        $placeTop = $image->getHeight() - $watermark->getHeight() - $image->getHeight() / 40;
        $placeLeft = $image->getWidth() - $watermark->getWidth() - $image->getWidth() / 40;
        $image->place($watermark, $placeLeft, $placeTop);
        return TRUE;
    }

    /**
     * @param array $formats
     * @return array
     */
    public function cleanCachedPhotos(array $formats = [])
    {
        if (!$formats) {
            $formats = array_keys($this::$resolutions);
        }
        $failed = [];
        /** @var Photo $photo */
        foreach ($this->PR->findAll() as $photo) {
            /** @var string $format */
            foreach ($formats as $format) {
                $filename = $this->formatPhotoPath($format, $photo->filename);
                if (file_exists($filename)) {
                    if (!unlink($filename)) {
                        $failed[] = $photo->filename;
                    }
                }
            }
        }
        return $failed;
    }
}