<?php
define('ACCESS_LEVEL_ADMIN',  1);
define('ACCESS_LEVEL_OWNER',  2);
 
define('POSITION_QB',   1);
define('POSITION_RB',   2);
define('POSITION_WR',   3);
define('POSITION_TE',   4);
define('POSITION_DST',  5);
define('POSITION_K',    6);

define('TEAM_ABBY',     1);
define('TEAM_ALEX',     2);
define('TEAM_BEN',      3);
define('TEAM_EMILY_H',  4);
define('TEAM_EMILY_L',  5);
define('TEAM_JIM',      6);
define('TEAM_MAEGAN',   7);
define('TEAM_MAX',      8);
define('TEAM_MEG',      9);
define('TEAM_MOLLY',    10);
define('TEAM_RANDY',    11);
define('TEAM_RICKY',    12);
	
$positions = array(
	'qb'   => POSITION_QB,
	'rb'   => POSITION_RB,
	'wr'   => POSITION_WR,
	'te'   => POSITION_TE,
	'dst'  => POSITION_DST,
	'k'    => POSITION_K
);

$positions_display = array(
	POSITION_QB   => 'QB',
	POSITION_RB   => 'RB',
	POSITION_WR   => 'WR',
	POSITION_TE   => 'TE',
	POSITION_DST  => 'DST',
	POSITION_K    => 'K'
);

$teams = array(
	'abby'    => TEAM_ABBY,
	'alex'    => TEAM_ALEX,
	'ben'     => TEAM_BEN,
	'emilyh'  => TEAM_EMILY_H,
	'emilyl'  => TEAM_EMILY_L,
	'jim'     => TEAM_JIM,
	'maegan'  => TEAM_MAEGAN,
	'max'     => TEAM_MAX,
	'meg'     => TEAM_MEG,
	'molly'   => TEAM_MOLLY,
	'randy'   => TEAM_RANDY,
	'ricky'   => TEAM_RICKY
);

$teams_display = array(
	TEAM_ABBY     => 'Abby',
	TEAM_ALEX     => 'Alex',
	TEAM_BEN      => 'Ben',
	TEAM_EMILY_H  => 'Emily H.',
	TEAM_EMILY_L  => 'Emily L.',
	TEAM_JIM      => 'Jim',
	TEAM_MAEGAN   => 'Maegan',
	TEAM_MAX      => 'Max',
	TEAM_MEG      => 'Meg',
	TEAM_MOLLY    => 'Molly',
	TEAM_RANDY    => 'Randy',
	TEAM_RICKY    => 'Ricky'
);