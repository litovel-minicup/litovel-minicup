<?php

namespace Minicup\Model\Entity;

use LeanMapper\Entity;

/**
 * @property int            $id
 * @property Category       $category m:hasOne                              category
 * @property TeamInfo       $homeTeam m:hasOne(home_team_info_id:team_info) home team
 * @property TeamInfo       $awayTeam m:hasOne(away_team_info_id:team_info) away team
 * @property int|NULL       $scoreHome                                      score of home team
 * @property int|NULL       $scoreAway                                      score of away team
 * @property int            $confirmed                                      flag if is it really confirmed
 * @property MatchTerm      $matchTerm m:hasOne(match_term_id:match_term)   term fo this match
 * @property OnlineReport[] $onlineReports m:belongsToMany(:online_report)  reports
 */
class Match extends Entity
{

}
