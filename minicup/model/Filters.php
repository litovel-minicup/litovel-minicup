<?php

namespace Minicup\Model;


use LeanMapper\Exception\InvalidArgumentException;
use LeanMapper\Fluent;
use Minicup\Model\Entity\Team;
use Minicup\Model\Repository\BaseRepository;
use Nette\Object;

class Filters extends Object
{
    /**
     * @param Fluent $fluent
     * @param int $yearId
     */
    public function yearRestrict(Fluent $fluent, $yearId = 0)
    {
        if ($yearId) {
            $fluent->where('[year_id] = %i', $yearId);
        }
    }

    /**
     * @param Fluent $fluent
     * @param Team $team
     * @deprecated
     */
    public function joinAllMatches(Fluent $fluent, Team $team)
    {
        $fluent->removeClause('where')->where('[home_team_id] = ', $team->id, 'OR [away_team_id] =', $team->id);
    }

    /**
     * @param Fluent $fluent
     */
    public function joinTeamInfo(Fluent $fluent)
    {
        $fluent->leftJoin('[team_info]')->on('[team.team_info_id] = [team_info.id]')->select('[team_info.name], [team_info.slug]');
    }

    /**
     * @param Fluent $fluent
     */
    public function actualTeam(Fluent $fluent)
    {
        $fluent->where('[team.actual] = 1');
    }

    /**
     * @param Fluent $fluent
     */
    public function orderTeams(Fluent $fluent)
    {
        $fluent->orderBy('[team.order] ASC');
    }

    /**
     * @param Fluent $fluent
     */
    public function confirmedMatch(Fluent $fluent)
    {
        $fluent->where('[match.confirmed] = 1');
    }

    /**
     * @param Fluent $fluent
     */
    public function unconfirmedMatch(Fluent $fluent)
    {
        $fluent->where('[match.confirmed] = 0');
    }

    /**
     * @param Fluent $fluent
     * @param string $order
     * @throws InvalidArgumentException
     */
    public function orderMatches(Fluent $fluent, $order = BaseRepository::ORDER_ASC)
    {
        if (!in_array($order, array(BaseRepository::ORDER_ASC, BaseRepository::ORDER_DESC))) {
            throw new InvalidArgumentException('Invalid ordering method');
        }
        $fluent
            ->leftJoin('match_term')->as('mt')
            ->on('[match.match_term_id] = mt.[id]')
            ->leftJoin('day')->as('d')
            ->on('d.[id] = mt.[day_id]')
            ->orderBy("d.[day] $order, mt.[start] $order, [match.id] $order");
    }

    /**
     * @param Fluent $fluent
     */
    public function activePhotos(Fluent $fluent)
    {
        $fluent->where('[photo.active] = 1');
    }
}