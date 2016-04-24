<?php

namespace Minicup\Model\Manager;


use LeanMapper\Connection;
use LeanMapper\Fluent;
use Minicup\Model\Entity\Category;
use Minicup\Model\Repository\BaseRepository;
use Minicup\Model\Repository\TeamInfoRepository;
use Nette\Object;

class StatsManager extends Object
{
    /** @var Connection */
    private $connection;

    /** @var TeamInfoRepository */
    private $TIR;

    /**
     * @param Connection         $connection
     * @param TeamInfoRepository $TIR
     */
    public function __construct(Connection $connection, TeamInfoRepository $TIR)
    {
        $this->connection = $connection;
        $this->TIR = $TIR;
    }

    /**
     * @param Category $category
     * @return \DibiRow|FALSE
     */
    public function getStats(Category $category)
    {
        $fluent = $this->connection->command();
        /**
         * @param Fluent $arg
         * @return Fluent
         */
        $c = function (Fluent $arg) {
            return clone $arg;
        };
        $f = $c($fluent)
            ->select('IFNULL(SUM([score_home]), 0)+IFNULL(SUM([score_away]), 0) scored_goals')
            ->select('IFNULL(MAX([score_home]), 0) max_home_score')
            ->select('IFNULL(MAX([score_away]), 0) max_away_score')
            ->select('IFNULL(MIN([score_home]), 0) min_home_score')
            ->select('IFNULL(MIN([score_away]), 0) min_away_score')
            ->select('IFNULL(MAX([score_home] + [score_away]), 0) max_match_score')
            ->select('IFNULL(MIN([score_home] + [score_away]), 0) min_match_score')
            ->select('MAX(GREATEST([score_home], [score_away]) - LEAST([score_home], [score_away])) max_diff')
            ->select('(SELECT COUNT([id]) FROM [match] WHERE [score_home]=[score_away] AND [score_home]!=0 AND [category_id]=%i) as count_of_draws', $category->id)
            #->select('MAX()')

            //->select('MIN(SELECT IF([score_home]<[score_away], [score_away], [score_home]) FROM match WHERE category_id = %i) min_win_score', $category->id)

            ->where('[category_id] = ', $category->id)
            ->from('[match]');
        $numberStats = $f->fetch();

        $innerFluent = function () use ($category, $c, $fluent) {
            return $c($fluent)
                ->select('[team_info_id]')
                ->where('[actual] = 1')
                ->where('[category_id] = ', $category->id)
                ->from('[team]');
        };
        $f = $c($fluent)->select(
            $innerFluent()
                ->orderBy('[scored]', BaseRepository::ORDER_DESC)
                ->limit(1), 'most_scored, ',
            $innerFluent()
                ->orderBy('[scored]', BaseRepository::ORDER_ASC)
                ->limit(1), 'least_scored, ',
            $innerFluent()
                ->orderBy('[received]', BaseRepository::ORDER_DESC)
                ->limit(1), 'most_received, ',
            $innerFluent()
                ->orderBy('[received]', BaseRepository::ORDER_ASC)
                ->limit(1), 'least_received, ',
            $innerFluent()
                ->orderBy('([scored] - [received])', BaseRepository::ORDER_DESC)
                ->limit(1), 'best_diff ,',
            $innerFluent()
                ->orderBy('([scored] - [received])', BaseRepository::ORDER_ASC)
                ->limit(1), 'worst_diff'
        );

        $teamStats = $f->fetch()->toArray();
        $teams = $this->TIR->findByIds(array_values($teamStats));
        $teamStats = array_map(function ($id) use ($teams) {
            return isset($teams[$id]) ? $teams[$id] : NULL;
        }, $teamStats);

        return array_merge($numberStats->toArray(), $teamStats);
    }
}