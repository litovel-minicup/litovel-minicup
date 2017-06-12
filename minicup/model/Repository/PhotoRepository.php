<?php

namespace Minicup\Model\Repository;


use Minicup\Model\Entity\Photo;
use Minicup\Model\Entity\Tag;
use Minicup\Model\Entity\Year;

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
    public function findUntaggedPhotos(Year $year)
    {
        return $this->createEntities($this->connection
            ->select('*')
            ->from($this->getTable())
            ->where('EXTRACT(YEAR FROM [taken]) = ', $year->year)
            ->where('[id] NOT IN',
                $this->connection->select('[photo_id]')
                    ->from('[photo_tag]')
                    ->groupBy('[photo_id]')
            )->fetchAll());
    }

    /**
     * @param Year $year
     * @return Photo[]
     */
    public function findByYear(Year $year)
    {
        return $this->createEntities($this
            ->createFluent()
            ->where('[id] IN',
                $this->connection
                    ->select('[photo_id]')->from('[photo_tag]')
                    ->leftJoin('[tag]')->on('[tag.id] = [photo_tag.tag_id]')
                    ->where('[tag.year_id] = ', $year->id)
            )->fetchAll()
        );
    }

    /**
     * @param Tag    $tag
     * @param string $order
     * @return Photo[]
     */
    public function findByTag(Tag $tag, $order = BaseRepository::ORDER_ASC)
    {
        return $this->createEntities(
            $this->connection
                ->select('[photo.*]')->from('photo')
                ->leftJoin('[photo_tag]')->on('[photo_tag.photo_id] = [photo.id]')
                ->where('[photo_tag.tag_id] = ', $tag->id)
				->where('[photo.active] = 1')
                ->orderBy("[photo.taken] $order")
                ->fetchAll()
        );
    }
}