<?php
/**
 * @author TIMCA Computer Inc.
 * @copyright (c) 2015, TIMCA Computer Inc.
 */


/** Class Admin describes the admin application user */
class Admin implements ContentManipulator{
    private $id;
    private $name;
    private $email;
    private $passWord;
    private $dbObj;
    
    
    //Class constructor
    public function Admin($dbObj){
        $this->dbObj = $dbObj;
    }


    //Using Magic__set and __get
    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }
    
    /** Method for adding new admin */
    public function add() {
        $sql = "INSERT INTO admin "
            . "(name, email, password) VALUES "
            . "('{$this->name}','{$this->email}','".md5($this->passWord)."')";
        if($this->notEmpty($this->name,$this->email,$this->passWord)){
            $qur = mysqli_query($this->dbObj->connection,$sql);
            if($qur){ $json = array("status" => 1, "msg" => "Done admin added successfully!"); }
            else{ $json = array("status" => 2, "msg" => "Error adding admin!".  mysqli_error($this->dbObj->connection)); }
        }else{ $json = array("status" => 0, "msg" => "Fill all available fields."); }
        $this->dbObj->close();//Close Database Connection
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            return $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); return json_encode($json); }
    }
    
     /** fetch method that fetches admin details 
     * @return json JSON encoded string
     */
    public function fetch($column = "*", $condition = "", $sort = "id") {
        $sql = "SELECT $column FROM admin ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM admin WHERE $condition ORDER BY $sort";}
        $data = $this->dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)> 1){
            foreach($data as $r){
                $result[] = array("id" => $r['id'], "name" =>  utf8_encode($r['name']), 'email' =>  utf8_encode($r['email']));
            }
            $json = array("status" => 1, "info" => $result);
        } else{ $json = array("status" => 2, "msg" => "No admin fetched. Empty result. ".mysqli_error($this->dbObj->connection)); }
        $this->dbObj->close();
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            return $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); return json_encode($json); }
    }

    /** delete() deletes an admin using admin id {$this->id}
     *  @return Mixed Mysql result or False for failure
     */
    public function delete() {
        $sql = "DELETE FROM admin WHERE id = $this->id ";
        if($this->notEmpty($this->id)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ return $result; }
            else{ return $result;    }
        }
        else{return false; }
    }

    /** Empty string checker  */
    public function notEmpty() {
        foreach (func_get_args() as $arg) {
            if (empty($arg)) { return false; } 
            else {continue; }
        }
        return true;
    }

    /** Update() updates admins info 
     * @return Boolean True|False
     */
    public function update() {
        $sql = "UPDATE admin SET name = '{$this->name}', email = '{$this->email}' WHERE id = $this->id ";
        if($this->notEmpty($this->id)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ return true; }
            else{ return false;    }
        }
        else{return false; }
    }

    /**
     * Sign in handler 
     */
    public function signIn(){
        $sql = "SELECT * FROM admin WHERE email = '".$this->email."' AND passWord = '".md5($this->passWord)."' LIMIT 1 ";
        $data = $this->dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){
                $result[] = array("id" => $r['id'], "name" =>  utf8_encode($r['name']), 'email' =>  utf8_encode($r['email']));
            }
            $json = array("status" => 1, "info" => $result);
        } else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Login details incorrect. ".mysqli_error($this->dbObj->connection)); }
        
        $this->dbObj->close();
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            return $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); return json_encode($json); }
    }
    
    /** pwdExists checks if a password truely exists in the database
     * @return Boolean True for exists, while false for not
     */
    private function pwdExists(){
        $sql =  "SELECT * FROM admin WHERE password = '".md5($this->passWord)."' AND id = $this->id LIMIT 1 ";
        $result = $this->dbObj->fetchAssoc($sql);
        if($result != false){ return true; }
        else{ return false;    }
    } 
    
    /** Change Password
     * @param string $newPassword New password
     * @return JSON JSON Object success or failure
     */
    public function changePassword($newPassword){
        $sql = "UPDATE admin SET password = '".md5($newPassword)."' WHERE id = $this->id ";
        $pwdExists = $this->pwdExists();//Check if old password is corect
        if($pwdExists==TRUE){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, user password successfully updated!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating user password! ".  mysqli_error($this->dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Old password you typed is incorrect. Please retype old password."); }
        $this->dbObj->close();
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            return $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); return json_encode($json); }
    }
    
    /** Reset Password
     * @return JSON JSON Object success or failure
     */
    public function resetPassword(){
        $sql = "UPDATE admin SET password = '".md5($this->passWord)."' WHERE email = '$this->email' ";
        if($this->emailExists()){
            $result = $this->dbObj->query($sql);
            if($result != false){ $json = array("status" => 1, "msg" => "Done, admin password successfully reset! An email has been sent to you."); }
            else{ $json = array("status" => 2, "msg" => "Error reseting admin password! ".  mysqli_error($this->dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "The email you entered does not exist in our database."); }
        $this->dbObj->close();
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            return $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); return json_encode($json); }
    }
    
    /** emailExists checks if a password truely exists in the database
     * @return Boolean True for exists, while false for not
     */
    private function emailExists(){
        $sql =  "SELECT * FROM admin WHERE email = '$this->email' LIMIT 1 ";
        $result = $this->dbObj->fetchAssoc($sql);
        if($result != false){ return true; }
        else{ return false;    }
    }
}