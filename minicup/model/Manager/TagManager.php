<?php

namespace Minicup\Model\Manager;

use Minicup\Model\Entity\News;
use Minicup\Model\Entity\Tag;
use Minicup\Model\Entity\Team;
use Minicup\Model\Repository\NewsRepository;
use Minicup\Model\Repository\TagRepository;
use Minicup\Model\Repository\TeamInfoRepository;
use Minicup\Model\Repository\TeamRepository;
use Nette\InvalidArgumentException;

use Nette\SmartObject;
use Nette\Utils\Strings;

class TagManager
{

    use SmartObject;
    const PARTS_GLUE = '_';

    /** @var TeamRepository */
    private $teamInfoRepository;

    /** @var TagRepository */
    private $tagRepository;

    /**@var NewsRepository */
    private $newsRepository;

    /**
     * @param TagRepository      $tag
     * @param TeamInfoRepository $teamInfo
     * @param NewsRepository     $newsRepository
     */
    public function __construct(TagRepository $tag,
                                TeamInfoRepository $teamInfo,
                                NewsRepository $newsRepository)
    {
        $this->teamInfoRepository = $teamInfo;
        $this->tagRepository = $tag;
        $this->newsRepository = $newsRepository;
    }

    /**
     * @param News|Tag|Team|string $arg
     * @return Tag|NULL
     * @throws \LeanMapper\Exception\InvalidArgumentException
     */
    public function getTag($arg)
    {
        if ($arg instanceof Tag) {
            return $arg;
        }

        if ($arg instanceof Team) {
            $tag = $arg->i->tag;
            if (!$tag instanceof Tag) {
                $tag = new Tag();
                $tag->slug = $arg->category->slug . $this::PARTS_GLUE . $arg->i->slug;
                $tag->name = $arg->category->name . ' - ' . $arg->i->name;
                $tag->year = $arg->category->year;
                $this->tagRepository->persist($tag);
                $arg->i->tag = $tag;
                $this->teamInfoRepository->persist($arg->i);
            }
            return $tag;
        } elseif ($arg instanceof News) {
            if ($arg->tag) {
                return $arg->tag;
            }
            $tag = new Tag([
                'name' => "novinka - {$arg->title}",
                'slug' => $arg::$CACHE_TAG . $this::PARTS_GLUE . Strings::webalize($arg->title),
                'year' => $arg->year
            ]);
            $this->tagRepository->persist($tag);
            $arg->tag = $tag;
            $this->newsRepository->persist($arg);
            return $tag;
        }

        if ($arg instanceof News) {
            if ($arg->tag) {
                return $arg->tag;
            }
            $tag = new Tag([
                'name' => "novinka - {$arg->title}",
                'slug' => $arg::$CACHE_TAG . $this::PARTS_GLUE . Strings::webalize($arg->title),
                'year' => $arg->year
            ]);
            $this->tagRepository->persist($tag);
            $arg->tag = $tag;
            $this->newsRepository->persist($arg);
            return $tag;
        }

        if (is_string($arg)) {
            return $this->tagRepository->getBySlug($arg);
        }
        throw new InvalidArgumentException('Unknown type "' . gettype($arg) . '"" given.');
    }
}