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
 * @property int            $played flag
 * @property MatchTerm      $matchTerm m:hasOne(match_term_id:match_term) term fo this match
 * @property OnlineReport[] $onlineReports m:belongsToMany(:online_report) reports
 *
 */
class Match extends Entity
{

}
