<?php
// Base::Game.class.php

class Game extends User
{
	/*Vars*/
	public int $gameTime; 		//Time In Game
	public int $isRank; 		//Players Rank out of all active users
	public int $actionTurns; 	//Number of Action Turns Use has to use
	public float $inHand;		//Amount of Money On Hand
	public float $inBank;		//Amount of Money Banked
	public int $nextTurn;		//Amount of Time Till Next Turn
	public int $numMessages;    //Number of Messages In Users Inbox
	public int $uid; 			//UserID
	public int $rid;			//Race Identifier
	public array $fields;		//field List

	public function nextTurn(): int
	{
		$turnTime = 30;
		$timeIs = date("i");
		$perHr = 60 / $turnTime;
		for ($x = 1; $x <= $perHr; $x++)
		{
			if ($timeIs >= ($x-1)*$turnTime && $timeIs <= $x*$turnTime)
			{
				$this->nextTurn = ($x*$turnTime)-$timeIs;
			}
		}
		return $this->nextTurn;
	}
	
	public function getRaces(): array
	{
		$query = "SELECT `r_name`,`rid` FROM `race` LIMIT 30";
		$q = $this->query($query);		
		$list = [];
		$counter = 0;
		while($obj = $q->fetch_object())
		{
			$list[$counter]["name"] 	= $obj->r_name;
			$list[$counter]["id"] 		= $obj->rid;
			$counter++;
		}
		return $list;
	}
	
	public function autoLoad(): string
	{
		$query = "SELECT rank.overall AS isRank, bank.onHand, bank.inBank, userdata.actionTurns, (SELECT COUNT(messages.toUID) FROM `messages` RIGHT OUTER JOIN `userdata` ON messages.toUID = userdata.uid WHERE userdata.uid = ? GROUP BY userdata.uid) AS messageCount FROM `bank`,`userdata`,`rank` WHERE bank.uid=? AND userdata.uid = bank.uid  AND rank.uid = bank.uid LIMIT 1";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("ii", $_SESSION['userid'], $_SESSION['userid']);
		$stmt->execute();
		$q = $stmt->get_result();
		$auto = $q->fetch_object();
		$gameTime 	= date("F jS H:i:s");
		$str = "new Array(\"".number_format($auto->onHand)."\",\"".number_format($auto->inBank)."\",\""
		       .number_format($auto->isRank)."\",\"".number_format($auto->actionTurns)."\",\""
			   .$gameTime."\",\"".number_format($auto->messageCount)."\",\"".$this->nextTurn()." minutes\")";
		$_SESSION['money'] = $auto->onHand;
		return $str;
	}	
	
	public function messageCount(): string
	{	
		Debug::printMsg(__CLASS__, __FUNCTION__, "Getting Count of Messages");
		$query = "SELECT count(`message`) FROM `messages` WHERE `toUID`=? LIMIT 1000";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("i", $_SESSION['userid']);
		$stmt->execute();
		$q = $stmt->get_result();
		$x = $q->num_rows;
		return number_format($x);
	}
	
	public function baseVars(): object
	{
		$query = "SELECT     
					users.uid, 
					users.uname, 
					users.email,
					users.allyid,					
					userdata.link,
					race.r_name,
					users_1.uid AS cid, 
					users_1.uname AS cname, 
					planetsize.text, 
					planets.plnt_name,
					(SELECT     COUNT(planets.pid) FROM planets RIGHT OUTER JOIN `userdata` 
					ON planets.uid = userdata.uid WHERE userdata.uid = ? GROUP BY userdata.uid) AS `ttlPlanetsOwned`,
					((units.miners *(80+technology.income)) + ( units.lifers *(80+technology.income) ) + ( SUM( planets.income_bonus ) ) + (race.income_bonus*((units.miners *(80+technology.income))) + ( units.lifers *(80+technology.income)))) AS income,
					((technology.unitProd*(3+technology.uppl))+SUM( planets.up_bonus )+(race.up_bonus*(technology.unitProd*(3+technology.uppl)))) AS up
					FROM `users` 
					INNER JOIN `userdata` ON users.uid = userdata.uid
					INNER JOIN `race` ON userdata.rid = race.rid 
					LEFT OUTER JOIN `units` ON userdata.uid = units.uid 
					LEFT OUTER JOIN `planets` ON userdata.uid = planets.uid 
					LEFT OUTER JOIN `users` users_1 ON userdata.cid = users_1.uid 
					LEFT OUTER JOIN `planetsize` ON planets.plnt_size = planetsize.size
					LEFT OUTER JOIN `technology` ON userdata.uid = technology.uid
					WHERE users.uid = ? GROUP BY users.uid";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("ii", $_SESSION['userid'], $_SESSION['userid']);
		$stmt->execute();
		$q = $stmt->get_result();
		$base = $q->fetch_object();
		return $base;
	} 
	
	public function getRanks(): object
	{
		$query = "SELECT
					rank.overall	AS rank,
					rank.mil_atk	AS milAtkRank,
					rank.mil_def	AS milDefRank,
					rank.mil_cov	AS milCovRank,
					rank.mil_anti	AS milAntiRank,
					rank.mil_total	AS milRank,
					power.mil_atk	AS milAtk,
					power.mil_def	AS milDef,
					power.mil_cov	AS milCov,
					power.mil_anti	AS milAnti,
					SUM(rank.mil_atk+rank.mil_def+rank.mil_cov+rank.mil_anti) AS mil
					FROM rank,power
					WHERE rank.uid = ? AND power.uid = rank.uid GROUP BY rank.uid
					LIMIT 1";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("i", $_SESSION['userid']);
		$stmt->execute();
		$q = $stmt->get_result();
		$ranks = $q->fetch_object();
		return $ranks;
	}
	
	public function getPersonnel(int $uid): ?object
	{
		$query = "SELECT 
					units.attack 		AS attackCount, 
					units.superAttack 	AS superAttackCount, 
					units.attackMercs 	AS attackMercCount,
					units.defense		AS defenseCount,
					units.superDefense	AS superDefenseCount,
					units.defenseMercs	AS defenseMercCount,
					units.untrained		AS uuCount,
					units.miners 		AS minerCount,
					units.lifers		AS liferCount,
					units.covert		AS covertCount,
					units.superCovert	AS superCovertCount,
					units.anticovert	AS anticovertCount,
					units.superAnticovert	AS superAnticovertCount,
					unitnames.attack 	AS attackName, 
					unitnames.superAttack 	AS superAttackName, 
					unitnames.attackMercs 	AS attackMercName,
					unitnames.defense	AS defenseName,
					unitnames.superDefense	AS superDefenseName,
					unitnames.defenseMercs	AS defenseMercName,
					unitnames.covert	AS covertName,
					unitnames.superCovert	AS superCovertName,
					unitnames.anticovert	AS anticovertName,
					unitnames.superAnticovert AS superAnticovertName,
					unitcost.attack 	AS attackCost, 
					unitcost.superAttack 	AS superAttackCost, 
					unitcost.defense	AS defenseCost,
					unitcost.superDefense	AS superDefenseCost,
					unitcost.covert	AS covertCost,
					unitcost.superCovert	AS superCovertCost,
					unitcost.anticovert	AS anticovertCost,
					unitcost.superAnticovert AS superAnticovertCost,
					SUM( units.attack+ units.superAttack+ units.attackMercs+ units.defense+ units.superDefense+ units.defenseMercs+ units.untrained+ units.miners+ units.lifers+ units.covert+ units.superCovert+ units.anticovert+ units.superAnticovert ) AS ttlarmysize
					FROM `units`, `unitnames`,`userdata`,`unitcost` WHERE userdata.uid = ? AND unitnames.rid = userdata.rid AND units.uid = userdata.uid AND unitcost.rid = userdata.rid GROUP BY userdata.uid LIMIT 1";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("i", $uid);
		$stmt->execute();
		$q = $stmt->get_result();
		if ($q->num_rows > 0) {
			$person = $q->fetch_object();
			return $person;
		} else {
			return null;
		}
	}
	
	public function getOfficers(int $uid): array
	{
		Debug::printMsg(__CLASS__, __FUNCTION__, "Retrieving Officers");
		$query = "SELECT userdata.uid, userdata.uname , race.r_name , rank.overall, (SELECT SUM( units.attack+ units.superAttack+ units.attackMercs+ units.defense+ units.superDefense+ units.defenseMercs+ units.untrained+ units.miners+ units.lifers+ units.covert+ units.superCovert+ units.anticovert+ units.superAnticovert) FROM `units` WHERE uid=?) AS ttlarmy, (SELECT SUM( units.attackMercs+ units.defenseMercs) FROM `units` WHERE uid=?) AS mercs
				  FROM `userdata` , `users` , `race` , `rank`
				  WHERE userdata.cid =?
				  AND users.uid = userdata.uid
				  AND userdata.rid = race.rid
				  AND userdata.uid = rank.uid
				  ORDER BY `overall` ASC
				  LIMIT 100 ";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("iii", $uid, $uid, $uid);
		$stmt->execute();
		$q = $stmt->get_result();
		$officers = [];
		$num = 0;
		while($offlist = $q->fetch_assoc())
		{
			$officers[$num] = [];
			$officers[$num]["uid"]   = $offlist["uid"];
			$officers[$num]["name"]  = $offlist["uname"];
			$officers[$num]["rank"]  = $offlist["overall"];
			$officers[$num]["race"]  = $offlist["r_name"];
			$officers[$num]["size"]  = $offlist["ttlarmy"];
			$officers[$num]["mercs"] = $offlist["mercs"];
			$num++; 
		}
		return $officers;
	}
	
	public function Rankings(int $pnum=1): array
	{
		Debug::printMsg(__CLASS__, __FUNCTION__, "Retrieving Ranks");
		$rankings = [];
		$perpage = 25;
		$page = [1, $perpage]; // Selects Page
		$page[0] = 0 + ( ( $pnum - 1 ) * $perpage);
		$page[1] = ($pnum*$perpage)-1;
		$counter = 0; //SO it can just keep adding to array
		$query = "SELECT SUM(mil_cov + mil_anti) AS covact FROM `power` WHERE uid=?";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("i", $_SESSION['userid']);
		$stmt->execute();
		$q = $stmt->get_result();
		$userStats = $q->fetch_object();
		
		$query = "SELECT rank.overall, users.uid,users.allyid,bank.onHand,power.mil_cov,power.mil_anti,race.r_name,users.uname,
				  SUM( units.attack+ units.superAttack+ units.attackMercs+ units.defense+ units.superDefense+ units.defenseMercs+ units.untrained+ units.miners+ units.lifers+ units.covert+ units.superCovert+ units.anticovert+ units.superAnticovert) AS armySize
		       	  FROM `rank`,`users`,`userdata`,`race`,`bank`,`power`,`units`
				  WHERE userdata.rid = race.rid	
				  AND users.uid = userdata.uid 
				  AND userdata.uid = power.uid 
				  AND power.uid=users.uid 
				  AND users.uid = bank.uid 
				  AND rank.uid = bank.uid
				  AND units.uid = rank.uid
				  GROUP BY users.uid
				  ORDER BY rank.overall ASC";
		$q = $this->query($query);
		while ($rank = $q->fetch_object())
		{
			$xfact = $rank->mil_cov + $rank->mil_anti; //Covert Defense See if You Can see stats
			$rankings[$counter]['uid'] = $rank->uid;
			$rankings[$counter]['allyid'] = $rank->allyid;
			$rankings[$counter]['name'] = $rank->uname;
			$rankings[$counter]['rank'] = number_format($rank->overall);
			
			if ($userStats->covact < .2 * $xfact)
			{
				$rankings[$counter]['army'] = "??????";
			}
			else
			{
 				$rankings[$counter]['army'] = number_format($rank->armySize);
			}
			
			$rankings[$counter]['race'] = $rank->r_name;
			
			if ($userStats->covact < .25 * $xfact)
			{
				$rankings[$counter]['cash'] = "??????";
			}
			else
			{
 				$rankings[$counter]['cash'] = number_format($rank->onHand);
			}
			
			$counter++;
		}
		return $rankings;
		
	}
	public function allyRankings(int $pnum=1, int $allyid): array
	{
		Debug::printMsg(__CLASS__, __FUNCTION__, "Retrieving  alliance Rankings");
		$rankings = [];
		$perpage = 25;
		$page = [1, $perpage]; // Selects Page
		$page[0] = 0 + ( ( $pnum - 1 ) * $perpage);
		$page[1] = ($pnum*$perpage)-1;
		$counter = 0; //SO it can just keep adding to array
		$query = "SELECT SUM(mil_cov + mil_anti) AS covact FROM `power` WHERE uid=?";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("i", $_SESSION['userid']);
		$stmt->execute();
		$q = $stmt->get_result();
		$userStats = $q->fetch_object();
		
		$query = "SELECT rank.overall, users.uid,users.allyid,bank.onHand,power.mil_cov,power.mil_anti,race.r_name,users.uname,
				  SUM( units.attack+ units.superAttack+ units.attackMercs+ units.defense+ units.superDefense+ units.defenseMercs+ units.untrained+ units.miners+ units.lifers+ units.covert+ units.superCovert+ units.anticovert+ units.superAnticovert) AS armySize
		       	  FROM `rank`,`users`,`userdata`,`race`,`bank`,`power`,`units`
				  WHERE userdata.rid = race.rid	
				  AND users.uid = userdata.uid 
				  AND userdata.uid = power.uid 
				  AND power.uid=users.uid 
				  AND users.uid = bank.uid 
				  AND rank.uid = bank.uid
				  AND units.uid = rank.uid
				  AND users.allyid = ? 
				  GROUP BY users.uid
				  ORDER BY rank.overall ASC";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("ii", $_SESSION['userid'], $allyid);
		$stmt->execute();
		$q = $stmt->get_result();
		while ($rank = $q->fetch_object())
		{
		
		
			$xfact = $rank->mil_cov + $rank->mil_anti; //Covert Defense See if You Can see stats
			$rankings[$counter]['uid'] = $rank->uid;
			$rankings[$counter]['name'] = $rank->uname;
			$rankings[$counter]['rank'] = number_format($rank->overall);
			$rankings[$counter]['allyid'] = $rank->allyid;
			
			if ($userStats->covact < .2 * $xfact)
			{
				$rankings[$counter]['army'] = "??????";
			}
			else
			{
 				$rankings[$counter]['army'] = number_format($rank->armySize);
			}
			
			$rankings[$counter]['race'] = $rank->r_name;
			
			if ($userStats->covact < .25 * $xfact)
			{
				$rankings[$counter]['cash'] = "??????";
			}
			else
			{
 				$rankings[$counter]['cash'] = number_format($rank->onHand);
			}
			
			$counter++;
		}
		return $rankings;
		
	}
	public function getallyinfo(int $allyid): object
	{
	Debug::printMsg(__CLASS__, __FUNCTION__, "Retrieving alliance info");
	$query = "SELECT *
					FROM alliances
					WHERE alliances.allyid = ? 
					LIMIT 1";
	$stmt = $this->db_link->prepare($query);
	$stmt->bind_param("i", $allyid);
	$stmt->execute();
	$q = $stmt->get_result();
	$ranks = $q->fetch_object();
	return $ranks ?? (object)[];
	}
	public function getUserInfo(int $uid): object
	{
		$query = "SELECT SUM(mil_cov + mil_anti) AS covact FROM `power` WHERE uid=?";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("i", $_SESSION['userid']);
		$stmt->execute();
		$q = $stmt->get_result();
		$myStats = $q->fetch_object();

		$query = "SELECT
			users.uname AS userName,
			rank.overall as rank,
			SUM(power.mil_cov+ power.mil_anti) as `covPro`,
			(SELECT users.uname FROM users,userdata WHERE userdata.uid=? AND users.uid = userdata.cid) AS `cmdrName`,
			userdata.cid as `cmdrID`,
			(SELECT r_name FROM race WHERE rid=(SELECT rid FROM userdata WHERE uid=?)) AS race,
			bank.onHand,
			SUM( units.attack+ units.superAttack+ units.attackMercs+ units.defense+ units.superDefense+ units.defenseMercs+ units.untrained+ units.miners+ units.lifers+ units.covert+ units.superCovert+ units.anticovert+ units.superAnticovert ) as armySize
			FROM users, units, bank, userdata, power,rank
			WHERE userdata.uid=?
			AND users.uid = userdata.uid
			AND bank.uid = userdata.uid
			AND units.uid = userdata.uid
			AND power.uid = userdata.uid
			AND rank.uid = userdata.uid
			GROUP BY users.uid LIMIT 1";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("iii", $_SESSION['userid'], $uid, $uid);
		$stmt->execute();
		$q = $stmt->get_result();
		$userStats = $q->fetch_object();
		if ($myStats->covact < .2 * $userStats->covPro)
		{
			$userStats->armySize = "??????";
		}
		else
		{
 			$userStats->armySize = number_format($userStats->armySize);
		}
		
		if ($myStats->covact < .25 * $userStats->covPro)
		{
			$userStats->onHand = "??????";
		}
		else
		{
 			$userStats->onHand = number_format($userStats->onHand);
		}
		
		if($userStats->cmdrName == "")
		{
			$userStats->cmdrName = "None";
		}
		
		return $userStats;
	}
	
