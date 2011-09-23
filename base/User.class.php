<?php
// Base::User.class.php

class User extends Chive
{
	var $userName	= null;
	var $password	= null;
	var $access		= 0;
	var $loggedIn	= false;
    var $userid     = null;
	
	/**
	 * Contructor for Chive
	 * @param String $name Name of user
	 * @param String $password Password of user
	 *
	 */
	function User($userName = "", $password = "DoodleCakes and Rofl Sundae4278vsid")
	{
		parent::Chive();
		if(isset($userName) && !empty($userName) || isset($_SESSION['username']))
		{
			if(isset($_SESSION['username']) && isset($_SESSION['password']))
			{
				$this->userName	= $_SESSION['username'];
				$this->password	= $_SESSION['password'];
				$this->access	= $_SESSION['access'];
				$this->userid	= $_SESSION['userid'];
				$this->raceID	= $_SESSION['raceID'];
				
			} else {
				$this->userName = $this->clean_sql($userName, 0);
				$this->password = $this->clean_sql($this->salt($password), 0);
			}
						
			if($this->isRealUser())
			{
				$this->loggedIn = true;
				$_SESSION['username']	= $this->userName;
				$_SESSION['password']	= $this->password;
				$_SESSION['access']		= $this->access;
				$_SESSION['userid']		= $this->userid;
				$_SESSION['raceID']		= $this->raceID;
				$_SESSION['progress']   = $this->progress;
				$time = date("F jS");
				$query = "UPDATE users SET lastLogin=".$this->clean_sql($time)." WHERE uid='".$this->userid."' LIMIT 1";
				if ($this->query($query)){
					Debug::printMsg(__CLASS__, __FUNCTION__, "UserID is:".$this->userid." lastLogin Updated");
				}else{
					Debug::printMsg(__CLASS__, __FUNCTION__, "UserID is:".$this->userid." lastLogin Not Updated");
				}
				
				Debug::printMsg(__CLASS__, __FUNCTION__, "UserID is:".$this->userid);
				Debug::printMsg(__CLASS__, __FUNCTION__, "Logged In");
			} else {
				$this->loggedIn = false;
				$this->access = 0;
			}
		} else {
			$this->loggedIn = false;
			$this->access = 0;
		}
		Debug::printMsg(__CLASS__, __FUNCTION__, "Class created with <b>\$userName</b> ".$this->userName);
	}
	
	/**
	 * Checks if the user is authentic
	 *
	 * @return bool
	 */
	function isRealUser()
	{
		$query = "
			SELECT alevel
			FROM ".$this->db_prefix."users
			WHERE email='".$this->userName."' 
			AND password='".$this->password."' 
			LIMIT 1
			";
		$q = $this->query($query);
		if(mysql_num_rows($q))
		{
			$row = mysql_fetch_row($q);
			$this->access = $row[0];
			Debug::printMsg(__CLASS__, __FUNCTION__, "Validated '$this->userName'");
			$query = "SELECT users.uid,userdata.rid,userdata.progress as prog FROM ".$this->db_prefix."users , userdata	
				   		  WHERE users.email='".$this->userName."' AND 
						  users.password='".$this->password."'  LIMIT 1"; //SETS USER ID
			$ided = $this->query($query); //IDK
			$row = mysql_fetch_object($ided); //IDK
			$this->userid = $row->uid; /// SETS USER ID
			$this->raceID = $row->rid;
			$this->progress = $row->prog;
			return true;
		}
		Debug::printMsg(__CLASS__, __FUNCTION__, "Could not validate user '$this->userName'");
		return false;
	}
	
	function isAllowed($reqAcc)
	{
		if((int)$reqAcc & $this->access)
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Logs out user
	 *
	 */
	function logOut()
	{
		$_SESSION['username'] = null;
		$_SESSION['password'] = null;
		$_SESSION['access'] = null;
		$_SESSION['userid'] = null;
		session_unset();
		session_destroy();
	
	}
	
	/**
	 * Puts a salt on the encryption method
	 *
	 * @param String $value
	 * @return String
	 */
	function salt($value)
	{
		return md5(crypt($value, '.u55ybcbC,ufzQu2'));
	}
	
	/**
	 * Adds user to the database
	 *
	 * @param String $userName
	 * @param String $password
	 * @param int $access
	 * @return bool
	 */
	function addUser($userName, $password, $access = 1,$email,$rid,$hpname,$ip)
	{
		
		$userName	= $this->clean_sql($userName);
		$password	= $this->clean_sql($this->salt($password));
		$hpname 	= $this->clean_sql($hpname);
		$email		= $this->clean_sql($email);
		$rid 		= $this->clean_sql($rid);
		$ip			= $this->clean_sql($ip);
		
		$query = "SELECT `uname` FROM `users` WHERE `ip`=".$ip." LIMIT 1";
		$q = $this->query($query);
		$chk = mysql_fetch_row($q);
		if(!$chk[0]) {
		if(is_numeric($access))
		{
			$query = "
				INSERT INTO ".$this->db_prefix."users
				(uname, password, alevel, email, ip)
				VALUES ($userName, $password, $access, $email, $ip)
				";
				$this->query($query);
			$query = "SELECT `uid` FROM `users` WHERE `uname`=".$userName." LIMIT 1";
			$q = $this->query($query);
			$x = mysql_fetch_object($q);
			$query = "INSERT INTO ".$this->db_prefix."bank (uid,onHand) VALUES (".$x->uid.",250000)";
			$this->query($query);
			$query = "INSERT INTO ".$this->db_prefix."units (uid,untrained) VALUES (".$x->uid.", 250)";
			$this->query($query);
			$query = "INSERT INTO ".$this->db_prefix."technology (uid,unitProd) VALUES (".$x->uid.",1)";
			$this->query($query);
			$query = "INSERT INTO ".$this->db_prefix."power (uid) VALUES (".$x->uid.")";
			$this->query($query);
			$query = "INSERT INTO ".$this->db_prefix."rank (uid) VALUES (".$x->uid.")";
			$this->query($query);
			$query = "INSERT INTO ".$this->db_prefix."planets (uid,plnt_name,isHome) VALUES (".$x->uid.",$hpname, 1)";
			$this->query($query);
			$xxx = (string) $this->genUniqueLink();
			$query = "INSERT INTO ".$this->db_prefix."userdata (uid,rid,actionTurns,link) VALUES (".$x->uid.",".$rid.",250,".$xxx.")";
			$this->query($query);
			echo "Registration Complete";
		}
		}else{ echo "Your IP is used by another account only 1 account per IP"; }
	}
	
	function genUniqueLink()
	{
		$time=time();
		$uniqID="";
		for ($i=0; $i<strlen($time)/2;$i++){
			$uniqID.=chr( rand(ord('a'),ord('z')) );
		}
		$uniqID .= $time;
		return $this->clean_sql($uniqID);	
	}
}
?>