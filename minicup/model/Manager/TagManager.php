<?php

namespace Minicup\Model\Manager;

use Minicup\Model\Entity\Tag;
use Minicup\Model\Entity\Team;
use Minicup\Model\Repository\TagRepository;
use Minicup\Model\Repository\TeamInfoRepository;
use Minicup\Model\Repository\TeamRepository;
use Nette\InvalidArgumentException;
use Nette\Object;

class TagManager extends Object
{
    const PARTS_GLUE = '_';

    /** @var TeamRepository */
    private $teamInfo;

    /** @var TagRepository */
    private $tag;

    /**
     * @param TagRepository $tag
     * @param TeamInfoRepository $teamInfo
     */
    public function __construct(TagRepository $tag, TeamInfoRepository $teamInfo)
    {
        $this->teamInfo = $teamInfo;
        $this->tag = $tag;
    }

    /**
     * @param $arg
     * @return Tag|NULL
     */
    public function getTag($arg)
    {
        if ($arg instanceof Tag) {
            return $arg;
        } elseif ($arg instanceof Team) {
            $tag = $arg->i->tag;
            if (!$tag instanceof Tag) {
                $tag = new Tag();
                $tag->slug = $arg->category->slug . $this::PARTS_GLUE . $arg->i->slug;
                $tag->name = $arg->category->name . ' - ' . $arg->i->name;
                $this->tag->persist($tag);
                $arg->i->tag = $tag;
                $this->teamInfo->persist($arg->i);
            }
            return $tag;
        } elseif (is_string($arg)) {
            return $this->tag->getBySlug($arg);
        }
        throw new InvalidArgumentException('Unknown type "' . gettype($arg) . '"" given.');
    }
}