	public function getWeapons(): array
	{
		Debug::printMsg(__CLASS__, __FUNCTION__, "Retrieving Weapons Currently buyable by player");
		$query = "SELECT `isDefense`,`cash_cost`,`unit_cost`,`weaponName`,`weaponPower`,`wid`
		          FROM `armory`
				  WHERE armory.rid = ?
				  ORDER BY `weaponPower` ASC
				  LIMIT 100";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("i", $_SESSION['raceID']);
		$stmt->execute();
		$q = $stmt->get_result();
		$weapons = []; //3d Array for Defense and ATtack Weapons
		$defCounter = 0;
		$atkCounter = 0;
		while($weaps = $q->fetch_object())
		{
			if($weaps->isDefense == 1)
			{
				$weapons['def'][$defCounter]['name'] 		= $weaps->weaponName;
				$weapons['def'][$defCounter]['power'] 		= $weaps->weaponPower;
				$weapons['def'][$defCounter]['cashcost'] 	= $weaps->cash_cost;
				$weapons['def'][$defCounter]['unitcost'] 	= $weaps->unit_cost;
				$weapons['def'][$defCounter]['wid'] 		= $weaps->wid;
				$weapons['def'][$defCounter]['fieldname'] 	= "def".$defCounter;
				$defCounter++;
			}else{
				$weapons['atk'][$atkCounter]['name'] 		= $weaps->weaponName;
				$weapons['atk'][$atkCounter]['power'] 		= $weaps->weaponPower;
				$weapons['atk'][$atkCounter]['cashcost'] 	= $weaps->cash_cost;
				$weapons['atk'][$atkCounter]['unitcost'] 	= $weaps->unit_cost;
				$weapons['atk'][$atkCounter]['wid'] 		= $weaps->wid;
				$weapons['atk'][$atkCounter]['fieldname'] 	= "atk".$atkCounter;
				$atkCounter++;
			}
		}
		
		return $weapons;			
	}
	
	public function getWeaponInventory(int $uid): array
	{
		Debug::printMsg(__CLASS__, __FUNCTION__, "Retrieving UserID($uid) Weapon Inventory");
		$weapons = []; //3d Array for Defense and ATtack Weapons
		$defCounter = 0;
		$atkCounter = 0;
		$query = "SELECT armory.wid, armory.weaponName , weapons.strength, armory.weaponPower, 
		                 armory.cash_cost, armory.isDefense, weapons.quanity
			  	  FROM `armory`,`weapons`,`userdata`
				  WHERE weapons.uid = ?
				  AND armory.wid = weapons.wid
				  AND userdata.uid = weapons.uid
				  AND armory.rid = userdata.rid
				  ORDER BY armory.weaponPower ASC
				  LIMIT 1000";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("i", $uid);
		$stmt->execute();
		$q = $stmt->get_result();		 
		while($weaps = $q->fetch_object())
		{
			if($weaps->isDefense == 1)
			{
				$weapons['def'][$defCounter]['wid'] 		= $weaps->wid;
				$weapons['def'][$defCounter]['name'] 		= $weaps->weaponName;
				$weapons['def'][$defCounter]['quanity']		= $weaps->quanity;
				$weapons['def'][$defCounter]['power'] 		= $weaps->weaponPower;
				$weapons['def'][$defCounter]['strength']	= $weaps->strength;
				$weapons['def'][$defCounter]['sell']	 	= ($weaps->cash_cost*($weaps->strength/$weaps->weaponPower)) * .80;
				$weapons['def'][$defCounter]['perpoint'] 	= round(($weaps->cash_cost*.5)/$weaps->weaponPower)*$weaps->quanity;
				$weapons['def'][$defCounter]['fieldname'] 	= "defsel".$defCounter;
				$defCounter++;
			}else{
				$weapons['atk'][$atkCounter]['wid'] 		= $weaps->wid;
				$weapons['atk'][$atkCounter]['name'] 		= $weaps->weaponName;
				$weapons['atk'][$atkCounter]['quanity']		= $weaps->quanity;
				$weapons['atk'][$atkCounter]['power'] 		= $weaps->weaponPower;
				$weapons['atk'][$atkCounter]['strength'] 	= $weaps->strength;
				$weapons['atk'][$atkCounter]['sell'] 		= ($weaps->cash_cost*($weaps->strength/$weaps->weaponPower)) * .80;
				$weapons['atk'][$atkCounter]['perpoint'] 	= round(($weaps->cash_cost*.5)/$weaps->weaponPower)*$weaps->quanity;
				$weapons['atk'][$atkCounter]['fieldname'] 	= "atksel".$atkCounter;
				$atkCounter++;
			}
		}
		return $weapons;
	}
	
	public function updatePower(int $uid): void
	{
		Debug::printMsg(__CLASS__, __FUNCTION__, "Updating User Power Totals");		
		$query = "SELECT rid FROM userdata WHERE uid=? LIMIT 1";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("i", $uid);
		$stmt->execute();
		$q = $stmt->get_result();
		$fetched = $q->fetch_object();
		$rid = $fetched->rid;
	
		$weaponQuery = "SELECT weapons.quanity, weapons.strength, armory.weaponPower, armory.isDefense, armory.requireTrained
						FROM `weapons`,`armory`
						WHERE weapons.uid =?
						AND armory.wid = weapons.wid
						AND armory.rid =?
						ORDER BY `weaponPower` DESC 
						LIMIT 1000";
		$pBonusQuery = "SELECT * FROM `planets` WHERE `uid`=? LIMIT 1000";
		$comboQuery = "SELECT technology.cov_lvl ,technology.anti_lvl, technology.covert as techcovert, technology.anticovert as techanti, technology.attack as techattack, technology.defense as techdefense, race.* ,  units.`attack` ,  units.`superAttack` ,  units.`attackMercs` ,  units.`defense` ,  
						units.`superDefense` ,  units.`defenseMercs` ,  units.`covert` ,  units.`superCovert` ,  units.`anticovert` ,  units.`superAnticovert` 
						FROM  `units` ,  `technology` ,  `race` 
						WHERE technology.uid =?
						AND units.uid = technology.uid 
						AND race.rid =?
						LIMIT 1 ";			
		/*mySQL connection and query*/
		$stmt = $this->db_link->prepare($weaponQuery);
		$stmt->bind_param("ii", $uid, $rid);
		$stmt->execute();
		$weapon = $stmt->get_result();
		$stmt = $this->db_link->prepare($pBonusQuery);
		$stmt->bind_param("i", $uid);
		$stmt->execute();
		$pBonus = $stmt->get_result();
		$stmt = $this->db_link->prepare($comboQuery);
		$stmt->bind_param("ii", $uid, $rid);
		$stmt->execute();
		$combo	= $stmt->get_result();
	
		/*Object Declarations from MYSQL*/
		$comboObj = $combo->fetch_object();
			
		/*Covert and Anticovert Calulations*/
		$cSpys 		= (5*$comboObj->covert) + ( 10 * $comboObj->superCovert );
		$aSpys 		= (5*$comboObj->anticovert) + ( 10 * $comboObj->superAnticovert );
		$c_tBonus 	= $comboObj->cov_lvl ?? 0;
		$a_tBonus	= $comboObj->anti_lvl ?? 0;
		$c_pBonus	= 0;
		while ($pBonObj=$pBonus->fetch_object()) 
		{ 
			$c_pBonus += $pBonObj->cov_bonus ?? 0; 
		}
		
		$c_rBonus 	= $comboObj->cov_bonus ?? 0;
	

		$covert 	= round((((( sqrt( pow(2,$comboObj->cov_lvl) ) * $cSpys * (1+$c_tBonus) * (1+$c_rBonus) ) + $cSpys ) * 10 )+ $c_pBonus)*(float)(1+($comboObj->techcovert/10)));

		$anticovert = round((( ( ( sqrt( pow(2,$comboObj->anti_lvl+2) ) * $aSpys * (1+$a_tBonus) * (1+$c_rBonus) ) + $aSpys ) * 10 )+ $c_pBonus)*(float)(1+($comboObj->techanti/10)));
		
		/*Attack Declaraions*/
		$superAttack 	= $comboObj->superAttack;
		$attack			= $comboObj->attack;
		$aMercs			= $comboObj->attackMercs;
		$attackpower	= (float)0;
		$sAused			= 0;
		$aused			= 0;
		$aMused			= 0;
		
		/*Defense Declarations*/
		$superDefense 	= $comboObj->superDefense;
		$defense		= $comboObj->defense;
		$dMercs			= $comboObj->defenseMercs;
		$defensepower	= (float)0;
		$sDused			= 0;
		$dused			= 0;
		$aDused			= 0;
		
		while ($weaponobj = $weapon->fetch_object())
		{
			if($weaponobj->requireTrained==0)
			{
				if($weaponobj->isDefense == 0)
				{
					if($weaponobj->strength > $weaponobj->weaponPower)
					{
						$attackpower += $weaponobj->weaponPower*$weaponobj->quanity;
					}else{
						$attackpower += $weaponobj->strength*$weaponobj->quanity;
					}
				}elseif($weaponobj->isDefense == 1){
					if($weaponobj->strength > $weaponobj->weaponPower)
					{
						$defensepower += $weaponobj->weaponPower*$weaponobj->quanity;
					}else{
						$defensepower += $weaponobj->strength*$weaponobj->quanity;
					}
				}			
			}
			/*Attack Calculations*/
			if($weaponobj->isDefense == 0 && $weaponobj->requireTrained==1)
			{
				$quanity 	= $weaponobj->quanity;
				$q_used  	= 0;
				$sA 		= $superAttack - $sAused;
				$a 			= $attack - $aused;
				$aM			= $aMercs - $aMused;	

				/* This Calculates Weapons Power*/
				$weapon_power = 0;
				if ($weaponobj->strength > $weaponobj->weaponPower)
				{
					$weapon_power = $weaponobj->weaponPower;
				}
				else
				{
					$weapon_power = $weaponobj->strength;
				}
					
				/*Calculates Super Attack Unit Power*/			
				if(!$sA <= 0)
				{
					if ($quanity > $sA)
					{
						$q_used = $sA;
						$quanity -= $q_used;
						$sAused -= $q_used;
						$attackpower += ($q_used * $weapon_power) * 10;
					}
					else
					{
						$q_used = $quanity;
						$quanity -= $q_used;
						$sAused -= $q_used;
						$attackpower += ($q_used * $weapon_power) * 10;
					}
				}
				/*Calculates regualer Attacker*/			
				if(!quanity  <= 0 && !$a <=0 )
				{
					if ($quanity > $a)
					{
						$q_used = $a;
						$quanity -= $q_used;
						$aused -= $q_used;
						$attackpower += ($q_used * $weapon_power) * 5;
					}
					else
					{
						$q_used = $quanity;
						$quanity -= $q_used;
						$aused -= $q_used;
						$attackpower += ($q_used * $weapon_power) * 5;
					}
				}
				/*Calculated Mercs Attack*/
				if(!quanity == 0 && !$aM <=0)
				{
					if ($quanity > $aM)
					{
						$q_used = $aM;
						$quanity -= $q_used;
						$aMused -= $q_used;
						$attackpower += ($q_used * $weapon_power) * 5;
					}else{
						$q_used = $quanity;
						$quanity -= $q_used;
						$aMused -= $q_used;
						$attackpower += ($q_used * $weapon_power) * 5;
					}
				}
			}
			elseif($weaponobj->isDefense == 1 && $weaponobj->requireTrained==1)
			{
				$quanity 	= $weaponobj->quanity;
				$q_used  	= 0;
				$sD 		= $superDefense - $sDused;
				$d 		= $defense - $dused;
				$dM		= $dMercs - $aDused;	
				/* This Calculates Weapons Power*/
				$weapon_power = 0;
				if ($weaponobj->strength > $weaponobj->weaponPower)
				{
					$weapon_power = $weaponobj->weaponPower;
				}
				else
				{
					$weapon_power = $weaponobj->strength;
				}
				
				/*Calculates Super Attack Unit Power*/			
				if(!$sD <= 0)
				{
					if ($quanity > $sD)
					{
						$q_used = $sD;
						$quanity -= $q_used;
						$sDused -= $q_used;
						$defensepower += ($q_used * $weapon_power) * 10;
					}
					else
					{
						$q_used = $quanity;
						$quanity -= $q_used;
						$sDused -= $q_used;
						$defensepower += ($q_used * $weapon_power) * 10;
					}
				}
				/*Calculates regualer Attacker*/			
				if(!quanity  <= 0 && !$d <=0 )
				{
					if ($quanity > $d)
					{
						$q_used = $d;
						$quanity -= $q_used;
						$dused -= $q_used;
						$defensepower += ($q_used * $weapon_power) * 5;
					}
					else
					{
						$q_used = $quanity;
						$quanity -= $q_used;
						$dused -= $q_used;
						$defensepower += ($q_used * $weapon_power) * 5;
					}
				}
				/*Calculated Mercs Attack*/
				if(!quanity == 0 && !$dM <=0)
				{
					if ($quanity > $dM)
					{
						$q_used = $dM;
						$quanity -= $q_used;
						$dMused -= $q_used;
						$defensepower += ($q_used * $weapon_power) * 5;
					}else{
						$q_used = $quanity;
						$quanity -= $q_used;
						$dMused -= $q_used;
						$defensepower += ($q_used * $weapon_power) * 5;
					}
				}
			}
		}
		$attackpower += (($comboObj->techattack/10) *$attackpower);
		$defensepower += (($comboObj->techdefense/10) *$defensepower);
		/*Queries Planets for Bonuses*/
		while($pBonObj=$pBonus->fetch_object())
		{
			$attackpower += $pBonObj->atk_bonus;
			$defensepower += $pBonObj->def_bonus;
		}
	
		/*Query Database for Race Bonus*/
	
		$attackpower += ($comboObj->atk_bonus ?? 0) * $attackpower;	
		$defensepower += ($comboObj->def_bonus ?? 0) * $defensepower;	
	
		$query = "UPDATE `power` SET `mil_atk` = ?,`mil_def` = ?,`mil_cov` = ?,`mil_anti` = ? WHERE `uid` =? LIMIT 1";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("ddddd", $attackpower, $defensepower, $covert, $anticovert, $uid);
		$stmt->execute();
		$this->fields = ["unitProd","uppl","income","galaxy","puCap","pmCap","attack","auSteal","auEffect","auRes","defense","duSteal","duEffect","duRes","covert","cuEffect","cuRes","anticovert","acuEffect","acuRes","cov_lvl","anti_lvl","pDef","ascend"];
	}

