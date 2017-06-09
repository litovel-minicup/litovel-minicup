<?php
namespace Minicup\Model\Manager;


use Dibi\DateTime;
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

    /** @var Image */
    private $watermark;

    /** @var PhotoRepository */
    private $PR;

    /** @var string */
    private $wwwPath;

    /**
     * @param string          $wwwPath
     * @param PhotoRepository $PR
     */
    public function __construct($wwwPath, PhotoRepository $PR)
    {
        $this->PR = $PR;
        $this->wwwPath = $wwwPath;
        $this->watermark = Image::fromFile($this->wwwPath . '/assets/img/watermark.png');
    }

    /**
     * @param FileUpload[] $files
     * @param string|NULL  $prefix
     * @param string|NULL  $author
     * @return \Minicup\Model\Entity\Photo[]
     */
    public function save($files, $prefix = NULL, $author = NULL)
    {
        if (!$prefix) {
            $prefix = Random::generate(20);
        }
        $photos = [];
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
            $taken = new DateTime();
            if (isset($exif['DateTimeOriginal'])) {
                try {
                    $taken = new DateTime($exif['DateTimeOriginal']);
                } catch (\Exception $e) {
                }
            }
            $photo->taken = $taken;
            $photo->author = $author;
            $this->PR->persist($photo);
            $photos[] = $photo;
        }

        return $photos;
    }

    /**
     * @param string $format
     * @param string $filename
     * @return string
     */
    public function formatPhotoPath($format, $filename)
    {
        @mkdir("$this->wwwPath/media/" . $format . '/');
        return "$this->wwwPath/media/" . $format . "/$filename";
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
                    unlink($path);
                }
            }
            unlink($this->formatPhotoPath($this::PHOTO_ORIGINAL, $photo->filename));
            $this->PR->delete($photo);
        }
    }

    /**
     * @param string|Photo|NULL $photo
     * @param string            $format
     * @throws InvalidArgumentException
     * @throws FileNotFoundException
     * @throws InvalidStateException
     * @return string
     */
    public function getInFormat($photo, $format)
    {
        if (!in_array($format, array_keys($this::$resolutions), TRUE)) {
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
            throw new FileNotFoundException('Apache fails with ' . $requested);
        }

        $original = $this->formatPhotoPath($this::PHOTO_ORIGINAL, $filename);
        $flag = $this::DEFAULT_FLAG;
        if (isset($this::$resolutions[$format][2])) {
            $flag = $this::$resolutions[$format][2];
        }

        $image = Image::fromFile($original)->resize($this::$resolutions[$format][0], $this::$resolutions[$format][1], $flag);
		$exif = exif_read_data($original);
		if (isset($exif['COMPUTED']) && isset($exif['COMPUTED'] ['Orientation'] )) {
			try {
				$orientation = $exif['COMPUTED'] ['Orientation'];
				dump($orientation);
				switch($orientation){
					case 3:
						$image->rotate(180, Image::rgb(0,0,0));
						break;
					case 6:
						$image->rotate(-90, Image::rgb(0,0,0));
						break;
					case 8:
						$image->rotate(90, Image::rgb(0,0,0));
						break;
				}
			} catch (\Exception $e) {
				dump($e);
			}
		}
		dump($image);
        $image->sharpen();
        $watermark = clone $this->watermark;
        $watermark = $watermark->resize(
            $this::$resolutions[$format][0] / 6,
            $this::$resolutions[$format][1] / 6,
            Image::FIT | Image::SHRINK_ONLY
        );

        $placeTop = $image->getHeight() - $watermark->getHeight() - $image->getHeight() / 40;
        $placeLeft = $image->getWidth() - $watermark->getWidth() - $image->getWidth() / 40;
        $image->place($watermark, $placeLeft, $placeTop);
        $image->save($requested);
        return $requested;
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