<?php

namespace Minicup\Model\Entity;
use LeanMapper\Entity;
/**
 * @property int $id
 * @property Category $category m:hasOne
 * @property Team $home_team m:hasOne(home_team_id:team)
 * @property Team $away_team m:hasOne(away_team_id:team)
 * @property int $score_home score of home team
 * @property int $score_away score of away team
 * @property OnlineReport[] $online_reports m:belongsToMany(:online_report)
 * 
 */
class Match extends Entity {
    
}
