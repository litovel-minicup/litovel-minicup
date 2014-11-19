<?php

namespace Minicup\Model\Entity;

use LeanMapper\Entity;

/**
 * @property int            $id
 * @property Category       $category m:hasOne
 * @property Team           $homeTeam m:hasOne(home_team_id:team)
 * @property Team           $awayTeam m:hasOne(away_team_id:team)
 * @property int            $scoreHome score of home team
 * @property int            $scoreAway score of away team
 * @property MatchTerm      $matchTerm m:hasOne(match_term_id:match_term)
 * @property OnlineReport[] $onlineReports m:belongsToMany(:online_report)
 *
 */
class Match extends Entity
{

}
