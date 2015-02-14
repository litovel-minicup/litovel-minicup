<?php

namespace Minicup\Model\Repository;


use LeanMapper\Connection;
use LeanMapper\Events;
use LeanMapper\IEntityFactory;
use LeanMapper\IMapper;
use Minicup\Model\Entity\Photo;
use Minicup\Model\Entity\Tag;

class PhotoRepository extends BaseRepository
{
    public function __construct(Connection $connection, IMapper $mapper, IEntityFactory $entityFactory)
    {
        parent::__construct($connection, $mapper, $entityFactory);
        $this->events->registerCallback(Events::EVENT_AFTER_PERSIST, function () {
            //TODO: saving image to FS
        });
    }

    /**
     * @param Tag[] $tags
     * @return Photo[]
     */
    public function findByTags(array $tags)
    {
        $photos = array();
        foreach ($tags as $tag) {
            foreach ($tag->photos as $photo) {
                $photos[$photo->id] = $photo;
            }
        }
        return $photos;
    }

    /**
     * @param int[] $ids
     * @return Photo[]
     */
    public function findByIds(array $ids)
    {
        return $ids ? $this->createEntities($this->createFluent()->where('[id] IN (%i)', $ids)->fetchAll()) : array();
    }

}