	public function buyWeapons(array $data): void
	{
		Debug::printMsg(__CLASS__, __FUNCTION__, "buying Weapons");
		$weapons = $this->getWeapons();
		$cashcost = 0;
		$unitcost = 0;
		$query = "SELECT bank.onHand, units.untrained FROM bank, units WHERE bank.uid=? AND units.uid = bank.uid";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("i", $_SESSION['userid']);
		$stmt->execute();
		$q = $stmt->get_result();
		$stats = $q->fetch_object();
		for ($x = 0; $x < count($weapons['atk']); $x++)
		{
			$cashcost += $data[$weapons['atk'][$x]['fieldname']]*$weapons['atk'][$x]['cashcost'];
			$unitcost += $data[$weapons['atk'][$x]['fieldname']]*$weapons['atk'][$x]['unitcost'];
		}
		for ($x = 0; $x < count($weapons['def']); $x++)
		{
			$cashcost += $data[$weapons['def'][$x]['fieldname']]*$weapons['def'][$x]['cashcost'];
			$unitcost += $data[$weapons['def'][$x]['fieldname']]*$weapons['def'][$x]['unitcost'];
		}
		$unitsavail = $stats->untrained;
		$cashavail = $stats->onHand;
		
		if ($cashavail >= $cashcost && $unitsavail >= $unitcost && !$cashcost <= 0)
		{
			for ($x = 0; $x < count($weapons['def']); $x++)
			{
				
				if(!$data[$weapons['def'][$x]['fieldname']] <=0)
				{
					$query = "SELECT `quanity` FROM `weapons` WHERE `uid`=? AND `wid`=? LIMIT 1";
					$stmt = $this->db_link->prepare($query);
					$stmt->bind_param("ii", $_SESSION['userid'], $weapons['def'][$x]['wid']);
					$stmt->execute();
					$q = $stmt->get_result();
					$rows = $q->num_rows;
					$obj = $q->fetch_object();
					if ($rows == 1)
					{
						$quan = $obj->quanity + $data[$weapons['def'][$x]['fieldname']];
						$query = "UPDATE `weapons` SET `quanity`= ? WHERE `uid` =? AND `wid`=? LIMIT 1";
						$stmt = $this->db_link->prepare($query);
						$stmt->bind_param("iii", $quan, $_SESSION['userid'], $weapons['def'][$x]['wid']);
						$stmt->execute();
					}elseif($rows == 0){
						$init = "SELECT `weaponPower` FROM `armory` WHERE wid=? LIMIT 1";
						$stmt = $this->db_link->prepare($init);
						$stmt->bind_param("i", $weapons['def'][$x]['wid']);
						$stmt->execute();
						$q = $stmt->get_result();
						$power = $q->fetch_object();
						$query = "INSERT INTO `weapons` ( `uid` , `wid` , `quanity` , `strength` )
								  VALUES (?, ?, ?, ?)";
						$stmt = $this->db_link->prepare($query);
						$stmt->bind_param("iiii", $_SESSION['userid'], $weapons['def'][$x]['wid'], $data[$weapons['def'][$x]['fieldname']], $power->weaponPower);
						$stmt->execute();
					}
				}
			}
			for ($x = 0; $x < count($weapons['atk']); $x++)
			{
				
				if(!$data[$weapons['atk'][$x]['fieldname']] <=0)
				{
					$query = "SELECT `quanity` FROM `weapons` WHERE `uid`=? AND `wid`=? LIMIT 1";
					$stmt = $this->db_link->prepare($query);
					$stmt->bind_param("ii", $_SESSION['userid'], $weapons['atk'][$x]['wid']);
					$stmt->execute();
					$q = $stmt->get_result();
					$rows = $q->num_rows;
					$obj = $q->fetch_object();
					if ($rows == 1)
					{
						$quan = $obj->quanity + $data[$weapons['atk'][$x]['fieldname']];
						$query = "UPDATE `weapons` SET `quanity`= ? WHERE `uid` =? AND `wid`=? LIMIT 1";
						$stmt = $this->db_link->prepare($query);
						$stmt->bind_param("iii", $quan, $_SESSION['userid'], $weapons['atk'][$x]['wid']);
						$stmt->execute();
					}elseif($rows == 0){
						$init = "SELECT `weaponPower` FROM `armory` WHERE wid=? LIMIT 1";
						$stmt = $this->db_link->prepare($init);
						$stmt->bind_param("i", $weapons['atk'][$x]['wid']);
						$stmt->execute();
						$q = $stmt->get_result();
						$power = $q->fetch_object();
						$query = "INSERT INTO `weapons` ( `uid` , `wid` , `quanity` , `strength` )
								  VALUES (?, ?, ?, ?)";
						$stmt = $this->db_link->prepare($query);
						$stmt->bind_param("iiii", $_SESSION['userid'], $weapons['atk'][$x]['wid'], $data[$weapons['atk'][$x]['fieldname']], $power->weaponPower);
						$stmt->execute();
					}
				}
			}
			$endcash = (float) number_format($cashavail,'0','','')-number_format($cashcost,'0','','');
			$endunit = $unitsavail-$unitcost;
			$query = "UPDATE `bank` SET `onHand`=? WHERE `uid`=? LIMIT 1";
			$stmt = $this->db_link->prepare($query);
			$stmt->bind_param("di", $endcash, $_SESSION['userid']);
			$stmt->execute();
			$query = "UPDATE `units` SET `untrained`= ? WHERE `uid` =? LIMIT 1";
			$stmt = $this->db_link->prepare($query);
			$stmt->bind_param("ii", $endunit, $_SESSION['userid']);
			$stmt->execute();
			echo "Purchase Successful";
		}elseif($unitsavail < $unitcost && $cashavail < $cashcost){
			echo "Not Enough Cash or Units";
		}elseif($cashavail < $cashcost){
			echo "Not Enough Cash";
		}elseif($unitsavail < $unitcost){
			echo "Not Enough Units";
		}
	}//update to include withdrawl from Bank
	
	public function trainUnits(int $atk, int $uberAtk, int $def, int $uberDef, int $miners, int $cov, int $uberCov, int $anti, int $uberAnti): void
	{
		Debug::printMsg(__CLASS__, __FUNCTION__, "Training Units");
		$this->autoLoad();
		$trn = $this->getPersonnel($_SESSION['userid']);
		$cashcost = (float)($atk*$trn->attackCost)+
					($uberAtk*$trn->superAttackCost)+
					($def*$trn->defenseCost)+
		            ($uberDef*$trn->superDefenseCost)+
					($miners*1500)+
					 ($cov*$trn->covertCost)+
					($uberCov*$trn->superCovertCost)+
					($anti*$trn->anticovertCost)+
					($uberAnti*$trn->superAnticovertCost);
					if (($atk < 0) OR ($def < 0) OR ($miners < 0) OR ($cov < 0) OR ($anti < 0) OR ($uberDef < 0) OR ($uberAtk < 0) OR ($uberCov < 0) OR ($uberAnti < 0)) {
$atk = 0;$uberAtk=0;$def=0;$uberDef=0;$miners=0;$cov=0;$uberCov=0;$anti=0;$uberAnti=0;
}
		$unitcost = $atk+$def+$miners+$cov+$anti;
		Debug::printMsg(__CLASS__, __FUNCTION__, "Costs Calculated ( $cashcost  &  $unitcost )");
		/*-------------------------------------------------------------------------*/
		$unitsavail = $trn->uuCount;
		$cashavail = number_format($_SESSION['money'], 0, '', '');
		Debug::printMsg(__CLASS__, __FUNCTION__, "Units Available Calculated ( $cashavail  &  $unitsavail )");
		/*-------------------------------------------------------------------------*/
		if ($cashavail >= $cashcost && $unitsavail >= $unitcost && !$cashcost <= 0 && 
			$trn->attackCount >= $uberAtk &&
			$trn->defenseCount >= $uberDef && 
			$trn->covertCount >= $uberCov && 
			$trn->anticovertCount >= $uberAnti)
		{
		Debug::printMsg(__CLASS__, __FUNCTION__, "units and Everythign Okay So far");
			/*Code for Training*/
			$lifers = (.1 * $miners);
			$miner = ($miners -$lifers);
			
			$query = "UPDATE `units` SET ";
			if ($atk != 0){ 
				$query .= "`attack`=(`attack`+$atk), ";
			}
			if ($uberAtk != 0){
				$query .= "`superAttack`=(`superAttack`+$uberAtk), ";
			}
			if ($def != 0){
				$query .= "`defense`=(`defense`+$def), ";
			}
			if ($uberDef != 0){
				$query .= "`superDefense`=(`superDefense`+$uberDef), ";
			}
			if ($miners != 0){
				if(($lifers+$miner) == $miners)
				{
					$query .= "`miners`=(`miners` + $miner), `lifers`=(`lifers`+$lifers), ";
				}elseif(($lifers+$miner) < $miners){
					while(($lifers+$miner) != $miners)
					{
						$lifers++;
					}
					$query .= "`miners`=(`miners` + $miner), `lifers`=(`lifers`+$lifers), ";
				}elseif(($lifers+$miner) > $miners){
					while(($lifers+$miner) != $miners)
					{
						$lifers--;
					}
					$query .= "`miners`=(`miners` + $miners), `lifers`=(`lifers`+$lifers), ";
				}							
			}
			if($cov != 0){
				$query .= "`covert`=(`covert`+ $cov), ";
			}
			if ($uberCov != 0){
				$query .= "`superCovert`=(`superCovert`+$uberCov), ";
			}
			if ($anti != 0){
				$query .= "`anticovert`=(`anticovert`+$anti), ";
			}
			if($uberAnti != 0){
				$query .= "`superAnticovert`=(`superAnticovert`+$uberAnti), ";
			}
			$query = substr($query,0,-2)." WHERE `uid`=".$_SESSION['userid']." LIMIT 1";
			$this->query($query);

			$endcash = (float) number_format($cashavail,'0','','')-number_format($cashcost,'0','','');
			$query = "UPDATE `bank` SET `onHand`='".$endcash."' WHERE `uid`=".$_SESSION['userid']." LIMIT 1";
			$this->query($query);

			$query = "UPDATE `units` SET ";
			if ($unitcost != 0){
				$query .= "`untrained`=(`untrained`-$unitcost), ";
			}
			if($uberAtk != 0){
				$query .= "`attack`=(`attack`-$uberAtk), ";
			}
			if($uberDef != 0){
				$query .= "`defense`=(`defense`-$uberDef), ";
			}
			if($uberCov != 0){
				$query .= "`covert`=(`covert`-$uberCov), ";
			}
			if($uberAnti != 0){
				$query .= "`anticovert`=(`anticovert`-$uberAnti), ";
			}
			$query = substr($query,0,-2)." WHERE `uid`=".$_SESSION['userid']." LIMIT 1";
			$this->query($query);
			echo "Training Successful";
		}elseif($unitsavail < $unitcost && $cashavail < $cashcost && 
		        $uberavail["atk"] < $ubercost["atk"] &&
				$uberavail["def"] < $ubercost["def"] && 
				$uberavail["cov"] < $ubercost["cov"] && 
				$uberavail["anti"] < $ubercost["anti"]){
			echo "Not Enough Naq or Units";
		}elseif($cashavail < $cashcost){
			echo "Not Enough Cash";
		}elseif($unitsavail < $unitcost){
			echo "Not Enough Units";
		}elseif($uberavail["atk"] < $ubercost["atk"] ||
				$uberavail["def"] < $ubercost["def"] || 
				$uberavail["cov"] < $ubercost["cov"] || 
				$uberavail["anti"] < $ubercost["anti"]){
			echo "Not Enough Trained Units";
			
		}
		else
		{
		Debug::printMsg(__CLASS__, __FUNCTION__, "Problem at End of Training");
		}
	}
	
