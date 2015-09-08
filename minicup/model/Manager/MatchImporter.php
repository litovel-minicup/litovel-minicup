<?php

namespace Minicup\Model\Manager;


use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Match;
use Minicup\Model\Repository\DayRepository;
use Minicup\Model\Repository\MatchRepository;
use Minicup\Model\Repository\MatchTermRepository;
use Minicup\Model\Repository\TeamInfoRepository;
use Nette\Utils\Strings;

class MatchImporter
{
    /** @var MatchRepository */
    private $MR;

    /** @var MatchTermRepository */
    private $MTR;

    /** @var DayRepository */
    private $DR;

    /** @var TeamInfoRepository */
    private $TIR;

    /**
     * @param MatchRepository $MR
     * @param MatchTermRepository $MTR
     * @param DayRepository $DR
     * @param TeamInfoRepository $TIR
     */
    public function __construct(MatchRepository $MR, MatchTermRepository $MTR, DayRepository $DR, TeamInfoRepository $TIR)
    {
        $this->MR = $MR;
        $this->MTR = $MTR;
        $this->DR = $DR;
        $this->TIR = $TIR;
    }

    /**
     * @param Category $category
     * @param string $file
     */
    public function import(Category $category, $file)
    {
        $data = file_get_contents($file);
        foreach (Strings::split($data, "#\r|\n#") as $line) {
            $line = Strings::split($line, "#\t#");
            $datetime =  \DateTime::createFromFormat('j. n. Y H:i', $line[0].' '.$line[1]);
            $term = $this->MTR->getByStart($datetime);
            $home = $this->TIR->getByName($category, $line[2]);
            $away = $this->TIR->getByName($category, $line[3]);

            $match = new Match();
            $match->category = $category;
            $match->homeTeam = $home;
            $match->awayTeam = $away;
            $match->matchTerm = $term;
            try {
                $this->MR->persist($match);
            } catch (\DibiDriverException $e) {

            }

        }
    }
}