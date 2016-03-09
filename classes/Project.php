<?php
/* 
 * Class Project describes individual students' projects
 * 
 */
class Project implements ContentManipulator{
    //class properties/data
    private $id;
    private $title;
    private $abstract;
    private $author;
    private $category;
    private $department;
    private $supervisor;
    private $year;
    private $projectFile;
    private $dateUploaded = " CURRENT_DATE ";
    private $status = 0;
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
        $sql = "INSERT INTO project (title, abstract, author, category, department, supervisor, year, project_file, date_uploaded, status) "
                ."VALUES ('{$this->title}','{$this->abstract}','{$this->author}','{$this->category}','{$this->department}','{$this->supervisor}','{$this->year}','{$this->projectFile}',$this->dateUploaded,'{$this->status}')";
        if($this->notEmpty($this->title,$this->abstract,$this->category,$this->projectFile)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, project successfully added!"); }
            else{ $json = array("status" => 2, "msg" => "Error adding project! ".  mysqli_error($this->dbObj->connection)); }
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
        $sql = "DELETE FROM project WHERE id = $this->id AND supervisor = '".$this->supervisor."' ";
        if($this->notEmpty($this->id,$this->supervisor)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, project successfully deleted!"); }
            else{ $json = array("status" => 2, "msg" => "Error deleting project! ".  mysqli_error($this->dbObj->connection));  }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $this->dbObj->close();//Close Database Connection
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            return $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); return json_encode($json); }
    }

    /** Method that fetches projects from database
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g category_id > 9
     * @param string $sort column name to be used as sort parameter
     */
    public function fetch($column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM project ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM project WHERE $condition ORDER BY $sort";}
        $data = $this->dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){
                $fetProjectStat = 'icon-check-empty'; $fetProjectRolCol = 'btn-warning'; $fetProjectRolTit = "Approve Project";
                if($r['status'] == 1){  $fetProjectStat = 'icon-check'; $fetProjectRolCol = 'btn-success'; $fetProjectRolTit = "Disapprove Project";}
                $fileType = pathinfo($r['project_file'],PATHINFO_EXTENSION);
                $result[] = array("id" => $r['id'], "title" =>  utf8_encode($r['title']), 'author' => utf8_encode(Student::getName($this->dbObj, $r['author']). ' ['.Student::getSingle($this->dbObj, 'matric_number', $r['author']).']'), 'abstract' => trim(strip_tags(utf8_encode($r['abstract'])), 35), 'category' =>  utf8_encode($r['category']), 'department' =>  utf8_encode($r['department']), 'supervisor' =>  utf8_encode("<a href='mailto:".$r['supervisor']."' title='Email Supervisor'>".Supervisor::getName($this->dbObj, $r['supervisor'])."</a>"), 'year' => utf8_encode($r['year']), 'projectFile' =>  utf8_encode($r['project_file']), 'dateUploaded' =>  utf8_encode($r['date_uploaded']), 'status' => $r['status'], 'actionLink' =>utf8_encode('<button data-id="'.$r['id'].'" data-title="'.$r['title'].'" data-project-file="'.$r['project_file'].'" class="btn btn-danger btn-small delete-project" title="Delete"><i class="btn-icon-only icon-trash"> </i></button> <button data-id="'.$r['id'].'" data-title="'.$r['title'].'" data-status="'.$r['status'].'"  class="btn '.$fetProjectRolCol.' btn-small approve-project"  title="'.$fetProjectRolTit.'"><i class="btn-icon-only '.$fetProjectStat.'"> </i></button>'), 'fileType' => $fileType);
            }
            $json = array("status" => 1, "info" => $result);
        } else{ $json = array("status" => 2, "msg" => "No project found. Empty result. ".mysqli_error($this->dbObj->connection)); }
        
        $this->dbObj->close();
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            return $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); return json_encode($json); }
    }
    
    /** Method that fetches projects from database for JQuery Data Table
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g category_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return JSON JSON encoded project details
     */
    public function fetchForJQDT($draw, $totalData, $totalFiltered, $customSql="", $column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM project ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM project WHERE $condition ORDER BY $sort";}
        if($customSql !=""){ $sql = $customSql; }
        $data = $this->dbObj->fetchAssoc($sql);
        $result =array(); $fetProjectStat = 'icon-check-empty'; $fetProjectRolCol = 'btn-warning'; $fetProjectRolTit = "Approve Project";
        if(count($data)>0){
            foreach($data as $r){ 
                $fetProjectStat = 'icon-check-empty'; $fetProjectRolCol = 'btn-warning'; $fetProjectRolTit = "Approve Project";
                if($r['status'] == 1){  $fetProjectStat = 'icon-check'; $fetProjectRolCol = 'btn-success'; $fetProjectRolTit = "Disapprove Project";}
                $result[] = array(utf8_encode(' <button data-id="'.$r['id'].'" data-title="'.$r['title'].'" data-author="'.$r['author'].'"  data-category="'.$r['category'].'" data-department="'.$r['department'].'" data-supervisor="'.$r['supervisor'].'" data-year="'.$r['year'].'" data-project-file="'.$r['project_file'].'" data-date="'.$r['date_uploaded'].'" class="btn btn-info btn-small edit-project"  title="Edit"><i class="btn-icon-only icon-pencil"></i> <span id="JQDTabstractholder" class="hidden">'.$r['abstract'].'</span> </button> <button data-id="'.$r['id'].'" data-title="'.$r['title'].'" data-project-file="'.$r['project_file'].'" class="btn btn-danger btn-small delete-project" title="Delete"><i class="btn-icon-only icon-trash"> </i></button> <button data-id="'.$r['id'].'" data-title="'.$r['title'].'" data-status="'.$r['status'].'"  class="btn '.$fetProjectRolCol.' btn-small approve-project"  title="'.$fetProjectRolTit.'"><i class="btn-icon-only '.$fetProjectStat.'"> </i></button>'), $r['id'], utf8_encode($r['title']), utf8_encode(substr(stripslashes(strip_tags($r['abstract'])),0, 60).".."), utf8_encode(Student::getName($this->dbObj, $r['author'])), utf8_encode($r['category']), utf8_encode($r['year']), utf8_encode("<a href='project/".$r['project_file']."'>View/Download Project</a>"), utf8_encode($r['date_uploaded']));//
            }
            $json = array("status" => 1,"draw" => intval($draw), "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Or empty result. ".mysqli_error($this->dbObj->connection), "draw" => intval($draw),  "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => false); }
        $this->dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }
    
    /** Method that fetches projects from database for JQuery Data Table
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g category_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return JSON JSON encoded project details
     */
    public function fetchForStudentJQDT($draw, $totalData, $totalFiltered, $customSql="", $column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM project ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM project WHERE $condition ORDER BY $sort";}
        if($customSql !=""){ $sql = $customSql; }
        $data = $this->dbObj->fetchAssoc($sql);
        $result =array(); $fetProjectStat = '<button>Pending</button>'; 
        if(count($data)>0){
            foreach($data as $r){ 
                $fetProjectStat = '<button class="btn btn-warning">Pending</button>';
                if($r['status'] == 1){  $fetProjectStat = '<button  class="btn btn-success">Approved</button>'; }
                $result[] = array(utf8_encode($fetProjectStat), $r['id'], utf8_encode($r['title']), utf8_encode(substr(stripslashes(strip_tags($r['abstract'])),0, 60).".."), utf8_encode("<a href='mailto:".$r['supervisor']."' title='Email Supervisor'>".Supervisor::getName($this->dbObj, $r['supervisor'])."</a>"), utf8_encode($r['category']), utf8_encode($r['year']), utf8_encode("<a href='project/".$r['project_file']."'>View/Download Project</a>"), utf8_encode($r['date_uploaded']));//
            }
            $json = array("status" => 1,"draw" => intval($draw), "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Or empty result. ".mysqli_error($this->dbObj->connection), "draw" => intval($draw),  "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => false); }
        $this->dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }
    
    /** Method that fetches projects from database for JQuery Data Table
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g category_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return JSON JSON encoded project details
     */
    public function fetchForGuestJQDT($draw, $totalData, $totalFiltered, $customSql="", $column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM project ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM project WHERE $condition ORDER BY $sort";}
        if($customSql !=""){ $sql = $customSql; }
        $data = $this->dbObj->fetchAssoc($sql);
        $result =array();  
        if(count($data)>0){
            foreach($data as $r){ 
                $result[] = array(utf8_encode($r['title']), utf8_encode(substr($r['abstract'],0, 100).".."), utf8_encode(Department::getName($this->dbObj, $r['department'])), utf8_encode("<a href='mailto:".$r['supervisor']."' title='Email Supervisor'>".Supervisor::getName($this->dbObj, $r['supervisor'])."</a>"), utf8_encode($r['category']), utf8_encode($r['year']), utf8_encode("<a href='project/".$r['project_file']."'>View/Download Project</a>"), utf8_encode($r['date_uploaded']));//
            }
            $json = array("status" => 1,"draw" => intval($draw), "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Or empty result. ".mysqli_error($this->dbObj->connection), "draw" => intval($draw),  "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => false); }
        $this->dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that update details of a project
     * @return JSON JSON encoded success or failure message
     */
    public function update() {
        $sql = "UPDATE project SET title = '{$this->title}', abstract = '{$this->abstract}', category = '{$this->category}', year = '{$this->year}', project_file = '{$this->projectFile}' WHERE id = $this->id ";
        if(!empty($this->id)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, project successfully update!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating project! ".  mysqli_error($this->dbObj->connection));   }
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
    public static function updateSingle($dbObj, $field, $value, $id){
        $sql = "UPDATE project SET $field = '{$value}' WHERE id = $id ";
        if(!empty($id)){
            $result = $dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, project successfully update!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating project! ".  mysqli_error($dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $dbObj->close();
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

    
}