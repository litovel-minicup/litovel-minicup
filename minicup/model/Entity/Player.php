<?php
/**
 * Created by PhpStorm.
 * User: Martin Kobelka
 * Date: 01/03/18
 * Time: 22:48
 */

namespace Minicup\Model\Entity;


/**
 * @property int      $id
 * @property string   $name                          Name of player
 * @property string   $surname                       Surname of player
 * @property int      $number                        Ordinal number of player in team
 * @property int|NULL $secondaryNumber               Secondary Ordinal number of player in team
 * @property TeamInfo $teamInfo m:hasOne(team_info_id)             Info about player team
 */
class Player extends BaseEntity
{

    public static $CACHE_TAG = 'player';

}