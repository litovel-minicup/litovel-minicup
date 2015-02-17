<?php
namespace Minicup\Model\Manager;


use Minicup\Model\Entity\Photo;
use Minicup\Model\Repository\PhotoRepository;
use Nette\Http\FileUpload;
use Nette\Object;
use Nette\Utils\Random;

class PhotoManager extends Object
{
    /** @var PhotoRepository */
    private $PR;

    /** @var string */
    private $wwwPath;

    public function __construct($wwwPath, PhotoRepository $PR)
    {
        $this->PR = $PR;
        $this->wwwPath = $wwwPath;
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
            $filename = substr(md5($prefix.$file->name.time()), 0, 8);
            $photo->filename = "$filename";
            // TODO: add thumbnails generation
            $file->move("$this->wwwPath/media/original/$filename.png");
            $this->PR->persist($photo);
            $photos[] = $photo;
        }

        return $photos;
    }

    public function delete(Photo $photo) {
        return unlink("{$this->wwwPath}/media/original/{$photo->filename}.png");
    }
}