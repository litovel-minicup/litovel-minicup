<?php

namespace Minicup\Model\Manager;


use LeanMapper\Connection;
use Minicup\Model\Entity\Category;
use Nette\Object;

class StatsManager extends Object
{
    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param Category $category
     * @return \DibiRow|FALSE
     */
    public function getStats(Category $category)
    {
        $f = $this->connection->command();
        $f
            ->select('IFNULL(SUM([score_home]), 0)+IFNULL(SUM([score_away]), 0) all_scored')
            ->select('IFNULL(MAX([score_home]), 0) max_home_score')
            ->select('IFNULL(MAX([score_away]), 0) max_away_score')
            ->select('IFNULL(MIN([score_home]), 0) min_home_score')
            ->select('IFNULL(MIN([score_away]), 0) min_away_score')
            ->select('IFNULL(MAX([score_home]+[score_home]), 0) max_match_score')
            ->select('IFNULL(MIN([score_home]+[score_home]), 0) min_match_score')

            ->select('MAX(GREATEST([score_home], [score_away]) - LEAST([score_home], [score_away])) max_diff')
            ->select('(SELECT COUNT([id]) FROM [match] WHERE [score_home]=[score_away] AND [score_home]!=0 AND [category_id]=%i) as count_of_draws', $category->id)
            #->select('MAX()')

            //->select('MIN(SELECT IF([score_home]<[score_away], [score_away], [score_home]) FROM match WHERE category_id = %i) min_win_score', $category->id)

            ->where('[category_id] = ', $category->id)
            ->from('[match]');
        return $f->fetch();
    }
}