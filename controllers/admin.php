<?php

Class AdminController extends BaseController {
	$access_level = 2;
	
	function index($params) {
		
	}
	
	function createdraft($params) {
		$draft = new DraftAPI();
		$draft_id = $draft->createDraft($params['post']['name']);
		echo $draft_id;
		exit;
	}
	
	function setdraftorder($params) {
		
	}
	
	function deletedraft($params) {
		$draft = new DraftAPI();
		$draft_id = $draft->createDraft($params['post']['id']);
		echo $draft_id;
		exit;
	}
	
	function inviteuser($params) {
		$auth = new Auth();
		$user = $auth->inviteUser($params['post']['email'], $params['post']['team']);
		echo $user;
		exit;
	}
}