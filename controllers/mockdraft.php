<?php
require_once BASE_PATH . '/actions/draftAPI.php';

Class MockDraftController extends BaseController {
	$access_level = 2;

	function index($params) {
		if (!isset($params['url']['id'])) {
			require_once $routes['error']['file'];
			$controller = new $routes['error']['controller'];
			$controller->noDraftSpecified();
		}
		$draft_table = $params['url']['id'];
		$draft = new DraftAPI($draft_table);

		$full_draft = DraftAPI->getFullDraft();
		$available = DraftAPI->getPlayers();

		$view = new View();
		$view->addParams(array(
			'full_draft' => $full_draft,
			'available'  => $available
		))->render('mockdraft');
	}
}
