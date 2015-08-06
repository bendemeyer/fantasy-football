<?php
require_once BASE_PATH . '/actions/draftAPI.php';

Class DraftController extends BaseController {
    $access_level = 1;

    private function auth($team) {
        $user_access_level = $_SESSION['user']['access_level'];
        if ($_SESSION['user']['team'] != $team && $_SESSION['user']['access_level'] != ACCESS_LEVEL_ADMIN) {
            return false;
        }
        return true;
    }

    function draftplayer($params) {
        if (!$this->auth($params['post']['team'])) {
            return json_encode(array('error' => 'You are not allowed to draft players for team ' . $teams_display[$params['post']['team']]));
        }
        $params['url']['id']
		$draft = new DraftAPI($params['url']['id']);

		$player = $params['post']['player'];
		$pick = $params['post']['team'];

		DraftAPI->draftPlayer($player, $pick);

		echo json_encode(array($pick => $player));
		return;
    }

    function getplayers($params) {
        $filter_array = array();
        $filters = array(
            'team',
            'player_name',
            'player_position',
            'player_team'
        );
        foreach ($filters as $filter) {
            if (isset($params['post'][$filter])) {
                $filter_array[$filter] = $params['post'][$filter];
            }
        }
        $draft = new DraftAPI($params['url']['id']);
        $players = $draft->getPlayers($filter_array, isset($params['post']['available_only']))
        echo json_encode($players);
        return;
    }
}
