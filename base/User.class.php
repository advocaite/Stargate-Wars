<?php
// Base::User.class.php

class User extends Chive
{
	public ?string $userName = null;
	public ?string $password = null;
	public int $access = 0;
	public bool $loggedIn = false;
	public ?int $userid = null;
	public ?int $raceID = null;
	public ?int $progress = null;

	/**
	 * Constructor for User
	 * @param string $userName Name of user
	 * @param string $password Password of user
	 *
	 */
	public function __construct(string $userName = "", string $password = "DoodleCakes and Rofl Sundae4278vsid")
	{
		parent::__construct();
		if(isset($userName) && !empty($userName) || isset($_SESSION['username']))
		{
			if(isset($_SESSION['username']) && isset($_SESSION['password']))
			{
				$this->userName	= $_SESSION['username'];
				$this->password	= $_SESSION['password'];
				$this->access	= $_SESSION['access'];
				$this->userid	= $_SESSION['userid'];
				$this->raceID	= $_SESSION['raceID'];
				$this->progress = $_SESSION['progress'];
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
				$query = "UPDATE users SET lastLogin=? WHERE uid=? LIMIT 1";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("si", $time, $this->userid);
				if ($stmt->execute()){
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
	public function isRealUser(): bool
	{
		$query = "
			SELECT alevel
			FROM ".$this->db_prefix."users
			WHERE email=? 
			AND password=? 
			LIMIT 1
			";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("ss", $this->userName, $this->password);
		$stmt->execute();
		$q = $stmt->get_result();
		if($q->num_rows)
		{
			$row = $q->fetch_row();
			$this->access = $row[0];
			Debug::printMsg(__CLASS__, __FUNCTION__, "Validated '$this->userName'");
			$query = "SELECT users.uid, userdata.rid, userdata.progress as prog FROM ".$this->db_prefix."users , userdata	
				   		  WHERE users.email=? AND 
						  users.password=?  LIMIT 1"; //SETS USER ID
			$stmt = $this->db_link->prepare($query);
			$stmt->bind_param("ss", $this->userName, $this->password);
			$stmt->execute();
			$ided = $stmt->get_result(); //IDK
			$row = $ided->fetch_object(); //IDK
			$this->userid = $row->uid; /// SETS USER ID
			$this->raceID = $row->rid;
			$this->progress = $row->prog;
			return true;
		}
		Debug::printMsg(__CLASS__, __FUNCTION__, "Could not validate user '$this->userName'");
		return false;
	}
	
	public function isAllowed(int $reqAcc): bool
	{
		return (bool)((int)$reqAcc & $this->access);
	}
	
	/**
	 * Logs out user
	 *
	 */
	public function logOut(): void
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
	 * @param string $value
	 * @return string
	 */
	public function salt(string $value): string
	{
		return md5(crypt($value, '.u55ybcbC,ufzQu2'));
	}
	
	/**
	 * Adds user to the database
	 *
	 * @param string $userName
	 * @param string $password
	 * @param int $access
	 * @param string $email
	 * @param int $rid
	 * @param string $hpname
	 * @param string $ip
	 * @return bool
	 */
	public function addUser(string $userName, string $password, int $access = 1, string $email, int $rid, string $hpname, string $ip): bool
	{
		$userName	= $this->clean_sql($userName);
		$password	= $this->clean_sql($this->salt($password));
		$hpname 	= $this->clean_sql($hpname);
		$email		= $this->clean_sql($email);
		$rid 		= $this->clean_sql($rid);
		$ip			= $this->clean_sql($ip);
		
		$query = "SELECT `uname` FROM `users` WHERE `ip`=? LIMIT 1";
		$stmt = $this->db_link->prepare($query);
		$stmt->bind_param("s", $ip);
		$stmt->execute();
		$q = $stmt->get_result();
		$chk = $q->fetch_row();
		if(!$chk[0]) {
			if(is_numeric($access))
			{
				$query = "
					INSERT INTO ".$this->db_prefix."users
					(uname, password, alevel, email, ip)
					VALUES (?, ?, ?, ?, ?)
					";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("ssiss", $userName, $password, $access, $email, $ip);
				$stmt->execute();
				
				$query = "SELECT `uid` FROM `users` WHERE `uname`=? LIMIT 1";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("s", $userName);
				$stmt->execute();
				$q = $stmt->get_result();
				$x = $q->fetch_object();
				
				$query = "INSERT INTO ".$this->db_prefix."bank (uid,onHand) VALUES (?, 250000)";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("i", $x->uid);
				$stmt->execute();
				
				$query = "INSERT INTO ".$this->db_prefix."units (uid,untrained) VALUES (?, 250)";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("i", $x->uid);
				$stmt->execute();
				
				$query = "INSERT INTO ".$this->db_prefix."technology (uid,unitProd) VALUES (?, 1)";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("i", $x->uid);
				$stmt->execute();
				
				$query = "INSERT INTO ".$this->db_prefix."power (uid) VALUES (?)";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("i", $x->uid);
				$stmt->execute();
				
				$query = "INSERT INTO ".$this->db_prefix."rank (uid) VALUES (?)";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("i", $x->uid);
				$stmt->execute();
				
				$query = "INSERT INTO ".$this->db_prefix."planets (uid,plnt_name,isHome) VALUES (?, ?, 1)";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("is", $x->uid, $hpname);
				$stmt->execute();
				
				$xxx = (string) $this->genUniqueLink();
				$query = "INSERT INTO ".$this->db_prefix."userdata (uid,rid,actionTurns,link) VALUES (?, ?, 250, ?)";
				$stmt = $this->db_link->prepare($query);
				$stmt->bind_param("iis", $x->uid, $rid, $xxx);
				$stmt->execute();
				
				echo "Registration Complete";
				return true;
			}
		} else { 
			echo "Your IP is used by another account only 1 account per IP"; 
		}
		return false;
	}
	
	public function genUniqueLink(): string
	{
		$time = time();
		$uniqID = "";
		for ($i = 0; $i < strlen((string)$time) / 2; $i++){
			$uniqID .= chr(rand(ord('a'), ord('z')));
		}
		$uniqID .= $time;
		return $this->clean_sql($uniqID);	
	}
}
?>