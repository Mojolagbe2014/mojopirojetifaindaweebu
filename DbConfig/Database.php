<?php
//it Reqiures DbConfig
require(__ROOT__."/DbConfig/DbConfig.php");

/** Databse Class 
* It handles connection to database
* and database logic or oprations<br>
* It is a child of DbConfiguration 
*/
class Database extends DbConfig{
    /** Class instant fields */
    var $connection;

    /** Class constructor */
    public function Database(){
        /** It handle Database Connection and Selection */
        $this->connection = mysqli_connect($this->get_dbServer(), $this->get_dbUser(), $this->get_dbPass(),$this->get_dbName())
        or die("Database Connection and Selection Failed. " . mysqli_error($this->connection)); //error message
    }

    /** Method for querying database */
    public function query($query){
        $result = mysqli_query($this->connection, $query) or 
        die("Invalid Query: ".mysqli_error($this->connection));
        if($result){
            return $result;
        } else { return false; }
    }

    /** Method for fetching data as associate array( i.e using database field names) */
    public function fetchAssoc($query){
        $result = $this->query($query); 
        $rows = array();
        while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
                $rows[] = $row;
        }
        return $rows;
    }

    /** Method for fetching data as numeric array( i.e using database column as $row[0]) */
    public function fetchNum($query){
        $result = $this->query($query); 
        $rows = array();
        while($row = mysqli_fetch_array($result,MYSQLI_NUM)){
                $rows[] = $row;
        }
        return $rows;
    }

    /** Method for closing connection */
    public function close(){/** Method for closing connection */
        //If connection to database is on then close it
        if (isset($this->connection)) {
            mysqli_close($this->connection);
        }
    }
    
}
?>