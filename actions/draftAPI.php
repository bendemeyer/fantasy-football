<?php
require_once('../util/constants.php');

Class DraftAPI {
	
	$pdo;
	$draft_table;
	
	__construct($draft_table) {
		$this->draft_table = mysql_real_escape_string($draft_table);
		$pdo = new PDO('mysql:host=localhost;dbname=fantasyfootball', $user, $pass);
		$stmt = $pdo->prepare("CREATE TABLE IF NOT EXISTS $this->draft_table (
									  pick SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
									  rank SMALLINT UNSIGNED, 
									  team TINYINT
									  PRIMARY KEY (pick)) ENGINE=InnoDB");
		$stmt->execute();
		unset($stmt);
	}
	
	__destruct() {
		$this->pdo = null;
	}
	
	function refreshPlayerRankings() {
		$drop = $this->pdo->prepare("DROP TABLE player_rankings");
		$drop->execute();
		unset($drop);
		
		$create = $this->pdo->prepare("CREATE TABLE player_rankings (
									  rank SMALLINT UNSIGNED NOT NULL, 
									  player_name TINYTEXT, player_team TINYTEXT, 
									  player_position TINYINT 
									  PRIMARY KEY (rank)) ENGINE=InnoDB)");
		$create->execute();
		unset($create);
									  
		$url = urldecode("http://espn.go.com/fantasy/football/story/_/id/12866396/top-300-rankings-2015");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_NOBODY, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$html = curl_exec($ch);
		curl_close($ch);

		$dom = new DOMDocument();
		$dom->strictErrorChecking = false;
		@$dom->loadHTML($html);

		$tbody = $dom->getElementsByTagName('tbody')->item(1);
		$trs = $tbody->getElementsByTagName('tr');
		
		$stmt = $this->pdo->prepare("INSERT INTO player_rankings 
											(rank, player_name, player_team, player_position) VALUES 
											(:rank, :player_name, :player_team, :player_position)");
		$stmt->bindParam(':rank', $rank);
		$stmt->bindParam(':player_name', $name);
		$stmt->bindParam(':player_team', $team);
		$stmt->bindParam(':player_position', $pos);

		foreach($trs as $tr) {
			$text = $tr->getElementsByTagName('td')->item(0)->nodeValue;
			$dot_array = explode('.', $text);
			$rank = (int) trim(array_shift($dot_array));
			$comma_array = explode(',', implode('.', $dot_array));
			$pos = $positions[strtolower(trim(array_pop($comma_array)))];
			$name = trim(implode(',', $comma_array));
			$team = trim($tr->getElementsByTagName('td')->item(1)->nodeValue);

			$stmt->execute();
		}
		unset($stmt);
	}
	
	function setDraftOrder($draft_order) {
		$stmt = $this->pdo->prepare("INSERT INTO $this->draft_table (team) VALUES (:team)");
		$stmt->bindParam(':team', $team);
		
		foreach ($draft_order as $team) {
			$stmt->execute();
		}
		unset($stmt);
	}
	
	function setKeepers($keepers_array) {
		$stmt = $this->pdo->prepare("SELECT pick FROM $this->draft_table WHERE team = :team ORDER BY pick ASC LIMIT :limit");
		$stmt->bindParam(':team', $team);
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
		$where_clause = $this->_buildWhereClause($filter_array);
		if ($available_only) {
			if (!empty($filter_array)) {
				$where_clause .= " AND ";
			}
			$where_clause .= "`team` IS NULL";
		}
		$stmt = $this->pdo->prepare("SELECT * FROM player_rankings as p LEFT JOIN 
										$this->draft_table as d ON p.rank = d.rank 
										WHERE $where_clause");
		$stmt->execute();
		$players = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		return $players;
	}
	
	function draftPlayer($rank, $pick) {
		$stmt = $this->pdo->prepare("UPDATE $this->draft_table SET rank = :rank WHERE pick = :pick");
		$stmt->bindParam(':rank', $rank);
		$stmt->bindParam(':pick'), $pick);
		$result = $stmt->execute();
		unset($stmt);
		return $result;
	}
	
	function undraftPlayer($pick = false) {
		if (!$pick) {
			$stmt = $this->pdo->prepare("UPDATE $this->draft_table SET rank = NULL ORDER BY pick DESC LIMIT 1");
		}
		else {
			$stmt = $this->pdo->prepare("UPDATE $this->draft_table SET rank = NULL WHERE pick = :pick");
			$stmt->bindParam(':pick', $pick);
		}
		$stmt->execute();
		unset($stmt);
	}
	
	private function _buildWhereClause($filter_array) {
		$where = "";
		$first = true;
		foreach ($filter_array as $key => $value) {
			if (!$first) {
				$where .= " AND ";
			}
			$first = false;
			if (is_string($value)) {
				$value = str_replace('.', '')
				$where .= "replace($key, '.', '') LIKE '%$value%'";
			}
			else {
				$where .= "`$key` = '$value'";
			}
		}
		
		return $where;
	}
}