	public function untrainUnits(int $atk, int $def, int $cov, int $anti, int $min): void
	{
		Debug::printMsg(__CLASS__, __FUNCTION__, "Resigning Units");
		$trn = $this->getPersonnel($_SESSION['userid']);
		$atkavail = $trn->attackCount;
		$defavail = $trn->defenseCount;
		$covavail = $trn->covertCount;
		$antiavail = $trn->anticovertCount;
		$minavail = $trn->minerCount;
		$uu = $trn->uuCount;
if (($atk < 0) OR ($def < 0) OR ($min < 0) OR ($cov < 0) OR ($anti < 0)) {
$atk = 0;$def=0;$min=0;$cov=0;$anti=0;
}
		if ($atk <= $atkavail && $def <= $defavail && $cov <= $covavail && $anti <= $antiavail && $min <= $minavail)
		{
			$endatk = $atkavail - $atk;
			$enddef = $defavail - $def;
			$endcov = $covavail - $cov;
			$endanti = $antiavail - $anti;
			$endmin = $minavail - $min;
			
			$uu += $atk + $def + $cov + $anti + $min;
			$query = "UPDATE `units` SET ";
			if ($atk !=0){ $query .= "`attack`=".$endatk.", "; }
			if ($def !=0){ $query .= "`defense`=".$enddef.", "; }
			if ($cov !=0){ $query .= "`covert`=".$endcov.", "; }
			if ($anti !=0){ $query .= "`anticovert`=".$endanti.", "; }
			if ($min !=0){ $query .= "`miners`=".$endmin.", "; }
			
			$query .= "`untrained`=".$uu." WHERE `uid`=".$_SESSION['userid']." LIMIT 1";
			$this->query($query);
			
			echo "Resignation of Units Successful";
		}else{
			echo "You Dont have that many trained Units";
		}
	}
	
	public function attack_raid(string $type, int $uid, int $turns=0): ?int
	{
		if ($turns == 0||!$uid > 0) { exit; }
		if($uid == $_SESSION['userid']) { echo "Can't Attack Ones Self"; exit; }
		Debug::printMsg(__CLASS__, __FUNCTION__, "Attacking $uid.");
		$time = date("H:i:s");
		$str = "";
		$query = "SELECT
					users.`uname`AS atkrName,
					users.uid AS atkrID,
					userdata.rid AS atkrRace,
					power.mil_atk AS atkrPower,
					units.attack AS attack,
					units.superAttack AS superAttack,
					units.attackMercs AS attackMerc,
					units.anticovert AS anticovert,
					units.superAnticovert AS superAnticovert,
					unitnames.attack AS attackName,
					unitnames.superAttack AS superAttackName,
					unitnames.attackMercs AS attackMercName,
					unitnames.anticovert AS anticovertName,
					unitnames.superAnticovert AS superAnticovertName,
					bank.onHand AS money,
					technology.auEffect AS attackKill,
					technology.auRes AS attackDie,
					technology.auSteal AS take,
					technology.acuEffect AS acKill,
					technology.acuRes AS acDie,
					(SELECT `uname` FROM users WHERE uid=?) AS atkdName,
					(SELECT `uid` FROM users WHERE uid=?) AS atkdID,
					(SELECT `rid` FROM userdata WHERE uid=?) AS atkdRace,
					(SELECT power.mil_def FROM power WHERE uid=?) AS atkdPower,
					(SELECT defense FROM units WHERE uid=?) AS defense,
					(SELECT superDefense FROM units WHERE uid=?) AS superDefense,
					(SELECT defenseMercs FROM units WHERE uid=?) AS defenseMerc,
					(SELECT covert FROM `units` WHERE uid=?) AS covert,
					(SELECT superCovert FROM `units` WHERE uid=?) AS superCovert,
					(SELECT `defense` FROM `unitnames` WHERE `rid`=atkdRace) AS defenseName,
					(SELECT superDefense FROM `unitnames` WHERE `rid`=atkdRace) AS superDefenseName,
					(SELECT defenseMercs FROM `unitnames` WHERE `rid`=atkdRace) AS defenseMercName,
					(SELECT covert FROM `unitnames` WHERE `rid`=atkdRace) AS covertName,
					(SELECT superCovert FROM `unitnames` WHERE `rid`=atkdRace) AS superCovertName,
					(SELECT `duSteal` FROM technology WHERE `uid`=?) AS protect,
					(SELECT `cuEffect` FROM technology WHERE `uid`=?) AS cKill,
					(SELECT `cuRes` FROM technology WHERE `uid`=?) AS cDie,
					(SELECT `duEffect` FROM technology WHERE `uid`=?) AS defenseKill,
					(SELECT `duRes` FROM technology WHERE `uid`=?) AS defenseDie,
					(SELECT untrained FROM `units` WHERE `uid`=?) AS uu
					FROM `units`, `userdata`, `users`, `unitnames`,`power`,`bank`,`technology`
					WHERE userdata.uid = ?
					AND power.uid = userdata.uid
					AND units.uid = userdata.uid
					AND bank.uid = ?
					AND unitnames.rid = userdata.rid
					AND users.uid = userdata.uid";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("iiiiiiiiiiiiiiiiiiiiii", $uid, $uid, $uid, $uid, $uid, $uid, $uid, $uid, $uid, $uid, $uid, $uid, $uid, $uid, $uid, $_SESSION['userid'], $uid);
		$stmt->execute();
		$q = $stmt->get_result();
		$data = $q->fetch_object();			
		$atkWpowerQuery = "SELECT weapons.wid,armory.weaponPower,armory.weaponName,weapons.strength,weapons.quanity,armory.requireTrained FROM `weapons`,`armory` WHERE 
							armory.wid = weapons.wid AND weapons.uid = ? AND 
							armory.rid = ? AND armory.isDefense = 0 ORDER BY armory.weaponPower DESC LIMIT 10000";
		$defWpowerQuery = "SELECT weapons.wid,armory.`weaponPower`,armory.`weaponName`,weapons.strength,weapons.quanity,armory.requireTrained FROM `weapons`,`armory` WHERE 
							armory.wid = weapons.wid AND weapons.uid = ? AND 
							armory.rid = ? AND armory.isDefense = 1 ORDER BY armory.weaponPower DESC LIMIT 10000";
		$stmt = $this->db_link->prepare($atkWpowerQuery);
		$stmt->bind_param("ii", $data->atkrID, $data->atkrRace);
		$stmt->execute();
		$atkWpowerQ = $stmt->get_result();
		$stmt = $this->db_link->prepare($defWpowerQuery);
		$stmt->bind_param("ii", $data->atkdID, $data->atkdRace);
		$stmt->execute();
		$defWpowerQ = $stmt->get_result();
		$atk = round(abs( ( mt_rand(75,100) / 100 ) * $data->atkrPower));
		$def = round(abs( ( mt_rand(75,100) / 100 ) * $data->atkdPower));
		
		/*Attack Declaraions*/
		$superAttack 	= $data->superAttack;
		$attack			= $data->attack;
		$aMercs			= $data->attackMerc;
		$sAused			= 0;
		$aused			= 0;
		$aMused			= 0;
		
		/*Defense Declarations*/
		$superDefense 	= $data->superDefense;
		$defense		= $data->defense;
		$dMercs			= $data->defenseMerc;
		$sDused			= 0;
		$dused			= 0;
		$dMused			= 0;
		
		$aw_power = $this->percs($def,$atk);
		$df_power = $this->percs($atk,$def);
		
		$counter = 0;
		$a_equip = "It was Observed and Recorded that ".$data->atkrName."'s forces were equiped as follows:<br><table border='0'>";
		$atkW = "<table border='0'>";
		while($awp = $atkWpowerQ->fetch_object())
		{
			if($awp->requireTrained==0)
			{
				$a_equip .= "<tr><td>".number_format($awp->quanity)." $awp->weaponName reign down from above!</td></tr> ";	
			}
			if($awp->requireTrained==1)
			{
				$quanity 	= $awp->quanity;
				$q_used  	= 0;
				$sA 		= $superAttack - $sAused;
				$a 		= $attack - $aused;
				$aM		= $aMercs - $aMused;	
					
				/*Calculates Super Attack Unit Power*/			
				if($sA > 0)
				{
					if ($quanity > $sA)
					{
						$q_used = $sA;
						$quanity -= $q_used;
						$sAused += $q_used;
						$a_equip .= "<tr><td>".number_format($q_used)." $data->superAttackName armed with $awp->weaponName</td></tr> ";
					}
					else
					{
						$q_used = $quanity;
						$quanity -= $q_used;
						$sAused += $q_used;
						$a_equip .= "<tr><td>".number_format($q_used)." $data->superAttackName armed with $awp->weaponName</td></tr> ";
					}
				}
				/*Calculates regualer Attacker*/			
				if($a > 0 )
				{
					if ($quanity > $a)
					{
						$q_used = $a;
						$quanity -= $q_used;
						$aused += $q_used;
						$a_equip .= "<tr><td>".number_format($q_used)." $data->attackName armed with $awp->weaponName</td></tr> ";
					}
					else
					{
						$q_used = $quanity;
						$quanity -= $q_used;
						$aused += $q_used;
						$a_equip .= "<tr><td>".number_format($q_used)." $data->attackName armed with $awp->weaponName</td></tr> ";
					}
				}
				/*Calculated Mercs Attack*/
				if($aM > 0)
				{
					if ($quanity > $aM)
					{
						$q_used = $aM;
						$quanity -= $q_used;
						$aMused += $q_used;
						$a_equip .= "<tr><td>".number_format($q_used)." $data->attackMercName armed with $awp->weaponName</td></tr> ";
					}else{
						$q_used = $quanity;
						$quanity -= $q_used;
						$aMused += $q_used;
						$a_equip .= "<tr><td>".number_format($q_used)." $data->attackMercName armed with $awp->weaponName</td></tr> ";
					}
				}
			}
			$atkwps[$counter] = $awp->wid;
			$atkNew[$counter] = (int)($awp->strength-($awp->strength*$aw_power));
			$atkW .= "<tr><td>".$awp->weaponName."</td><td>".$atkNew[$counter]."</td><td>/<td><td>".$awp->weaponPower."</td></tr>"; 
			$counter++;
		}
		$atkW .= "</table>";
		if($sA > 0){
			$a_equip .= "<tr><td>".number_format($sA-$sAused)." $data->superAttackName came unarmed</td></tr> ";
		}
		if($a > 0){
			$a_equip .= "<tr><td>".number_format($a-$aused)." $data->attackName came unarmed</td></tr> ";
		}
		if($aM > 0){
			$a_equip .= "<tr><td>".number_format($aM-$aMused)." $data->attackMercName came unarmed</td></tr> ";
		}
		$a_equip .= "</table><br>";
		$counter = 0;
		$d_equip = "It was Observed and Recorded that ".$data->atkdName."'s forces were equiped as follows:<br> <table border='0'>  ";
		$defW = "<table border='0'>";
		while($dwp = $defWpowerQ->fetch_object())
		{
			if($dwp->requireTrained==0)
			{
					 $d_equip .= "<tr><td>".number_format($dwp->quanity)." $dwp->weaponName reign down from above!</td></tr> ";
			}else{
				$quanity 	= $dwp->quanity;
				$q_used  	= 0;
				$sD 		= $superDefense - $sDused;
				$d 		= $defense - $dused;
				$dM		= $dMercs - $dMused;	
						
				if($sD > 0)
				{
					if ($quanity > $sD)
					{
						$q_used = $sD;
						$quanity -= $q_used;
						$sDused += $q_used;
						$d_equip .= "<tr><td>".number_format($q_used)." $data->superDefenseName armed with $dwp->weaponName</td></tr> "; 
					}
					else
					{
						$q_used = $quanity;
						$quanity -= $q_used;
						$sDused += $q_used;
						$d_equip .= "<tr><td>".number_format($q_used)." $data->superDefenseName armed with $dwp->weaponName</td></tr> "; 
					}
				}
				if($quanity  > 0 && $d > 0 )
				{
					if ($quanity > $d)
					{
						$q_used = $d;
						$quanity -= $q_used;
						$dused += $q_used;
						$d_equip .= "<tr><td>".number_format($q_used)." $data->defenseName armed with $dwp->weaponName</td></tr> "; 
					}
					else
					{
						$q_used = $quanity;
						$quanity -= $q_used;
						$dused += $q_used;
						$d_equip .= "<tr><td>".number_format($q_used)." $data->defenseName armed with $dwp->weaponName</td></tr> "; 
					}
				}
				if($quanity > 0 && $dM > 0)
				{
					if ($quanity > $dM)
					{
						$q_used = $dM;
						$quanity -= $q_used;
						$dMused += $q_used;
						$d_equip .= "<tr><td>$q_used $data->defenseMerc with $dwp->weaponName</td></tr> "; 
					}else{
						$q_used = $quanity;
						$quanity -= $q_used;
						$dMused += $q_used;
						$d_equip .= "<tr><td>$q_used $data->defenseMerc with $dwp->weaponName</td></tr> "; 
					}
				}
			}
			$defwps[$counter] = $dwp->wid;
			$defNew[$counter] = (int)($dwp->strength-($dwp->strength*$df_power));
			$defW .= "<tr><td>".$dwp->weaponName."</td><td>".$defNew[$counter]."</td><td>/<td><td>".$dwp->weaponPower."</td></tr>  "; 
			$counter++;
		}
		$defW .= "</table>";
		if($sD > 0){
			$d_equip .= "<tr><td>".number_format($sD-$sDused)." $data->superDefenseName came unarmed</td></tr> ";
		}
		if($d > 0){
			$d_equip .= "<tr><td>".number_format($d-$dused)." $data->defenseName came unarmed</td></tr> ";
		}
		if($dM > 0){
			$d_equip .= "<tr><td>".number_format($dM-$dMused)." $data->defenseMercName came unarmed</td></tr> ";
		}
		$d_equip .= "</table><br>";
		
		$resStolen = .75 + (($data->take/100)-($data->protect/100));
		if($atk > $def)
		{
			if($type == "raid")
			{
				$succes = 1;
				$str .= "Raid Successful<br>";
				$resources = (abs(((int)round( ($data->uu * ( ( ( mt_rand(15,25) / 100 ) + ( mt_rand(15,25) / 100 ) ) /2) ) ) ) * $resStolen));
				$query = "UPDATE `units` SET `untrained`=(`untrained`+?) WHERE `uid`=? LIMIT 1";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("ii", $resources, $_SESSION['userid']);
				if (!$stmt->execute()) { echo "ERROR with Adding Resources"; exit;}
				$query = "UPDATE `units` SET `untrained`=(`untrained`-?) WHERE `uid`=? LIMIT 1";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("ii", $resources, $uid);
				if (!$stmt->execute()) { echo "ERROR with Taking Resources"; exit;}
				$str .= "Resources Stolen: ".$resources." in Untrained Units<br>";
			}elseif($type == "attack"){
				$str .= "Attack Successful<br>";
				$resources = abs(round($data->money * $resStolen * ( mt_rand(50,60) / 100 )));
				$str .= "Resources Stolen: ".$resources." in Naquadah<br>";
				$query = "UPDATE `bank` SET `onHand`=(`onHand`+?) WHERE `uid`=? LIMIT 1";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("ii", $resources, $_SESSION['userid']);
				if (!$stmt->execute()) { echo "ERROR with Adding Resources"; exit;}
				$query = "UPDATE `bank` SET `onHand`=(`onHand`-?) WHERE `uid`=? LIMIT 1";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("ii", $resources, $uid);
				if (!$stmt->execute()) { echo "ERROR with Taking Resources"; exit;}
				$succes = 1;
			}
		}else{
				$str .= "Attack Unsuccessful";
				$str .= "$data->atkr was unable to take any resources";
				$succes =0;

		}
$atkd = "$data->atkdName Sent:<br>  $data->superCovert $data->superCovertName and $data->covert $data->covertName and $data->defenseMerc $data->defenseMercName and $data->superDefense $data->superDefenseName and $data->defense $data->defenseName<br><br>";
$atkr = "$data->atkrName Sent:<br>  $data->superAnticovert $data->superAnticovertName and $data->anticovert $data->anticovertName and $data->attackMerc $data->attackMercName and $data->superAttack $data->superAttackName and $data->attack $data->attackName<br><br>";
		$atkrDmg	= 1 + (($data->defenseKill/50) - ($data->attackDie/50) );
		$defrDmg	= 1 + (($data->attackKill/50) - ($data->defenseDie/50) );
		$covDmg 	= 1 + (($data->acKill/50) - ($data->cDie/50));
		$antiDmg 	= 1 + (($data->cKill/50) - ($data->acDie/50));
		
		$uberAtkDead = round(($data->superAttack*$this->percs($def,$atk))*$defrDmg);
		$atkDead = round(($data->attack*$this->percs($def,$atk))*$defrDmg); 
		$defDead = round(($data->defense*$this->percs($atk,$def))*$atkrDmg);
		$atkMercDead = round(($this->percs($def,$atk)*$data->attackMerc)*$defrDmg); 
		$defMercDead = round(($this->percs($atk,$def)*$data->defenseMerc)*$atkrDmg);
		$uberDefDead = round(($this->percs($atk,$def)*$data->superDefense)*$atkrDmg);
		$covDead = round(($this->percs($atk,$def)*$data->covert)*$antiDmg);
		$uberCovDead = round(($this->percs($atk,$def)*$data->superCovert)*$antiDmg);
		$antiDead = round(($this->percs($atk,$def)*$data->anticovert)*$covDmg );
		$uberAntiDead = round(($this->percs($atk,$def)*$data->superAnticovert)*$covDmg );
		
		/*ATTaCK WEAPONS FOR LOOP and AtkDead*/
		for($x=0; $x <count($atkwps)&&$x<count($atkNew); $x++){
		$query = "UPDATE `weapons` SET `strength`=? WHERE `uid`=? AND `wid`=? LIMIT 1";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("iii", $atkNew[$x], $_SESSION['userid'], $atkwps[$x]);
			if (!$stmt->execute()) { echo "ERROR with Attack Strength Update"; exit;}
		}
		$query = "UPDATE `units` SET `superAttack`=(`superAttack`-?), `attack`=(`attack`-?), `attackMercs`=(`attackMercs`-?) WHERE `uid`=? LIMIT 1";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("iiii", $uberAtkDead, $atkDead, $atkMercDead, $_SESSION['userid']);
		if (!$stmt->execute()) { echo "ERROR with Attack Unit Update"; exit;}
		
		/*This Ends Attack and Start Defense and Def Dead*/
		for($x=0; $x <count($defwps)&&$x<count($defNew); $x++){
			$query = "UPDATE `weapons` SET `strength`=? WHERE `uid`=? AND `wid`=? LIMIT 1";
			$stmt = $this->db_link->prepare($query);
			$stmt->bind_param("iii", $defNew[$x], $uid, $defwps[$x]);
			if (!$stmt->execute()) { echo "ERROR with Defense Strength Update"; exit;}
		}
		$query = "UPDATE `units` SET `superDefense`=(`superDefense`-?), `defense`=(`defense`-?), `defenseMercs`=(`defenseMercs`-?) WHERE `uid`=? LIMIT 1";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("iiii", $uberDefDead, $defDead, $defMercDead, $uid);
		if (!$stmt->execute()) { echo "ERROR with Defense UNits Update"; exit;}
		
		/*ENDS the LOOPS*/
		$query = "INSERT INTO `actionlog` ( `actID`,`uid` , `to_uid` , `time` , `type` , `turnsUsed` , `atkSent` , `atkEquip` , `defSent` , `defEquip` , `attackPower` , `defensePower` , `atkDead` , `superAtkDead` , `atkMercsDead` , `antiDead` , `superAntiDead` , `defDead` , `superDefDead` , `defMercsDead` , `covDead` , `superCovDead` , `atkWeaponStatus` , `defWeaponStatus` , `success` , `phrase` , `stolen` ) VALUES ( NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("iiissssssssssssssssssssssss", $_SESSION['userid'], $uid, $time, $type, $turns, $atkr, $a_equip, $atkd, $d_equip, $atk, $def, $atkDead, $uberAtkDead, $atkMercDead, $antiDead, $uberAntiDead, $defDead, $uberDefDead, $defMercDead, $covDead, $uberCovDead, $atkW, $defW, $succes, $str, $resources);
		if (!$stmt->execute()) { echo "ERROR with ActionLog Insert"; exit;}
		$query = "UPDATE `userdata` SET `actionTurns`=(`actionTurns`-?) WHERE `uid`=? LIMIT 1";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("ii", $turns, $_SESSION['userid']);
		$q = $stmt->execute();
		if(!$q){ echo "ERROR with Removing Turns Used"; exit;}else{ $mysql = $q; }
		$query = "SELECT actID FROM actionlog WHERE type=? AND uid =? AND to_UID= ? AND time=? LIMIT 1";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("siii", $type, $_SESSION['userid'], $uid, $time);
		$stmt->execute();
		$q = $stmt->get_result();
		if(!$q) { echo "ERROR with actID Select"; exit;} else{ $mysql = $q; }
		$obj = $q->fetch_object();
		$this->updatePower($_SESSION['userid']);
		$this->updatePower($uid);
		return $obj->actID;
	}
	
