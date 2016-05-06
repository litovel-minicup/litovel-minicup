<?php

namespace Minicup\Model\Repository;


use Minicup\Model\Entity\Photo;
use Minicup\Model\Entity\Tag;

class PhotoRepository extends BaseRepository
{
    /**
     * @param Tag[] $tags
     * @return Photo[]
     */
    public function findByTags(array $tags)
    {
        $photos = [];
        foreach ($tags as $tag) {
            foreach ($tag->photos as $photo) {
                $photos[$photo->id] = $photo;
            }
        }
        return $photos;
    }

    /**
     * @param string $filename
     * @return Photo|NULL
     */
    public function getByFilename($filename)
    {
        $row = $this->createFluent()->where('filename = ?', $filename)->fetch();
        return $row ? $this->createEntity($row) : NULL;
    }

    /**
     * @return Photo[]
     */
    public function findUntaggedPhotos()
    {
        return $this->createEntities($this->connection
            ->select('*')
            ->from($this->getTable())
            ->where('[id] NOT IN',
                $this->connection->select('[photo_id]')
                    ->from('[photo_tag]')
                    ->groupBy('[photo_id]')
            )->fetchAll());
    }
}