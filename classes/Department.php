<?php
/* 
 * Class Department describes individual students' departments
 * 
 */
class Department implements ContentManipulator{
    //class properties/data
    private $id;
    private $name;
    private $faculty;
    private $dbObj;

    
    public function __construct($dbObj) {
        $this->dbObj =  $dbObj;
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
    
    /**  
     * Method that submits a project into the database
     */
    function add(){
        $sql = "INSERT INTO department (name, faculty) "
                ."VALUES ('{$this->name}','{$this->faculty}')";
        if($this->notEmpty($this->faculty,$this->name)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, department successfully added!"); }
            else{ $json = array("status" => 2, "msg" => "Error adding department! ".  mysqli_error($this->dbObj->connection)); }
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
        $sql = "DELETE FROM department WHERE id = $this->id ";
        if($this->notEmpty($this->id)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, department successfully deleted!"); }
            else{ $json = array("status" => 2, "msg" => "Error deleting department! ".  mysqli_error($this->dbObj->connection));  }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $this->dbObj->close();//Close Database Connection
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            return $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); return json_encode($json); }
    }
    
    /** Method that fetches departments from database for JQuery Data Table
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g category_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return JSON JSON encoded department details
     */
    public function fetchForJQDT($draw, $totalData, $totalFiltered, $customSql="", $column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM department ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM department WHERE $condition ORDER BY $sort";}
        if($customSql !=""){ $sql = $customSql; }
        $data = $this->dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){ 
                $result[] = array(utf8_encode(' <button data-id="'.$r['id'].'" data-name="'.$r['name'].'" data-faculty="'.$r['faculty'].'" class="btn btn-info btn-small edit-department"  title="Edit"><i class="btn-icon-only icon-pencil"> </i> </button> <button data-id="'.$r['id'].'" data-name="'.$r['name'].'" class="btn btn-danger btn-small delete-department" title="Delete"><i class="btn-icon-only icon-trash"> </i></button>'), $r['id'], utf8_encode($r['name']), utf8_encode($r['faculty']));//
            }
            $json = array("status" => 1,"draw" => intval($draw), "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Or empty result. ".mysqli_error($this->dbObj->connection), "draw" => intval($draw),  "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => false); }
        $this->dbObj->close();
        //header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that fetches departments from database
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g category_id > 9
     * @param string $sort column name to be used as sort parameter
     */
    public function fetch($column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM department ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM department WHERE $condition ORDER BY $sort";}
        $data = $this->dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){
                $result[] = array("id" => $r['id'], "name" =>  utf8_encode($r['name']), 'faculty' =>  utf8_encode($r['faculty']));
            }
            $json = array("status" => 1, "info" => $result);
        } else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Or empty result. ".mysqli_error($this->dbObj->connection)); }
        $this->dbObj->close();
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            return $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); return json_encode($json); }
    }

    /** Method that update details of a department
     * @return JSON JSON encoded success or failure message
     */
    public function update() {
        $sql = "UPDATE department SET name = '{$this->name}', faculty = '{$this->faculty}' WHERE id = $this->id ";
        if(!empty($this->id)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, department successfully update!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating department! ".  mysqli_error($this->dbObj->connection));   }
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
        $sql = "UPDATE department SET $field = '{$value}' WHERE id = $id ";
        if(!empty($id)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, department successfully update!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating department! ".  mysqli_error($this->dbObj->connection));   }
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

    /** getName() fetches the name of a department using the department $id
     * @param object $dbObj Database connectivity and manipulation object
     * @param int $id Category id of the category whose name is to be fetched
     * @return string Name of the category
     */
    public static function getName($dbObj, $id) {
        $thisDeptName = '';
        $thisDeptNames = $dbObj->fetchNum("SELECT name FROM department WHERE id = '{$id}' LIMIT 1");
        foreach ($thisDeptNames as $thisDeptNames) { $thisDeptName = $thisDeptNames[0]; }
        return $thisDeptName;
    }
    
}