	public function percs(float $val1, float $val2): float
	{
		Debug::printMsg(__CLASS__, __FUNCTION__, "Getting Weapon Damage Percentages.");
		if($val2 == 0 || $val1 == 0){
			$power = 0;
		}elseif($val1<=.01*$val2){
			$power = .0001;
		}elseif($val1<=.1*$val2){
			$power = .01;
		}elseif($val1<=.25*$val2){
			$power = (mt_rand(2,4))/100;
		}elseif($val1<=.50*$val2){
			$power = (mt_rand(5,7))/100;
		}elseif($val1<=.75*$val2){
			$power = (mt_rand(8,10))/100;
		}elseif($val1<=1.5*$val2){
			$power = (mt_rand(11,13))/100;
		}elseif($val1<=2*$val2){
			$power = (mt_rand(14,16))/100;
		}elseif($val1<=3*$val2){
			$power = (mt_rand(17,19))/100;
		}elseif($val1<=4*$val2){
			$power = (mt_rand(20,22))/100;
		}elseif($val1>4*$val2){
			$power = (mt_rand(23,25))/100;
		}
		return $power;
	}
	
	public function getActID(int $actID): bool
	{
		$query = "SELECT * FROM actionlog WHERE actID=? LIMIT 1";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("i", $actID);
		$stmt->execute();
		$q = $stmt->get_result();
		if(!$q)
		{ return false; }else{
			$act = $q->fetch_object();
			if ($act->actID > 0)
			{
				if ($act->to_uid != $_SESSION['userid'] && $act->uid != $_SESSION['userid'])
				{ 
					echo "Not Authorized to View this actionReport<br>";
					return true;
				}elseif($act->to_uid == $_SESSION['userid'] || $act->uid == $_SESSION['userid']) {
					
					switch($act->type){
						case "attack":
						case "raid":
							echo "<br><br>".$act->atkSent
								.$act->atkEquip
								.$act->defSent
								.$act->defEquip
								."Attack Was: ".number_format($act->attackPower)."<br>"
								."Numbers of Attackers that died: Super: "
								.number_format($act->superAtkDead)
								." Trained:"
								.number_format($act->atkDead)
								." Mercenaries:"
								.number_format($act->atkMercsDead)
								."<br>"
								."Defense Was: "
								.number_format($act->defensePower)
								."<br>"
								."Numbers of Defenders that died: Super:"
								.number_format($act->superDefDead)
								." Trained:"
								.number_format($act->defDead)
								." Mercenaries:"
								.number_format($act->defMercsDead)
								."<br>";
							if ($act->to_uid == $_SESSION['userid']){
								echo "Defense Weapons went from/to: ".$act->defWeaponStatus."<br>";
							}elseif($act->uid == $_SESSION['userid']){
								echo "Attack Weapons went from/to: ".$act->atkWeaponStatus."<br>";
							}
							echo $act->phrase;
							break;
						case "spy":
							$array = explode(',',$act->atkWeaponStatus);
							for($x=0; $x<count($array);$x++){
								if(is_numeric($array[$x])){
									$array[$x] = number_format($array[$x]);
								}
							}
							$x=0;
							echo "<table width='100%' border='0'>
							  <tr>
								<td><table width='100%' border='0'>
									<tr>
									  <td colspan='2' align='center'>Personnel Data</td>
									</tr>
									<tr>
									  <td width='37%' align='left'>".$array[$x++]."</td>
									  <td width='63%' align='right' valign='middle'>".$array[$x++]."</td>
									</tr>
									<tr>
									  <td align='left'>".$array[$x++]."</td>
									  <td align='right' valign='middle'>".$array[$x++]."</td>
									</tr>
									<tr>
									  <td align='left'>".$array[$x++]."</td>
									  <td align='right' valign='middle'>".$array[$x++]."</td>
									</tr>
									<tr>
									  <td align='left'>".$array[$x++]." </td>
									  <td align='right' valign='middle'>".$array[$x++]."</td>
									</tr>
									<tr>
									  <td align='left'>".$array[$x++]." </td>
									  <td align='right' valign='middle'>".$array[$x++]."</td>
									</tr>
									<tr>
									  <td align='left'>".$array[$x++]."</td>
									  <td align='right' valign='middle'>".$array[$x++]."</td>
									</tr>
									<tr>
									  <td align='left'>Untrained</td>
									  <td align='right' valign='middle'>".$array[$x++]."</td>
									</tr>
									<tr>
									  <td align='left'>Miners (Lifers) </td>
									  <td align='right' valign='middle'>".$array[$x++]." ( ".$array[$x++]." )</td>
									</tr>
									<tr>
									  <td align='left'>".$array[$x++]."</td>
									  <td align='right' valign='middle'>".$array[$x++]."</td>
									</tr>
									<tr>
									  <td align='left'>".$array[$x++]."</td>
									  <td align='right' valign='middle'>".$array[$x++]."</td>
									</tr>
									<tr>
									  <td align='left'>".$array[$x++]."</td>
									  <td align='right' valign='middle'>".$array[$x++]."</td>
									</tr>
									<tr>
									  <td align='left'>".$array[$x++]."</td>
									  <td align='right' valign='middle'>".$array[$x++]."</td>
									</tr>
									<tr>
									  <td>Total</td>
									  <td align='right' valign='middle'>".$array[$x++]."</td>
									</tr>
								  </table>      </td>
								<td><table width='100%' border='0'>
								  <tr>
									<td colspan='2'><center>
									  Information
									</center></td>
									</tr>
								  <tr>
									<td>Attack Power</td>
									<td><span class='style1'>".$array[$x++]."</span></td>
								  </tr>
								  <tr>
									<td>Defense Power </td>
									<td>".$array[$x++]."</td>
								  </tr>
								  <tr>
									<td>Covert Power </td>
									<td>".$array[$x++]."</td>
								  </tr>
								  <tr>
									<td>AntiCovert Power </td>
									<td>".$array[$x++]."</td>
								  </tr>
								  <tr>
									<td>Covert Level </td>
									<td>".$array[$x++]."</td>
								  </tr>
								  <tr>
									<td>AntiCovert Level </td>
									<td>".$array[$x++]."</td>
								  </tr>
								  <tr>
									<td>Action Turns </td>
									<td>".$array[$x++]."</td>
								  </tr>
								  <tr>
									<td>Unit Production </td>
									<td>".$array[$x++]."</td>
								  </tr>
								  <tr>
									<td>Income</td>
									<td>".$array[$x]."</td>
								  </tr>
								</table></td>
							  </tr>
							  <tr>
								<td colspan='2'>&nbsp;</td>
							  </tr>
							  <tr>
								<td colspan='2'><table width='100%' border='0'>
								  <tr>
									<td colspan='4' align='center'>Weapons</td>
									</tr>
								  <tr>
									<td align='left'>Name</td>
									<td align='left'>Type</td>
									<td align='center'>Quanity </td>
									<td align='left'>Power</td>
								  </tr>";
								 
								 echo    "</table></td>
							  </tr>
							</table><br>";
							break;
						}
						return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
	}
	
	public function actionLog(string $type ="attack"): void
	{
		$byMeQuery = "SELECT `actID`,`to_uid` AS uid, users.uname as user, `time`, `success`,`stolen`,actionlog.`thereDead`,actionlog.`myDead`, `turnsUsed`, `attackPower`, `defensePower`  FROM actionlog,users WHERE actionlog.`uid`=? AND users.uid =actionlog.to_uid AND `type`=? ORDER BY actID DESC";
		$toMeQuery = "SELECT `actID`,actionlog.`uid` AS uid, users.uname as user, `time`, `stolen`,actionlog.`thereDead`,actionlog.`myDead`, `turnsUsed`, `attackPower`, `defensePower`  FROM actionlog,users WHERE actionlog.to_uid=? AND users.uid =actionlog.uid AND `type`=? ORDER BY actID DESC";
		$stmt = $this->db_link->prepare($byMeQuery);
		$stmt->bind_param("is", $_SESSION['userid'], $type);
		$stmt->execute();
		$byMeQ = $stmt->get_result();
		$stmt = $this->db_link->prepare($toMeQuery);
		$stmt->bind_param("is", $_SESSION['userid'], $type);
		$stmt->execute();
		$toMeQ = $stmt->get_result();
		switch($type)
		{
			case "attack":
				print "<center><table border=0><tr><td colspan='9' align='center'>Attacks By You</td></tr><tr><td>Time</td><td>Enemy</td><td>Result</td>
					<td>Turns</td><td>Enemy Losses</td><td>Your Losses</td>
					<td> Damage By You</td><td>Damage To You</td></tr>";
				while ($byMe = $byMeQ->fetch_object())
				{
					?>
					
					<tr><td><?= $byMe->time;?> </td><td align="center">
					<a href="javascript:void(0)" onclick="sendData('user','get','<?= $byMe->uid; ?>'); return false">
					<?= $byMe->user; ?></td>
					<td align="center"><? if($byMe->success == 0 ) { echo "Attack Defended"; } else { echo number_format($byMe->stolen)." Cash Stolen"; } ?></td>
					<td align="center"><?= $byMe->turnsUsed; ?></td>
					<td align="center"><?= $byMe->thereDead; ?></td>
					<td align="center"><?= $byMe->myDead; ?></td>
					<td align="center"><?= number_format($byMe->attackPower); ?></td>
					<td align="center"><?= number_format($byMe->defensePower); ?></td>
					<td align="center">
					<a href="javascript:void(0)" onclick="sendData('actionLogs' , 'get' , '<?= $byMe->actID; ?>','atk'  )">Details
					</a></td>
					</tr>
			<?php
				}
				echo "<tr><td colspan='9' align='center'>Attacks On You</td></tr><tr><td>Time</td><td>Enemy</td><td>Result</td>
					<td>Turns</td><td>Enemy Losses</td><td>Your Losses</td>
					<td> Damage By You</td><td>Damage To You</td></tr>";
				while ($toMe = $toMeQ->fetch_object())
				{
					?>
					
					<tr><td><?= $toMe->time;?> </td><td align="center">
					<a href="javascript:void(0)" onclick="sendData('user','get','<?= $toMe->uid; ?>'); return false">
					<?= $toMe->user; ?></td>
					<td align="center"><? if($toMe->success == 0 ) { echo "Attack Defended"; } else { echo number_format($toMe->stolen)." Cash Stolen"; } ?> </td>
					<td align="center"><?= $toMe->turnsUsed; ?></td>
					<td align="center"><?= $toMe->thereDead; ?></td>
					<td align="center"><?= $toMe->myDead; ?></td>
					<td align="center"><?= number_format($toMe->defensePower); ?></td>
					<td align="center"><?= number_format($toMe->attackPower); ?></td>
					<td align="center">
					<a href="javascript:void(0)" onclick="sendData('actionLogs' , 'get' , '<?= $toMe->actID; ?>', 'atk' )">Details
					</a></td>
					</tr>
			<?php
				}
				echo "</table></center>";						
				break;
			case "raid":
				print "<center><table border=0><tr><td colspan='9' align='center'>Raids By You</td></tr><tr><td>Time</td><td>Enemy</td><td>Result</td>
					<td>Turns</td><td>Enemy Losses</td><td>Your Losses</td>
					<td> Damage By You</td><td>Damage To You</td></tr>";
				while ($byMe = $byMeQ->fetch_object())
				{
					?>
					
					<tr><td><?= $byMe->time;?> </td><td align="center">
					<a href="javascript:void(0)" onclick="sendData('user','get','<?= $byMe->uid; ?>'); return false">
					<?= $byMe->user; ?></td>
					<td align="center"><?= number_format($byMe->stolen); ?> Untrained Units Stolen</td>
					<td align="center"><?= $byMe->turnsUsed; ?></td>
					<td align="center"><?= $byMe->thereDead; ?></td>
					<td align="center"><?= $byMe->myDead; ?></td>
					<td align="center"><?= number_format($byMe->attackPower); ?></td>
					<td align="center"><?= number_format($byMe->defensePower); ?></td>
					<td align="center">
					<a href="javascript:void(0)" onclick="sendData('actionLogs' , 'get' , '<?= $byMe->actID; ?>', 'atk' )">Details
					</a></td>
					</tr>
			<?php
				}
				print "<tr><td colspan='9' align='center'>Raids On You</td></tr><tr><td>Time</td><td>Enemy</td><td>Result</td>
					<td>Turns</td><td>Enemy Losses</td><td>Your Losses</td>
					<td> Damage By You</td><td>Damage To You</td></tr>";
				while ($toMe = $toMeQ->fetch_object())
				{
					?>
					
					<tr><td><?= $toMe->time;?> </td><td align="center">
					<a href="javascript:void(0)" onclick="sendData('user','get','<?= $toMe->uid; ?>'); return false">
					<?= $byMe->user; ?></td>
					<td align="center"><?= number_format($toMe->stolen); ?> Untrained Units Stolen</td>
					<td align="center"><?= $toMe->turnsUsed; ?></td>
					<td align="center"><?= $toMe->thereDead; ?></td>
					<td align="center"><?= $toMe->myDead; ?></td>
					<td align="center"><?= number_format($toMe->attackPower); ?></td>
					<td align="center"><?= number_format($toMe->defensePower); ?></td>
					<td align="center">
					<a href="javascript:void(0)" onclick="sendData('actionLogs' , 'get' , '<?= $toMe->actID; ?>', 'atk' )">Details
					</a></td>
					</tr>
                    <?php
				}
				echo "</table></center>";						
				break;
			case "spy":
				print "<center><table border=0><tr><td colspan='9' align='center'>Spys By You</td></tr><tr><td>Time</td><td>Enemy</td><td>Result</td></tr>";
				while ($byMe = $byMeQ->fetch_object())
				{
					?>
					
					<tr><td><?= $byMe->time;?> </td><td align="center">
					<a href="javascript:void(0)" onclick="sendData('user','get','<?= $byMe->uid; ?>'); return false">
					<?= $byMe->user; ?></td>
					<td align="center">Covert Operation</td>
					<td align="center">
					<a href="javascript:void(0)" onclick="sendData('actionLogs' , 'get' , '<?= $byMe->actID; ?>', 'atk' )">Details
					</a></td>
					</tr>
			<?php
				}
				print "<tr><td colspan='9' align='center'>Spys On You</td></tr><tr><td>Time</td><td>Enemy</td><td>Result</td></tr>";
				while ($toMe = $toMeQ->fetch_object())
				{
					?>
					
					<tr><td><?= $toMe->time;?> </td><td align="center">
					<a href="javascript:void(0)" onclick="sendData('user','get','<?= $toMe->uid; ?>'); return false">
					<?= $byMe->user; ?></td>
					<td align="center">Covert Operation</td>
					<td align="center">
					<a href="javascript:void(0)" onclick="sendData('actionLogs' , 'get' , '<?= $toMe->actID; ?>', 'atk' )">Details
					</a></td>
					</tr>
                    <?php
				}
				echo "</table></center>";
				break;						
		}
	}
	
	public function turnUpdate(): bool
	{
		/*Queries Users From Database and Last Time THey Logged in*/
		$query  = "SELECT users.uid AS user, 
					((units.miners *(80+technology.income)) + ( units.lifers *(80+technology.income) ) + ( SUM( planets.income_bonus ) ) + (race.income_bonus*((units.miners *(80+technology.income))) + ( units.lifers *(80+technology.income)))) AS Income,
					((technology.unitProd*(3+technology.uppl))+SUM( planets.up_bonus )+(race.up_bonus*(technology.unitProd*(3+technology.uppl)))) AS up, bank.onHand
					FROM users
					INNER JOIN units ON users.uid = units.uid
					INNER JOIN userdata ON users.uid = userdata.uid
					INNER JOIN race ON userdata.rid = race.rid
					INNER JOIN planets ON users.uid = planets.uid
					INNER JOIN bank ON users.uid = bank.uid
					INNER JOIN technology ON users.uid = technology.uid
					GROUP BY users.uid";
		$atkq = "SELECT `uid` FROM power ORDER BY `mil_atk` DESC";
		$defq = "SELECT `uid` FROM power ORDER BY `mil_def` DESC";
		$covq = "SELECT `uid` FROM power ORDER BY `mil_cov` DESC";
		$antq = "SELECT `uid` FROM power ORDER BY `mil_anti` DESC";
		$uidq = "SELECT users.`uname` , power . uid , SUM(power.mil_cov +power.mil_def +power.mil_atk +power.mil_anti )as averaged FROM power , users WHERE users . uid =power . uid GROUP BY uid  ORDER BY `averaged`  DESC";
		$upQ  = "SELECT technology.`uid`, ((technology.unitProd*(3+technology.uppl))+SUM( planets.up_bonus )+(race.up_bonus*(technology.unitProd*(3+technology.uppl)))) AS up FROM technology
INNER JOIN userdata ON technology.uid = userdata.uid
				INNER JOIN race ON userdata.rid = race.rid	
				INNER JOIN planets ON userdata.uid = planets.uid 
				GROUP BY technology.uid ORDER BY up DESC";
		$rankQ = "SELECT users.`uname`,rank.uid, avg(rank.mil_cov+ rank.mil_def+rank.mil_atk+ rank.mil_anti+ rank.up+ rank.income) as avdzero FROM rank,users WHERE users.uid = rank.uid GROUP BY uid ORDER BY avdzero, uname DESC";
		$incQ  = "SELECT units.`uid`, ((units.miners *(80+technology.income)) + ( units.lifers *(80+technology.income) ) + ( SUM( planets.income_bonus ) ) + (race.income_bonus*((units.miners *(80+technology.income))) + ( units.lifers *(80+technology.income)))) AS Income
				FROM userdata
				INNER JOIN units ON userdata.uid = units.uid
				INNER JOIN race ON userdata.rid = race.rid	
				INNER JOIN planets ON userdata.uid = planets.uid
				INNER JOIN technology ON userdata.uid = technology.uid 
				GROUP BY userdata.uid ORDER BY Income DESC";
				
		$q = $this->query($query);
		$atk = $this->query($atkq);
		$def = $this->query($defq);
		$cov = $this->query($covq);
		$anti = $this->query($antq);
		$upS = $this->query($upQ);
		$incS = $this->query($incQ);
		$uids = $this->query($uidq);
		$rankings = $this->query($rankQ);
		$users = [];
		while($data = $q->fetch_object())
		{
			/*Gives Naq*/
			$query = "UPDATE `bank` SET `onHand` =(`onHand`+?) WHERE `uid` =? LIMIT 1 ";
			$stmt = $this->db_link->prepare($query);
			$stmt->bind_param("ii", $data->Income, $data->user);
			$stmt->execute();
			/*Gives Turns*/
			$query = "UPDATE `userdata` SET `actionTurns` = (`actionTurns` + 3) WHERE `uid` =? LIMIT 1";
			$stmt = $this->db_link->prepare($query);
			$stmt->bind_param("i", $data->user);
			$stmt->execute();
			/*Gives UU*/
			$query = "UPDATE `units` SET `untrained` = (`untrained` + ?) WHERE `uid` =? LIMIT 1";
			$stmt = $this->db_link->prepare($query);
			$stmt->bind_param("ii", $data->up, $data->user);
			$stmt->execute();
		}
		$counter = 1;
		while ($data = $atk->fetch_object())
		{
			$users[$data->uid]["atk"] = $counter;
			echo "$data->uid Atk Rank is $counter <br>";
			$counter++;
			
		}
		$counter = 1;
		while ($data = $def->fetch_object())
		{
			$users[$data->uid]["def"] = $counter;
			echo "$data->uid Def Rank is $counter <br>";
			$counter++;
		}
		$counter = 1;
		while ($data = $cov->fetch_object())
		{
			$users[$data->uid]["cov"] = $counter;
			echo "$data->uid Cov Rank is $counter <br>";
			$counter++;
		}
		$counter = 1;
		while ($data = $anti->fetch_object())
		{
			$users[$data->uid]["anti"] = $counter;
			echo "$data->uid Anti Rank is $counter <br>";
			$counter++;
		}
		$counter = 1;
		while ($data = $upS->fetch_object())
		{
			$users[$data->uid]["up"] = $counter;
			echo "$data->uid Unit Production Rank is $counter <br>";
			$counter++;
		}
		$counter = 1;
		while ($data = $incS->fetch_object())
		{
			$users[$data->uid]["inc"] = $counter;
			echo "$data->uid Inc Rank is $counter <br>";
			$counter++;
		}
		$counter = 1;
		while ($data = $rankings->fetch_object())
		{
			$users[$data->uid]["overall"] = $counter;
			echo "$data->uname overall Rank is $counter <br>";
			$counter++; 
		}
		$counter = 1;
		while ($data = $uids->fetch_object())
		{
			$query = "UPDATE rank SET `mil_atk`=?, `mil_def`=?, `mil_cov`=?, `mil_anti`=?, `up`= ?, `income`=?, `mil_total`=?, `overall`=? WHERE uid=? LIMIT 1";
			$stmt = $this->db_link->prepare($query);
			$stmt->bind_param("iiiiiiiii", $users[$data->uid]["atk"], $users[$data->uid]["def"], $users[$data->uid]["cov"], $users[$data->uid]["anti"], $users[$data->uid]["up"], $users[$data->uid]["inc"], $counter, $users[$data->uid]["overall"], $data->uid);
			echo $query."<br>";
			$stmt->execute();
			$counter++;
		}
		
		return true;
	}
	
	public function delOld(): void
	{
		/*Deletes Old Users*/
		$thrdays = time() - (30 * 24 * 60 * 60);
		$old = date('F jS', $thrdays);
		
		$query  = "SELECT users.lastLogin,users.uid FROM users";
		$q = $this->query($query);
		while($data = $q->fetch_object())
		{
		if ($data->lastLogin == $old)
			{
				$query = "DELETE FROM users WHERE uid=?";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("i", $data->uid);
				$stmt->execute();
				$query = "DELETE FROM bank WHERE uid=?";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("i", $data->uid);
				$stmt->execute();
				$query = "DELETE FROM planets WHERE uid=?";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("i", $data->uid);
				$stmt->execute();
				$query = "DELETE FROM power WHERE uid=?";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("i", $data->uid);
				$stmt->execute();
				$query = "DELETE FROM rank WHERE uid=?";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("i", $data->uid);
				$stmt->execute();
				$query = "DELETE FROM technology WHERE uid=?";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("i", $data->uid);
				$stmt->execute();
				$query = "DELETE FROM units WHERE uid=?";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("i", $data->uid);
				$stmt->execute();
				$query = "DELETE FROM userdata WHERE uid=?";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("i", $data->uid);
				$stmt->execute();
				$query = "DELETE FROM weapons WHERE uid=?";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("i", $data->uid);
				$stmt->execute();
				
			}
		}
	}
	
	public function viewTech(): object
	{
		$query="SELECT unitProd,uppl,income,attack,auSteal,auEffect,auRes,defense,duSteal,duEffect,duRes,covert,cuEffect,cuRes,anticovert,acuEffect,acuRes,galaxy,pDef,puCap,pmCap, ascend ,  cov_lvl, anti_lvl, SUM(attack+duSteal+auEffect+auRes+defense+duSteal+duEffect+duRes+covert+cuEffect+cuRes+anticovert+acuEffect+acuRes+pDef+1) AS ttl FROM `technology` WHERE `uid`=? GROUP BY uid";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("i", $_SESSION['userid']);
		$stmt->execute();
		$q = $stmt->get_result();
		$data = $q->fetch_object();
		return $data;
	}
	
	/*I have the next to functions to stop people from tampering with the form data.*/
	/*Its Salts the field then md5 encrypts it like tha passwords.*/
	public function fieldtocrypt(): array
	{
		$data = $this->fields;
		$counter=0;
		foreach($data as $x)
		{
			$data[$counter] = $this->salt($x);
			$counter++;
		}
		return $data;
	}
	
	public function crypttofield(string $crypt): ?string
	{
		$data = $this->fields;
		$counter = 0;
		$data2 = $this->fieldtocrypt();
		foreach($data2 as $dat)	
		{
			if($dat == $crypt)
			{
				return $data[$counter];
			}else{
				$counter++;
			}

		}
		return null;
	}
	/*End of Field Crypt Functions*/
	
	public function buytech(string $crypt, int $quanity=1): void
	{
		if(!$type = $this->crypttofield($crypt))
		{
			echo "Error With Crypt $crypt <br>";
			exit;
		}
		$query = "SELECT bank.onHand,bank.inBank,puCap,pmCap,unitProd,uppl,income,galaxy,anti_lvl,cov_lvl,ascend , SUM(attack+duSteal+auEffect+auRes+defense+duSteal+duEffect+duRes+covert+cuEffect+cuRes+anticovert+acuEffect+acuRes+pDef+1) AS ttl FROM `technology`,`bank` WHERE bank.`uid`=? AND technology.uid=bank.uid GROUP BY bank.uid LIMIT 1";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("i", $_SESSION['userid']);
		$stmt->execute();
		$techQ = $stmt->get_result();
		$tech = $techQ->fetch_object();
		$data = $this->level($tech->ascend);
		$data["z"] = $data["y"]*$tech->ttl;
		$money = (number_format($tech->onHand,0,'','') + number_format($tech->inBank,0,'',''));
		$cost = 0;
		$max = 0;
		
		switch($type)
		{
			case "unitProd":
				for($x=0;$x<$quanity;$x++)
				{
					$cost += ((($tech->ascend+1)*5000000)*($tech->unitProd+$x));
				}
				$max = (($tech->ascend+1)*500);
				break;
			case "uppl":
				for($x=0;$x<$quanity;$x++)
				{
					$cost += ((($tech->ascend+1)*50000000)*($tech->uppl+1+$x));
				}
				$max = ($tech->ascend+1)*10;
				break;
			case "income":
				for($x=0;$x<$quanity;$x++)
				{
					$cost += ((($tech->ascend+1)*10000000)*($tech->income+1+$x));
				}
				$max = ($tech->ascend+1)*10;
				break;	
			case "ascend":
				echo "Ascension is not Ready Yet";		
				break;
			case "cov_lvl":
				$cost = 15000;
				for ($x=0; $x<$tech->cov_lvl; $x++) { $cost *=2; }
				$max = 100000;
				break;
			case "anti_lvl":
				$cost = 15000;
				for ($x=0; $x<$tech->anti_lvl; $x++) { $cost *=2; }
				$max = 100000;
				break;
			default:
				for($x=0;$x<$quanity;$x++)
				{
					$cost += ($data["y"] * ($tech->ttl+$x));
				}
				$max = $data["x"];
				break;
		}
		$selectQ = "SELECT `$type` FROM `technology` WHERE `uid`=? LIMIT 1";
		$stmt = $this->db_link->prepare($selectQ);
		$stmt->bind_param("i", $_SESSION['userid']);
		$stmt->execute();
		$select = $stmt->get_result();
		$sel = $select->fetch_row();
		if ($quanity <= 0) { exit; }
		if ($quanity+$sel[0] <= $max)
		{
			if (number_format($cost,0,'','') <= number_format($money,0,'',''))
			{
				$query = "UPDATE `technology` SET `$type`=`$type`+$quanity WHERE `uid`=? LIMIT 1";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("i", $_SESSION['userid']);
				if($stmt->execute())
				{
					if($tech->onHand>=$cost)
					{
						$query = "UPDATE `bank` SET `onHand`=`onHand`-? WHERE `uid`=? LIMIT 1";
						$stmt = $this->db_link->prepare($query);
						$stmt->bind_param("ii", $cost, $_SESSION['userid']);
						if(!$stmt->execute()){ echo "SQL Error Check Debug <font> $query</font>"; exit; }
					}else{
						$left = $cost - $tech->onHand;
						$query = "UPDATE `bank` SET `onHand`=0 AND `inBank`=`inBank`-? WHERE `uid`=? LIMIT 1";
						$stmt = $this->db_link->prepare($query);
						$stmt->bind_param("ii", $left, $_SESSION['userid']);
						if(!$stmt->execute()){ echo "SQL Error Check Debug<br><font> $query</font>"; exit; }
					}
					echo "You Spent (".number_format($cost).") on $type Technology<br>";
				}else{ echo "SQL Error Check Debug<font> $query</font>"; exit; }
			}else{
				echo "Not Enough Cash (".number_format($cost).")<br>";
			}
		}else{
			echo "$type would be Above Max";
		}
	} 
	
	public function level(int $type): array
	{
		switch ($type){
			case '0':
				$data["str"] = "Non Ascended";
				$data["y"] = 500000;
				$data["x"] = 15;
				$data["ascend"] = 0;
				break;
			case '1':
				$data["str"] = "Prior";
				$data["y"] = 1000000;
				$data["x"] = 20;
				$data["ascend"] = 0;
				break;
			case '2':
				$data["str"] = "Prophet";
				$data["y"] = 5000000;
				$data["x"] = 25;
				$data["ascend"] = 0;
				break;
			case '3':
				$data["str"] = "Messiah";
				$data["y"] = 10000000;
				$data["x"] = 30;
				$data["ascend"] = 0;
				break;
			case '4':
				$data["str"] = "Incarnate";
				$data["y"] = 50000000;
				$data["x"] = 35;
				$data["ascend"] = 0;
				break;
			case '5':
				$data["str"] = "Living God";
				$data["y"] = 100000000;
				$data["x"] = 40;
				$data["ascend"] = 0;
				break;
			case '6':
				$data["str"] = "Fully Ascended";
				$data["y"] = 500000000;
				$data["x"] = 45;
				$data["ascend"] = 0;
				break;
		}
		return $data;
	}
	
	public function sendMessage(int $toUID, string $subject="None", string $message): bool
	{
		$gameTime 	= date("F jS H:i:s");
		$query = "INSERT INTO `messages` ( `fromUID` , `toUID` , `isDeleted` , `timeSent` ,`subject`, `message` )
					VALUES ( ?, ?, '0', ?, ?, ?)";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("iisss", $_SESSION['userid'], $toUID, $gameTime, $subject, $message);
		if($stmt->execute()) { return true; } else { return false; }
	}
	
	public function create_allliance(int $UID, string $name, string $desc, string $forumadd, int $isclosed): bool
	{
		if($name==''){
	echo "Invalid Alliance Name";
	return false;
	}else{
	$q="SELECT * FROM `alliances` WHERE `allyname` = ?";
	$stmt = $this->db_link->prepare($q);
	$stmt->bind_param("s", $name);
	$stmt->execute();
	$v = $stmt->get_result();
	$rcount = $v->num_rows;
	if($rcount==0){
	$q="INSERT INTO `alliances` (`allyid`,`allyname`,`desc`,`forumadd`,`isclosed`,`allybank`,`founder`) VALUES ('', ?, ?, ?, ?, '0', ?)";
	$stmt = $this->db_link->prepare($q);
	$stmt->bind_param("sssis", $name, $desc, $forumadd, $isclosed, $UID);
	$stmt->execute();
	//now to bring it around to the user
	$q="SELECT * FROM `alliances` WHERE `allyname` = ?";
	$stmt = $this->db_link->prepare($q);
	$stmt->bind_param("s", $name);
	$stmt->execute();
	$v = $stmt->get_result();
	$c = $v->fetch_array();
	$q="UPDATE `users` SET `allyid` = ?, `arank` = '2' WHERE `uid` = ?";
	$stmt = $this->db_link->prepare($q);
	$stmt->bind_param("ii", $c['allyid'], $UID);
	$stmt->execute();
	echo "alliance Created";
	return true;
	}else{
	echo "alliance already exists";
	return false;
	}
		
	}
	}
	public function viewMessages(): mysqli_result
	{
		$query = "SELECT messages.`mid`, messages.fromUID, users.uname as user, messages.subject, messages.message, messages.timeSent FROM messages,users WHERE messages.toUID =? AND users.uid = messages.fromUID";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("i", $_SESSION['userid']);
		$stmt->execute();
		$q = $stmt->get_result();
		return $q;
	}
	
	public function deleteMessage($mid): bool
	{
		if ($mid == "all") { 
			$query = "DELETE FROM messages WHERE toUID=?";
			$stmt = $this->db_link->prepare($query);
			$stmt->bind_param("i", $_SESSION['userid']);
		}elseif(is_numeric($mid)){
			$query = "DELETE FROM messages WHERE `mid`=? AND toUID=?";
			$stmt = $this->db_link->prepare($query);
			$stmt->bind_param("ii", $mid, $_SESSION['userid']);
		}
		if($stmt->execute())
		{
			return true;
		}
		return false;
	}
	
	public function bank(string $type="view", float $ammount=0): ?object
	{
		switch($type)
		{
			case "view":
				$query = "SELECT bank.onHand,`bank`.`inBank` , ((
							sum( planets.uid ) * ( 72 * ( units.miners * ( 80 + technology.income ) ) + ( units.lifers * ( 80 + technology.income ) ) 
							+ ( SUM( planets.income_bonus ) ) + ( race.income_bonus * ( (
							units.miners * ( 80 + technology.income ) ) ) + ( units.lifers * ( 80 + technology.income ) ) )
							))*(technology.ascend+1)) AS cap FROM `bank`
							INNER JOIN `userdata` ON bank.uid = userdata.uid
							INNER JOIN `race` ON userdata.rid = race.rid
							LEFT OUTER JOIN `units` ON userdata.uid = units.uid
							LEFT OUTER JOIN `planets` ON userdata.uid = planets.uid
							LEFT OUTER JOIN `users` users_1 ON userdata.cid = users_1.uid
							LEFT OUTER JOIN `planetsize` ON planets.plnt_size = planetsize.size
							LEFT OUTER JOIN `technology` ON userdata.uid = technology.uid
							WHERE bank.uid =?
							GROUP BY bank.uid";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("i", $_SESSION['userid']);
				$stmt->execute();
				$q = $stmt->get_result();
				$data = $q->fetch_object();
				$data->left = abs($data->cap - $data->inBank);
				return $data;
				break;
			case "deposit":
				if (number_format($ammount,0,'','') > 0)
				{
					$data = $this->bank();
					if(number_format($data->left,0,'','') < number_format($ammount,0,'',''))
					{
						$ammount = abs(number_format($data->left,0,'',''));
					}					
						$query = "UPDATE `bank` SET `inbank`=(`inbank`+(?*.95)) , `onHand`=(`onHand`-?) WHERE `uid`=? LIMIT 1";
					$stmt = $this->db_link->prepare($query);
					$stmt->bind_param("dii", $ammount, $ammount, $_SESSION['userid']);
					if(!$stmt->execute())
					{ echo $query; } else { echo "Deposited: ".number_format($ammount); }
				}
				break;
			case "withdrawl":
				if (number_format($ammount,0,'','') > 0)
				{
					$data = $this->bank();
					if(number_format($data->inBank,0,'','') < number_format($ammount,0,'',''))
					{
						$ammount = number_format($data->inBank,0,'','');
					}					
					$query = "UPDATE `bank` SET `inbank`=(`inbank`-(?)) , `onHand`=(`onHand`+?) WHERE `uid`=? LIMIT 1";
					$stmt = $this->db_link->prepare($query);
					$stmt->bind_param("dii", $ammount, $ammount, $_SESSION['userid']);
					if(!$stmt->execute())
					{ echo $query; } else { echo "Withdrew: ".number_format($ammount); }
					return null;
				}
		}
	}
	
	public function spy(int $uid, int $turns=0): ?int
	{
		if($turns==0) { echo "No Turns Used. Contact Admin"; exit; }
		$time 	= date("F j H:i:s");
		$this->updatePower($_SESSION['userid']);
		$this->updatePower($uid);
		$query = "SELECT (units.superCovert + units.covert) as units, (mil_cov + mil_anti) AS fromCovert, (SELECT (mil_cov + mil_anti) FROM power WHERE uid =? ) AS toCovert FROM power,units WHERE units.uid =? AND power.uid =?";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("iii", $uid, $_SESSION['userid'], $_SESSION['userid']);
		$stmt->execute();
		$q = $stmt->get_result();
		$pwr = $q->fetch_object();
		$query = "SELECT 
					userdata.actionTurns,
					technology.cov_lvl AS covertLVL,
					technology.anti_lvl AS antiLVL,
					power.mil_atk AS milStrike,
					power.mil_def AS milDefense,
					power.mil_cov AS milCovert,
					power.mil_anti AS milAnti,
					units.attack 		AS attackCount, 
					units.superAttack 	AS superAttackCount, 
					units.attackMercs 	AS attackMercCount,
					units.defense		AS defenseCount,
					units.superDefense	AS superDefenseCount,
					units.defenseMercs	AS defenseMercCount,
					units.untrained		AS uuCount,
					units.miners 		AS minerCount,
					units.lifers		AS liferCount,
					units.covert		AS covertCount,
					units.superCovert	AS superCovertCount,
					units.anticovert	AS anticovertCount,
					units.superAnticovert	AS superAnticovertCount,
					unitnames.attack 	AS attackName, 
					unitnames.superAttack 	AS superAttackName, 
					unitnames.attackMercs 	AS attackMercName,
					unitnames.defense	AS defenseName,
					unitnames.superDefense	AS superDefenseName,
					unitnames.defenseMercs	AS defenseMercName,
					unitnames.covert	AS covertName,
					unitnames.superCovert	AS superCovertName,
					unitnames.anticovert	AS anticovertName,
					unitnames.superAnticovert AS superAnticovertName,
					SUM( units.attack+ units.superAttack+ units.attackMercs+ units.defense+ units.superDefense+ units.defenseMercs+ units.untrained+ units.miners+ units.lifers+ units.covert+ units.superCovert+ units.anticovert+ units.superAnticovert ) AS ttlarmysize,
					((units.miners *(80+technology.income)) + ( units.lifers *(80+technology.income) ) + ( SUM( planets.income_bonus ) ) + (race.income_bonus*((units.miners *(80+technology.income))) + ( units.lifers *(80+technology.income)))) AS income,
					((technology.unitProd*(3+technology.uppl))+SUM( planets.up_bonus )+(race.up_bonus*(technology.unitProd*(3+technology.uppl)))) AS up
					FROM `userdata` INNER JOIN `units` ON userdata.uid = units.uid INNER JOIN `unitnames` ON userdata.rid = unitnames.rid INNER JOIN `power` ON userdata.uid = power.uid INNER JOIN `technology` ON userdata.uid = technology.uid INNER JOIN `planets` ON userdata.uid = planets.uid INNER JOIN `race` ON userdata.rid = race.rid WHERE userdata.uid = ? GROUP BY userdata.uid LIMIT 1";
					$stmt = $this->db_link->prepare($query);
					$stmt->bind_param("i", $uid);
					if($stmt->execute())
					{
						$q = $stmt->get_result();
						$data = $q->fetch_object();
						$ttl = $data->minerCount+$data->liferCount; 
						switch($pwr->fromCovert){
							case ($pwr->fromCovert >= 5*$pwr->toCovert):
								$perc = 1;
								$suc = 1;
								break;
							case ($pwr->fromCovert > 4*$pwr->toCovert):
								$perc = .8;
								$suc = 1;
								break;
							case ($pwr->fromCovert > 3*$pwr->toCovert):
								$perc = .6;
								$suc = 1;
								break;
							case ($pwr->fromCovert > 2*$pwr->toCovert):
								$perc = .4;
								$suc = 1;
								break;
							case ($pwr->fromCovert > $pwr->toCovert):
								$perc = .2;
								$suc = 1;
								break;
							case ($pwr->fromCovert > .25*$pwr->toCovert):
								$perc = 0.1;
								$suc = 1;
								break;
							case ($pwr->fromCovert <= .25*$pwr->toCovert):
								$suc = 0;
								$perc = 0;
								break;
						}
						$array = [$data->attackName,$data->attackCount,$data->superAttackName,$data->superAttackCount,$data->attackMercName,$data->attackMercCount,$data->defenseName,$data->defenseCount,$data->superDefenseName,$data->superDefenseCount,$data->defenseMercName,$data->defenseMercCount,$data->uuCount,$ttl,$data->liferCount,$data->covertName,$data->covertCount,$data->superCovertName,$data->superCovertCount,$data->anticovertName,$data->anticovertCount,$data->superAnticovertName,$data->superAnticovertCount,$data->ttlarmysize,$data->milStrike,$data->milDefense,$data->milCovert,$data->milAnti,$data->covertLVL,$data->antiLVL,$data->actionTurns,$data->up,$data->income];
												
						$xyz = 20 * (1-$perc);
						for($x = 0; $x < count($array); $x++)
						{
							$arrayQ[$x] = "i";
						}
						for ($z=0; $z <$xyz; $z++)
						{
							$arrayb[$z] = mt_rand(0,32);
						}
						
						for($x=0; $x < count($arrayb); $x++)
						{
								for($y=0; $y < count($arrayb); $y++)
								{
									if($y != $x)
									{
										while ($arrayb[$x] == 0 || $arrayb[$x] == 2 || $arrayb[$x] == 4 || $arrayb[$x] == 6 || 
												$arrayb[$x] == 8 || $arrayb[$x] == 10 || $arrayb[$x] == 15 || $arrayb[$x] == 17 || 
												$arrayb[$x] == 19 || $arrayb[$x] == 21 || $arrayb[$x] == $arrayb[$y])
										{
											$arrayb[$x] = mt_rand(0,32);
										}
									}
								}
							$arrayQ[$arrayb[$x]] = "z";
						}
						for($xyz = 0; $xyz < count($array); $xyz++)
						{
							if($arrayQ[$xyz] == "z")
							{
								$arrayFinal[$xyz] = "??????";
								
							}elseif($arrayQ[$xyz] == "i")
							{
								if(is_numeric($array[$xyz]))
								{
									$arrayFinal[$xyz] = $array[$xyz];
								}else{
									$arrayFinal[$xyz] = $array[$xyz];
								}
							}
							
						}
						if ($suc == 0) { 
							$query = "INSERT INTO `actionlog` (`uid`,`to_uid`,`time`,`type`,`atkSent`,`success`,`phrase`) VALUES (?, ?, ?, 'spy', ?, ?, 'Covert Operation')";
							$stmt = $this->db_link->prepare($query);
							$stmt->bind_param("iisis", $_SESSION['userid'], $uid, $time, $pwr->units, $suc);
							$stmt->execute();
							$query = "UPDATE `units` SET `covert` = (`covert`) , `superCovert`=(`superCovert`) WHERE uid = ?";
							$stmt = $this->db_link->prepare($query);
							$stmt->bind_param("i", $_SESSION['userid']);
							$stmt->execute();
							$query = "SELECT `actID` FROM `actionlog` WHERE uid=? AND to_uid=? AND `time`=?";
							$stmt = $this->db_link->prepare($query);
							$stmt->bind_param("iis", $_SESSION['userid'], $uid, $time);
							$stmt->execute();
							$q = $stmt->get_result();
							$qa = $q->fetch_object();
							return $qa->actID;
						} 
						
						$query = "INSERT INTO `actionlog` (`uid`,`to_uid`,`time`,`type`,`atkSent`,`atkWeaponStatus`,`success`,`phrase`) VALUES 
									(?, ?, ?, 'spy', ?, ?, ?, 'Covert Operation')";
						$stmt = $this->db_link->prepare($query);
						$stmt->bind_param("iissss", $_SESSION['userid'], $uid, $time, $pwr->units, implode(',',$arrayFinal), $suc);
						$stmt->execute();
						$query = "SELECT `actID` FROM `actionlog` WHERE uid=? AND to_uid=? AND `time`=?";
						$stmt = $this->db_link->prepare($query);
						$stmt->bind_param("iis", $_SESSION['userid'], $uid, $time);
						$stmt->execute();
						$q = $stmt->get_result();
						$qa = $q->fetch_object();
						return $qa->actID;
					}
					echo "Broken Contact Admin";
					return null;
	}
	
	/*function spyWeapons($uid)
	{
		$this->updatePower($_SESSION['userid']);
		$this->updatePower($uid);
		$query = "SELECT (mil_cov + mil_anti) AS fromCovert, (SELECT (mil_cov + mil_anti) FROM power WHERE uid =".$uid." ) AS toCovert FROM power WHERE uid =".$_SESSION['userid'];
		
		$q = $this->query($query);
		$pwr = mysql_fetch_object($q);
		$query = "SELECT armory.WeaponName as name, armory.WeaponPower as base, armory.isDefense as def, weapons.quanity as quan, weapons.strength as power FROM weapons, userdata, armory WHERE weapons.uid = $uid AND userdata.uid = weapons.uid AND armory.rid = userdata.rid AND weapons.wid = armory.wid ORDER by armory.isDefense ASC";
		if($q = $this->query($query))
		{
			$data = mysql_fetch_object($q);
			$ttl = $data->minerCount+$data->liferCount; 
			switch($pwr->fromCovert){
				case ($pwr->fromCovert >= 5*$pwr->toCovert):
					$perc = 1;
					break;
				case ($pwr->fromCovert > 4*$pwr->toCovert):
					$perc = .8;
					break;
				case ($pwr->fromCovert > 3*$pwr->toCovert):
					$perc = .6;
					break;
				case ($pwr->fromCovert > 2*$pwr->toCovert):
					$perc = .4;
					break;
				case ($pwr->fromCovert > $pwr->toCovert):
					$perc = .2;
					break;
				case ($pwr->fromCovert > .25*$pwr->toCovert):
					$perc = 0.1;
					break;
			}

		return $q;
	}*/
	
	public function sabotage(int $uid, int $turns = 0): void 
	{
		if($turns == 0){ echo "no Turns Used<br>"; exit; }
		$query = "SELECT (Select `mil_cov` FROM `power` WHERE `uid`=? LIMIT 1) AS toCov, `mil_cov` AS fromCov, `actionTurns` FROM userdata,power WHERE userdata.`uid` =? AND power.uid = userdata.uid ";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("ii", $uid, $_SESSION['userid']);
		$stmt->execute();
		$q = $stmt->get_result();
		$data = $q->fetch_object();
		$fromCov = $data->fromCov;
		$toCov = $data->toCov;
		if ($turns > $data->actionTurns) { echo "You do not have that many turns<br>"; }
		if ($fromCov > $toCov) { 
			echo "Your Men Destroyed weapons and live to sabotage another day<br>"; 
			$query = "SELECT `covert`,`superCovert`,(SELECT `covert` FROM units WHERE `uid`=?) as enemy_cov,(SELECT `superCovert` as enemy_superCovert FROM units WHERE `uid`=?) as enemy_superCovert FROM units WHERE uid=?";
			$stmt = $this->db_link->prepare($query);
			$stmt->bind_param("iii", $uid, $uid, $_SESSION['userid']);
			$stmt->execute();
			$q = $stmt->get_result();
			$data2 = $q->fetch_object();
			$data3 = $this->getWeaponInventory($_SESSION['userid']);
			print_r($data3);
		} else { 
			echo "Your Men are Dead<br>"; 
		}	
	}
}
?>