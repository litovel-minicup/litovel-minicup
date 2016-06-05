<?php
/**
 * Created by PhpStorm.
 * User: thejoeejoee
 * Date: 3.4.15
 * Time: 14:53
 */

namespace Minicup\Model\Manager;


use Minicup\Model\Entity\StaticContent;
use Minicup\Model\Entity\Team;
use Minicup\Model\Entity\Year;
use Minicup\Model\Repository\StaticContentRepository;
use Minicup\Model\Repository\TeamInfoRepository;
use Nette\InvalidArgumentException;
use Nette\Object;

class StaticContentManager extends Object
{
    const PARTS_GLUE = '_';

    /** @var TeamInfoRepository */
    private $TIR;

    /** @var StaticContentRepository */
    private $SCR;

    /**
     * @param TeamInfoRepository      $TIR
     * @param StaticContentRepository $SCR
     */
    public function __construct(TeamInfoRepository $TIR,
                                StaticContentRepository $SCR)
    {
        $this->TIR = $TIR;
        $this->SCR = $SCR;
    }

    /**
     * @param Team|string|StaticContent $arg
     * @param Year                      $year
     * @return StaticContent|NULL
     */
    public function getContent($arg, Year $year = NULL)
    {
        if ($arg instanceof Team) {
            $staticContent = $arg->i->staticContent;
            if (!$staticContent instanceof StaticContent) {
                $staticContent = new StaticContent();
                $staticContent->content = '';
                $staticContent->slug = $arg->category->slug . $this::PARTS_GLUE . $arg->i->slug;
                $this->SCR->persist($staticContent);
                $arg->i->staticContent = $staticContent;
                $this->TIR->persist($arg->i);
            }
            return $staticContent;
        } elseif (is_string($arg)) {
            if (!$year) {
                throw new InvalidArgumentException('Static content can be made only from slug and year.');
            }
            $staticContent = $this->SCR->getBySlug($arg, $year);
            if (!$staticContent) {
                $staticContent = new StaticContent();
                $staticContent->slug = $arg;
                $staticContent->year = $year;
                $staticContent->content = '';
                $this->SCR->persist($staticContent);
            }
            return $staticContent;
        } elseif ($arg instanceof StaticContent) {
            return $arg;
        }
        throw new InvalidArgumentException('Unknown type "' . gettype($arg) . '"" given.');

    }
}