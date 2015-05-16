<?php

namespace Minicup\Model\Manager;


use LeanMapper\Events;
use Minicup\Model\Entity\BaseEntity;
use Minicup\Model\Entity\Category;
use Minicup\Model\Repository\BaseRepository;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Object;

class CacheManager extends Object
{
    /** @var IStorage */
    private $cache;

    /** @var BaseRepository[] */
    private $repositories;

    /**
     * @param BaseRepository[] $repositories
     * @param IStorage $cache
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
            });
        }
    }

    /***/
    public function cleanByEntity(BaseEntity $entity)
    {
        $this->cache->clean(array(Cache::TAGS => array($entity->getCacheTag())));
        $this->cache->remove($entity->getCacheTag());
    }
}