<?php

namespace Minicup\Model\Repository;

use Minicup\Model\Entity\Match;

class MatchRepository extends Repository
{
    /**
     * @param \DateTime $dateTime
     * @return Match[]
     */
    public function findMatchesByDate(\DateTime $dateTime)
    {
        $date = $dateTime->format('Y-m-d');

        $values = $this->connection->query('SELECT
        [match].[id],
        [match].[home_team_id],
        [match].[away_team_id],
        [match].[match_term_id],
        [match_term].[start],
        [match_term].[end]
        FROM [match] LEFT JOIN [match_term] ON [match_term].[id] = [match].[match_term_id] WHERE [match_term].[start] LIKE ', '"%'.$date.'%"')->fetchAll();
        return $this->createEntities(
            $values
        );
    }
}
