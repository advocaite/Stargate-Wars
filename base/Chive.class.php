<?php
// Base::Chive.class.php
class Chive
{
    // General Info
    /**
     * Name of the class
     *
     * @var string|null $name
     */
    public ?string $name = null;
    
    /**
     * Database table prefix
     *
     * @var string|null $db_prefix
     */
    public ?string $db_prefix = null;

    /**
     * Database server location
     *
     * @var string|null $db_server
     */
    public ?string $db_server = null;

    /**
     * Database name
     *
     * @var string|null $db_name
     */
    public ?string $db_name = null;

    /**
     * Database username
     *
     * @var string|null $db_username
     */
    public ?string $db_username = null;

    /**
     * Database password
     *
     * @var string|null $db_password
     */
    public ?string $db_password = null;

    /**
     * MySQLi Resource link for database connections
     *
     * @var mysqli|null $db_link
     */
    public ?mysqli $db_link = null;
    public int $queryCount = 0;

    /**
     * Constructor for Chive
     * @param string $name Name of the class
     *
     */
    public function __construct(string $name = "")
    {
        global $conf;
        $this->name = $name;
        $this->db_server = $conf['db_server'];
        $this->db_name = $conf['db_name'];
        $this->db_username = $conf['db_username'];
        $this->db_password = $conf['db_password'];
        $this->db_prefix = $conf['db_prefix'];
        
        Debug::printMsg(__CLASS__, __FUNCTION__, "Class created with <b>\$name</b> ".$this->name);
    }
    
    /**
     * Creates a MySQLi Resource link to $db_link
     *
     */
    public function connectToDB(): void
    {
        Debug::printMsg(__CLASS__, __FUNCTION__, "Connecting to DB...");
        $this->db_link = new mysqli($this->db_server, $this->db_username, $this->db_password, $this->db_name);
        if($this->db_link->connect_error)
        {
            Debug::printMsg(__CLASS__, __FUNCTION__, "Couldn't connect to DB ".$this->db_link->connect_error);
        } else {
            Debug::printMsg(__CLASS__, __FUNCTION__, "Connected to database");
        }
    }
    
    /**
     * Checks if a connection to the database server has been made
     *
     * @return bool
     */
    public function connected(): bool
    {
        return $this->db_link && !$this->db_link->connect_error;
    }
    
    /**
     * Cleans $string for a MySQL statement
     *
     * @param string $string
     * @param int $quotes
     * @return string
     */
    public function clean_sql(string $string, int $quotes = 1): string
    {
        if(!$this->connected()) $this->connectToDB();
        
        // Stripslashes
        if (get_magic_quotes_gpc())
        {
            $string = stripslashes($string);
        }
        // Quote if not integer
        if (!is_numeric($string) && $quotes)
        {
            $string = "'".$this->db_link->real_escape_string($string)."'";
        }
        return $string;
    }
    
    /**
     * Queries Database.
     * Returns true or false depending on success
     *
     * @param string $query
     * @return mixed
     */
    public function query(string $query)
    {
        if(!$this->connected()) $this->connectToDB();
        
        $r = $this->db_link->query($query);
        if($r)
        {
            Debug::printMsg(__CLASS__, __FUNCTION__, "Query successful: ".$query."\r\n");
            $this->queryCount++;
            return $r;
        }
        Debug::printMsg(__CLASS__, __FUNCTION__, "Query unsuccessful - <b>ERROR:</b> ".$this->db_link->error." FROM QUERY - \"".$query."\"\n");
        $this->queryCount++;
        return false;
    }
}

class page_gen {
    //
    // PRIVATE CLASS VARIABLES
    //
    private float $_start_time;
    private float $_stop_time;
    private float $_gen_time;
    
    //
    // USER DEFINED VARIABLES
    //
    public int $round_to;
    
    //
    // CLASS CONSTRUCTOR
    //
    public function __construct() {
        if (!isset($this->round_to)) {
            $this->round_to = 4;
        }
    }
    
    //
    // FIGURE OUT THE TIME AT THE BEGINNING OF THE PAGE
    //
    public function start(): void {
        $microstart = explode(' ', microtime());
        $this->_start_time = $microstart[0] + $microstart[1];
    }
    
    //
    // FIGURE OUT THE TIME AT THE END OF THE PAGE
    //
    public function stop(): void {
        $microstop = explode(' ', microtime());
        $this->_stop_time = $microstop[0] + $microstop[1];
    }
    
    //
    // CALCULATE THE DIFFERENCE BETWEEN THE BEGINNNG AND THE END AND RETURN THE VALUE
    //
    public function gen(): float {
        $this->_gen_time = round($this->_stop_time - $this->_start_time, $this->round_to);
        return $this->_gen_time; 
    }
} 
?>