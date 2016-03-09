<?php 
/** Required Database Constants i.e constants.php 
* as a means of hidden the conn. parameters */

/** Hidden Connection string location */
require_once(__ROOT__.'/includes/constants.php');

/** Class DbConfig holds the application's configuration settings  */
class DbConfig {
    /** Instant variables  */
    //Initialize database server
    private $dbServer= DB_SERVER; 
    private $dbUser = DB_USER;
    private $dbPass = DB_PASSWORD;
    private $dbName = DB_NAME;

    /** Class constructor */
    public function DbConfig(){
    }

    /** Database connection string Getter Methods */
    public function get_dbServer(){ return $this->dbServer; }
    public function get_dbUser(){ return $this->dbUser; }
    public function get_dbPass(){ return $this->dbPass; }
    public function get_dbName(){ return $this->dbName; }
}
?>