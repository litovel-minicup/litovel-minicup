<?php

namespace Minicup\Model\Repository;


use Minicup\Model\Entity\Match;
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
     * @param Year $year
     * @return Photo[]
     * @throws \LeanMapper\Exception\InvalidStateException
     */
    public function findUntaggedAndNotActivePhotos(Year $year): array
    {
        return $this->createEntities($this->connection->query('
            SELECT *
            FROM photo
            WHERE (EXTRACT(YEAR FROM taken) = %i) AND (active IS FALSE OR id NOT IN (SELECT photo_id from photo_tag))
            ORDER BY taken
        ', $year->year)->fetchAll()
        );
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

    /**
     * @param Match $match
     * @return array
     * @throws \Dibi\Exception
     */
    public function findForMatch(Match $match)
    {
        if (!$match->homeTeam->tag || !$match->awayTeam->tag) return [];

        return $this->createEntities(
            $this->connection->query(
                '
                    SELECT p.*
                    FROM photo p
                    LEFT JOIN photo_tag pt1 on p.id = pt1.photo_id and pt1.tag_id = ?
                    LEFT JOIN photo_tag pt2 on p.id = pt2.photo_id and pt2.tag_id = ?
                    WHERE (
                      -- has both tags
                      pt1.tag_id IS NOT NULL AND pt2.tag_id IS NOT NULL
                    ) OR (
                      -- or has one tag and has been taken in match term
                      (pt1.tag_id IS NOT NULL OR pt2.tag_id IS NOT NULL) AND taken BETWEEN %s AND %s AND taken = %s
                    )
                    ORDER BY taken;
                ',
                $match->homeTeam->tag->id,
                $match->awayTeam->tag->id,
                $match->matchTerm->start->format('H:i:s'),
                $match->matchTerm->end->format('H:i:s'),
                $match->matchTerm->day->day->format('Y-m-d')
            )->fetchAll()
        );
    }

    public function countYearPhotos(Year $year): int
    {
        return $this->connection->query('
            SELECT COUNT(DISTINCT p.id) as c
            FROM photo p
            INNER JOIN photo_tag pt on p.id = pt.photo_id
            INNER JOIN tag t on t.id = pt.tag_id
            WHERE t.year_id = ? AND active = 1
        ', $year->id)->fetch()['c'];
    }
}