<?php

namespace Minicup\Model\Manager;


use LeanMapper\Events;
use Minicup\Model\Entity\BaseEntity;
use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Tag;
use Minicup\Model\Repository\BaseRepository;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\MatchRepository;
use Minicup\Model\Repository\StaticContentRepository;
use Minicup\Model\Repository\TeamInfoRepository;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Object;

class CacheManager extends Object
{
    /** @var IStorage */
    private $cache;

    /** @var TeamInfoRepository */
    private $TIR;

    /** @var CategoryRepository */
    private $CR;

    /** @var StaticContentRepository */
    private $SCR;

    /** @var MatchRepository */
    private $MR;

    /** @var TagManager */
    private $TM;

    /**
     * @param IStorage $cache
     * @param TeamInfoRepository $TIR
     * @param CategoryRepository $CR
     * @param StaticContentRepository $SCR
     * @param MatchRepository $MR
     * @param TagManager $TM
     */
    public function __construct(IStorage $cache,
                                TeamInfoRepository $TIR,
                                CategoryRepository $CR,
                                StaticContentRepository $SCR,
                                MatchRepository $MR,
                                TagManager $TM)
    {
        $this->cache = $cache;
        $this->TIR = $TIR;
        $this->CR = $CR;
        $this->SCR = $SCR;
        $this->MR = $MR;
        $this->TM = $TM;
    }

    public function initEvents()
    {
        $cache = $this->cache;
        foreach (array($this->TIR, $this->CR, $this->SCR, $this->MR) as $repository) {
            if (!$repository instanceof BaseRepository) {
                return;
            }
            $repository->registerCallback(Events::EVENT_AFTER_PERSIST, function (BaseEntity $entity) use ($cache) {
                $cache->clean(array(Cache::TAGS => array($entity->getCacheTag())));
                $cache->remove($entity->getCacheTag());

                if (isset($entity->category) && $entity->category instanceof Category) {
                    $cache->clean(array(Cache::TAGS => array($entity->category->getCacheTag())));
                    $cache->remove($entity->category->getCacheTag());
                }
                if ($entity instanceof Tag) {

                }
            });
        }
    }


}