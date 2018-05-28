<?php

namespace Minicup\Model\Manager;


use LeanMapper\Events;
use Minicup\Model\Entity\BaseEntity;
use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Photo;
use Minicup\Model\Entity\TeamInfo;
use Minicup\Model\Entity\Year;
use Minicup\Model\Repository\BaseRepository;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\SmartObject;

class CacheManager
{

    use SmartObject;
    /** @var IStorage */
    private $cache;

    /** @var BaseRepository[] */
    private $repositories;

    /**
     * @param BaseRepository[] $repositories
     * @param IStorage         $cache
     */
    public function __construct(array $repositories, IStorage $cache)
    {
        $this->cache = $cache;
        $this->repositories = $repositories;
    }

    public function initEvents()
    {
        $that = $this;
        foreach ($this->repositories as $repository) {
            if (!$repository instanceof BaseRepository) {
                return;
            }
            $repository->registerCallback(Events::EVENT_AFTER_PERSIST, function (BaseEntity $entity) use ($that) {
                $that->cleanByEntity($entity);

                if (isset($entity->category) && $entity->category instanceof Category) {
                    $that->cleanByEntity($entity->category);
                }

                if (isset($entity->year) && $entity->year instanceof Year) {
                    $that->cleanByEntity($entity->year);
                }

                if ($entity instanceof Photo) {
                    foreach ($entity->tags as $tag) {
                        if ($tag->teamInfo) {
                            $that->cleanByEntity($tag->teamInfo);
                        }
                        $that->cleanByEntity($tag);
                    }
                }
            });
        }
    }

    /**
     * @param BaseEntity $entity
     */
    public function cleanByEntity(BaseEntity $entity)
    {
        if ($entity instanceof TeamInfo && $entity->team) {
            $this->cleanByEntity($entity->team);
        }
        $this->cache->clean([Cache::TAGS => [$entity->getCacheTag()] + $entity->getCacheTags()]);
        $this->cache->remove($entity->getCacheTag());
        $this->cache->remove($entity::$CACHE_TAG);
    }

    public function cleanAllEntityCaches()
    {
        foreach ($this->repositories as $repository) {
            foreach ($repository->findAll() as $entity) {
                $this->cleanByEntity($entity);
            }
        }
    }
}