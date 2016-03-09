<?php
/* 
 * Class Supervisor describes individual students' supervisors
 * 
 */
class Supervisor implements ContentManipulator{
    //class properties/data
    private $id;
    private $name;
    private $department;
    private $passWord;
    private $dbObj;

    
    public function __construct($dbObj) { $this->dbObj =  $dbObj;  }

    //Using Magic__set and __get
    public function __get($property) {
        if (property_exists($this, $property)) { return $this->$property; }
    }
    public function __set($property, $value) {
        if (property_exists($this, $property)) { $this->$property = $value; }
    }
    
    /**  
     * Method that submits a project into the database
     */
    function add(){
        $sql = "INSERT INTO supervisor (id, name, password, department) "
                ."VALUES ('{$this->id}','{$this->name}','".md5($this->passWord)."','{$this->department}')";
        if($this->notEmpty($this->id,$this->name,$this->passWord,$this->department)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, supervisor successfully added!"); }
            else{ $json = array("status" => 2, "msg" => "Error adding supervisor! ".  mysqli_error($this->dbObj->connection)); }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted. All fields must be filled."); }
        
        $this->dbObj->close();//Close Database Connection
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            return $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); return json_encode($json); }
    }

    /** 
     * Method for deleting a project
     */
    public function delete(){
        $sql = "DELETE FROM supervisor WHERE id = '$this->id' ";
        if($this->notEmpty($this->id)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, supervisor successfully deleted!"); }
            else{ $json = array("status" => 2, "msg" => "Error deleting supervisor! ".  mysqli_error($this->dbObj->connection));  }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $this->dbObj->close();//Close Database Connection
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            return $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); return json_encode($json); }
    }

    /** Method that fetches supervisors from database
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g category_id > 9
     * @param string $sort column name to be used as sort parameter
     */
    public function fetch($column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM supervisor ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM supervisor WHERE $condition ORDER BY $sort";}
        $data = $this->dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){
                $result[] = array("id" => $r['id'], "name" =>  utf8_encode($r['name']), 'department' =>  utf8_encode($r['department']));
            }
            $json = array("status" => 1, "info" => $result);
        } else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Or empty result. ".mysqli_error($this->dbObj->connection)); }
        
        $this->dbObj->close();
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            return $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); return json_encode($json); }
    }

    /** Method that fetches supervisors from database for JQuery Data Table
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g category_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return JSON JSON encoded supervisor details
     */
    public function fetchForJQDT($draw, $totalData, $totalFiltered, $customSql="", $column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM supervisor ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM supervisor WHERE $condition ORDER BY $sort";}
        if($customSql !=""){ $sql = $customSql; }
        $data = $this->dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){ 
                $result[] = array(utf8_encode(' <button data-id="'.$r['id'].'" data-name="'.$r['name'].'" data-department="'.$r['department'].'" class="btn btn-info btn-small edit-supervisor"  title="Edit"><i class="btn-icon-only icon-pencil"> </i> </button> <button data-id="'.$r['id'].'" data-name="'.$r['name'].'"  data-department="'.$r['department'].'" class="btn btn-danger btn-small delete-supervisor" title="Delete"><i class="btn-icon-only icon-trash"> </i></button> '), $r['id'], utf8_encode($r['name']), utf8_encode(Department::getName($this->dbObj, $r['department'])));//
            }
            $json = array("status" => 1,"draw" => intval($draw), "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Or empty result. ".mysqli_error($this->dbObj->connection), "draw" => intval($draw),  "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => false); }
        $this->dbObj->close();
        //header('Content-type: application/json');
        return json_encode($json);
    }
    
    /**
     * Sign in handler 
     */
    public function signIn(){
        $sql = "SELECT * FROM supervisor WHERE id = '".$this->id."' AND password = '".md5($this->passWord)."' LIMIT 1 ";
        $data = $this->dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){
                $result[] = array("id" => $r['id'], "name" =>  utf8_encode($r['name']), 'department' =>  utf8_encode($r['department']));
            }
            $json = array("status" => 1, "info" => $result);
        } else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Login details incorrect. ".mysqli_error($this->dbObj->connection)); }
        
        $this->dbObj->close();
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            return $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); return json_encode($json); }
    }
    
    /** Method that update details of a supervisor
     * @return JSON JSON encoded success or failure message
     */
    public function update() {
        $sql = "UPDATE supervisor SET name = '{$this->name}', department = '{$this->department}' WHERE id = '$this->id' ";
        if(!empty($this->id)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, supervisor successfully update!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating supervisor! ".  mysqli_error($this->dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $this->dbObj->close();
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            return $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); return json_encode($json); }
    }
    
    /** Method that update single field detail of a project
     * @param string $field Column to be updated 
     * @param string $value New value of $field (Column to be updated)
     * @param int $id Id of the post to be updated
     */
    public static function updateSingle($field, $value, $id){
        $sql = "UPDATE supervisor SET $field = '{$value}' WHERE id = $id ";
        if(!empty($id)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, supervisor successfully update!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating supervisor! ".  mysqli_error($this->dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $this->dbObj->close();
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            return $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); return json_encode($json); }
    }
    
    /** Empty string checker  */
    public function notEmpty() {
        foreach (func_get_args() as $arg) {
            if (empty($arg)) { return false; } 
            else {continue; }
        }
        return true;
    }
    
    /** pwdExists checks if a password truely exists in the database
     * @return Boolean True for exists, while false for not
     */
    private function pwdExists(){
        $sql =  "SELECT * FROM supervisor WHERE password = '".md5($this->passWord)."' AND id = '$this->id' LIMIT 1 ";
        $result = $this->dbObj->fetchAssoc($sql);
        if($result != false){ return true; }
        else{ return false;    }
    } 
    
    /** Change Password
     * @param string $newPassword New password
     * @return JSON JSON Object success or failure
     */
    public function changePassword($newPassword){
        $sql = "UPDATE supervisor SET password = '".md5($newPassword)."' WHERE id = '$this->id' ";
        $pwdExists = $this->pwdExists();//Check if old password is corect
        if($pwdExists==TRUE){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, supervisor password successfully updated!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating supervisor password! ".  mysqli_error($this->dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Old password you typed is incorrect. Please retype old password."); }
        $this->dbObj->close();
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            return $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); return json_encode($json); }
    }
    
    /** getName() fetches the name of a department using the department $id
     * @param object $dbObj Database connectivity and manipulation object
     * @param int $id Category id of the category whose name is to be fetched
     * @return string Name of the category
     */
    public static function getName($dbObj, $id) {
        $thisSuprName = '';
        $thisSuprNames = $dbObj->fetchNum("SELECT name FROM supervisor WHERE id = '{$id}' LIMIT 1");
        foreach ($thisSuprNames as $thisSuprNames) { $thisSuprName = $thisSuprNames[0]; }
        return $thisSuprName;
    }
    
    /** Reset Password
     * @return JSON JSON Object success or failure
     */
    public function resetPassword(){
        $sql = "UPDATE supervisor SET password = '".md5($this->passWord)."' WHERE id = '$this->id' ";
        if($this->emailExists()){
            $result = $this->dbObj->query($sql);
            if($result != false){ $json = array("status" => 1, "msg" => "Done, supervisor password successfully reset! An email has been sent to you."); }
            else{ $json = array("status" => 2, "msg" => "Error reseting supervisor password! ".  mysqli_error($this->dbObj->connection));   }
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
        $sql =  "SELECT * FROM supervisor WHERE id = '$this->id' LIMIT 1 ";
        $result = $this->dbObj->fetchAssoc($sql);
        if($result != false){ return true; }
        else{ return false;    }
    }
}