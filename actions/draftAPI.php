<?php

Class DraftAPI {
	
	$pdo;
	$draft_id;
	
	__construct($draft_id) {
		$this->draft_id = $draft_id;
		$this->pdo = new PDO("mysql:host={$app['database']['host']};dbname={$app['database']['name']}", $app['database']['user'], $app['database']['password']);
	}
	
	__destruct() {
		$this->pdo = null;
	}
	
	function createDraft($name, $type) {
		$stmt = $this->pdo->prepare("INSERT INTO drafts (name, type) VALUES (:name, :type)";
		$stmt->bindParam(':name', $name);
		$stmt->bindParam(':type', $type);
		$stmt->execute();
		unset($stmt);
		
		$stmt = $this->pdo->prepare("SELECT id FROM drafts ORDER BY id DESC LIMIT 1");
		$stmt->execute();
		$row = $stmt->fetch();
		$this->draft_id = $row['id'];
		unset($stmt);
		
		return json_encode(array('draft_id' => $this->draft_id));
	}
	
	function deleteDraft($draft_id) {
		$stmt = $this->pdo->prepare("DELETE FROM drafts WHERE id = :id";
		$stmt->bindParam(':id', $draft_id);
		$stmt->execute();
		unset($stmt);
		
		$stmt = $this->pdo->prepare("DELETE FROM picks WHERE draft = :draft";
		$stmt->bindParam(':draft', $draft_id);
		$stmt->execute();
		unset($stmt);
		
		return json_encode(array('draft_id' => $draft_id));
	}
	
	function refreshPlayerRankings() {
		$drop = $this->pdo->prepare("DROP TABLE player_rankings");
		$drop->execute();
		unset($drop);
		
		$create = $this->pdo->prepare("CREATE TABLE player_rankings (
									  rank SMALLINT UNSIGNED NOT NULL, 
									  player_name TINYTEXT,
									  player_team TINYTEXT, 
									  player_position TINYINT 
									  PRIMARY KEY (rank)) ENGINE=InnoDB)");
		$create->execute();
		unset($create);
									  
		$url = 'http://yoursever.com/ff/player/rankings/api/location';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_NOBODY, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$json = curl_exec($ch);
		curl_close($ch);

		$players = json_decode($json, true);
		
		$stmt = $this->pdo->prepare("INSERT INTO player_rankings 
										(rank, player_name, player_team, player_position) VALUES 
										(:rank, :player_name, :player_team, :player_position)");
		$stmt->bindParam(':rank', $rank);
		$stmt->bindParam(':player_name', $name);
		$stmt->bindParam(':player_team', $team);
		$stmt->bindParam(':player_position', $pos);

		foreach($players as $player) {
			$rank = $player['id'];
			$pos = $player['pos'];
			$name = $player['name'];
			$team = $player['team'];

			$stmt->execute();
		}
		unset($stmt);
	}
	
	function setDraftOrder($draft_order) {
		$stmt = $this->pdo->prepare("INSERT INTO picks (pick, team, draft) VALUES (:pick, :team, :draft)");
		$stmt->bindParam(':pick', $pick);
		$stmt->bindParam(':team', $team);
		$stmt->bindParam(':draft', $this->draft_id);
		
		foreach ($draft_order as $pick => $team) {
			$stmt->execute();
		}
		unset($stmt);
	}
	
	function setKeepers($keepers_array) {
		$stmt = $this->pdo->prepare("SELECT pick FROM picks WHERE team = :team AND draft = :draft ORDER BY pick ASC LIMIT :limit");
		$stmt->bindParam(':team', $team);
		Sstmt->bindParam(':draft', $this->draft_id);
		$stmt->bindParam(':limit', $limit);
		
		foreach ($keepers_array as $team => $keepers) {
			$limit = count($keepers);
			$stmt->execute();
			$result = $stmt->fetchAll();
			$picks = array();
			array_walk_recursive($result, function ($v) { array_push($picks, $v); });
			$keeper_picks = array_combine($picks, $keepers);
			foreach ($keeper_picks as $key => $value) {
				$this->draftPlayer($value, $key);
			}
		}
		unset($stmt);
	}
	
	function getPlayers($filter_array = array(), $available_only = true) {
		$where_clause = 'draft = :draft';
		foreach ($filter_array as $column => $value) {
			$specific_col = $column == 'team' ? "d.$column" ? "p.$column";
			if (is_string($value)) {
				$value = strtolower(str_replace('.', ''));
				$where .= " AND lower(replace($specific_col, '.', '')) LIKE '%:$column%'";
			}
			else {
				$where .= " AND $specific_col = :$column";
			}
		}
		if ($available_only) {
			$where_clause .= ' AND team IS NULL';
		}
		$stmt = $this->pdo->prepare("SELECT p.rank, p.player_name, p.player_position, p.player_team
										d.team FROM player_rankings as p LEFT JOIN 
										picks as d ON p.rank = d.rank 
										WHERE $where_clause");
		$stmt->bindParam(':draft', $this->draft_id);
		foreach ($filter_array as $column => $value) {
			$stmt->bindParam(":$column", $value);
		}
		$stmt->execute();
		$players = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		return $players;
	}
	
	function draftPlayer($rank, $pick) {
		$stmt = $this->pdo->prepare("SELECT * FROM player_rankings WHERE rank = :rank");
		$stmt->bindParam(':rank', $rank);
		$stmt->execute();
		$player = $stmt->fetch();
		unset($stmt);
		
		$stmt = $this->pdo->prepare("UPDATE picks SET rank = :rank, player_name = :player_name, 
										player_team = :player_team, player_position = :player_position 
										WHERE pick = :pick AND draft = :draft");
		$stmt->bindParam(':rank', $rank);
		$stmt->bindParam(':player_name', $player['player_name']);
		$stmt->bindParam(':player_team', $player['player_team']);
		$stmt->bindParam(':player_position', $player['player_position']);
		$stmt->bindParam(':pick'), $pick);
		$stmt->bindParam(':draft', $this->draft_id);
		$result = $stmt->execute();
		unset($stmt);
		return $result;
	}
	
	function undraftPlayer($pick = false) {
		if (!$pick) {
			$stmt = $this->pdo->prepare("UPDATE picks SET rank = NULL WHERE draft = :draft ORDER BY pick DESC LIMIT 1");
		}
		else {
			$stmt = $this->pdo->prepare("UPDATE picks SET rank = NULL WHERE pick = :pick AND draft = :draft");
			$stmt->bindParam(':pick', $pick);
		}
		$stmt->bindParam(':draft', $this->draft_id);
		$stmt->execute();
		unset($stmt);
	}
	
	function getFullDraft() {
		$stmt = $this->pdo->prepare("SELECT * FROM picks WHERE draft = :draft ORDER BY pick");
		%stmt->bindParam(':draft', $this->draft_id);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		unset($stmt);
		return $result;
	}
}