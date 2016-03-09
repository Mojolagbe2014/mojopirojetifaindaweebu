<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$projectObj = new Project($dbObj); // Create an object of ProjectCategory class
$errorArr = array(); //Array of errors
$newFile =''; $oldFile = '';


if(filter_input(INPUT_POST, "fetchProjects") != NULL){
    $requestData= $_REQUEST;
    $columns = array(0 => 'status', 1 =>'id', 2 => 'title', 3 => 'abstract', 4 => 'author', 5 => 'category', 6 => 'year', 7 => 'project_file', 8 => 'date_uploaded');

    // getting total number records without any search
    $query = $dbObj->query("SELECT * FROM project WHERE  supervisor = '".$requestData['supervisor']."' AND department =  '".$requestData['department']."' ");
    $totalData = mysqli_num_rows($query);
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    $sql = "SELECT * FROM project WHERE   supervisor = '".$requestData['supervisor']."' AND department =  '".$requestData['department']."' "; //id, name, short_name, category, start_date, code, description, media, amount, date_registered
    if(!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
            $sql.=" AND ( title LIKE '%".$requestData['search']['value']."%' ";    
            $sql.=" OR abstract LIKE '%".$requestData['search']['value']."%' ";
            $sql.=" OR category LIKE '%".$requestData['search']['value']."%' ";
            $sql.=" OR year LIKE '%".$requestData['search']['value']."%' ";
            $sql.=" OR date_uploaded LIKE '%".$requestData['search']['value']."%' ";
            $sql.=" OR department LIKE '%".$requestData['search']['value']."%' ) ";
    }
    $query = $dbObj->query($sql);
    $totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
    $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
    /* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	

    echo $projectObj->fetchForJQDT($requestData['draw'], $totalData, $totalFiltered, $sql);
}

if(filter_input(INPUT_POST, "deleteProject")!=NULL){
    $postVars = array('supervisor','id','projectFile'); // Form fields names
    //Validate the POST variables and add up to error message if empty
    foreach ($postVars as $postVar){
        switch($postVar){
            default     :   $projectObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                            if($projectObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                            break;
        }
    }
    //If validated and not empty submit it to database
    if(count($errorArr) < 1)   {
        if(unlink(PROJECT_FILES_PATH.$projectObj->projectFile))
            echo $projectObj->delete();
    }
    //Else show error messages
    else{ 
        $json = array("status" => 0, "msg" => $errorArr); 
        $dbObj->close();//Close Database Connection
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            echo $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); echo json_encode($json); }
    }
} 

if(filter_input(INPUT_GET, "deleteProject")!=NULL){
    $postVars = array('supervisor','id','projectFile'); // Form fields names
    //Validate the POST variables and add up to error message if empty
    foreach ($postVars as $postVar){
        switch($postVar){
            default     :   $projectObj->$postVar = filter_input(INPUT_GET, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_GET, $postVar)) :  ''; 
                            if($projectObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                            break;
        }
    }
    //If validated and not empty submit it to database
    if(count($errorArr) < 1)   {
        if(unlink(PROJECT_FILES_PATH.$projectObj->projectFile))
            echo $projectObj->delete();
    }
    //Else show error messages
    else{ 
        $json = array("status" => 0, "msg" => $errorArr); 
        $dbObj->close();//Close Database Connection
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            echo $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); echo json_encode($json); }
    }
} 

if(filter_input(INPUT_POST, "projectForm") == 'update'){
    $postVars = array('id','title','abstract','category','year','projectFile'); // Form fields names
        $oldFile = $_REQUEST['oldFile']; 
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'projectFile':   $newFile = basename($_FILES["file"]["name"]) ? rand(100000, 1000000)."_".  strtolower(str_replace(" ", "_", 'project file')).".".pathinfo(basename($_FILES["file"]["name"]),PATHINFO_EXTENSION): ""; 
                                $projectObj->$postVar = $newFile;
                                if($projectObj->$postVar == "") { $projectObj->$postVar = $oldFile;}
                                $projectMedFil = $newFile;
                                break;
                default     :   $projectObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($projectObj->$postVar == "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
                
            }
        }
        if(count($errorArr) < 1)   {
            $target_file = PROJECT_FILES_PATH. $projectMedFil;
            $uploadOk = 1; $msg = '';
            if($newFile !=""){
                if (move_uploaded_file($_FILES["file"]["tmp_name"], PROJECT_FILES_PATH.$projectMedFil)) {
                    $msg .= "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
                    $status = 'ok'; if(file_exists(PROJECT_FILES_PATH.$oldFile)) unlink(PROJECT_FILES_PATH.$oldFile); $uploadOk = 1;
                } else { $uploadOk = 0; }
            }
            if($uploadOk == 1){ echo $projectObj->update(); }
            else {
                $msg = " Sorry, there was an error uploading your project media. ERROR: ".$msg;
                $json = array("status" => 0, "msg" => $msg); 
                $dbObj->close();//Close Database Connection
                if(array_key_exists('callback', $_GET)){
                    header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
                    echo $_GET['callback'].'('.json_encode($json).');';
                }else{ header('Content-Type: application/json'); echo json_encode($json); }
            }
        }
        else{ 
            $json = array("status" => 0, "msg" => $errorArr); 
            $dbObj->close();//Close Database Connection
            if(array_key_exists('callback', $_GET)){
                header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
                echo $_GET['callback'].'('.json_encode($json).');';
            }else{ header('Content-Type: application/json'); echo json_encode($json); }
        }
} 

if(filter_input(INPUT_GET, "projectForm") == 'update'){
    $postVars = array('id','title','abstract','category','year','projectFile'); // Form fields names
        $oldFile = $_REQUEST['oldFile']; 
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'projectFile':   $newFile = basename($_FILES["file"]["name"]) ? rand(100000, 1000000)."_".  strtolower(str_replace(" ", "_", 'project file')).".".pathinfo(basename($_FILES["file"]["name"]),PATHINFO_EXTENSION): ""; 
                                $projectObj->$postVar = $newFile;
                                if($projectObj->$postVar == "") { $projectObj->$postVar = $oldFile;}
                                $projectMedFil = $newFile;
                                break;
                default     :   $projectObj->$postVar = filter_input(INPUT_GET, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_GET, $postVar)) :  ''; 
                                if($projectObj->$postVar == "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
                
            }
        }
        if(count($errorArr) < 1)   {
            $target_file = PROJECT_FILES_PATH. $projectMedFil;
            $uploadOk = 1; $msg = '';
            if($newFile !=""){
                if (move_uploaded_file($_FILES["file"]["tmp_name"], PROJECT_FILES_PATH.$projectMedFil)) {
                    $msg .= "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
                    $status = 'ok'; if(file_exists(PROJECT_FILES_PATH.$oldFile)) unlink(PROJECT_FILES_PATH.$oldFile); $uploadOk = 1;
                } else { $uploadOk = 0; }
            }
            if($uploadOk == 1){ echo $projectObj->update(); }
            else {
                $msg = " Sorry, there was an error uploading your project media. ERROR: ".$msg;
                $json = array("status" => 0, "msg" => $msg); 
                $dbObj->close();//Close Database Connection
                if(array_key_exists('callback', $_GET)){
                    header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
                    echo $_GET['callback'].'('.json_encode($json).');';
                }else{ header('Content-Type: application/json'); echo json_encode($json); }
            }
        }
        else{ 
            $json = array("status" => 0, "msg" => $errorArr); 
            $dbObj->close();//Close Database Connection
            if(array_key_exists('callback', $_GET)){
                header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
                echo $_GET['callback'].'('.json_encode($json).');';
            }else{ header('Content-Type: application/json'); echo json_encode($json); }
        }
} 

if(filter_input(INPUT_GET, "approveProject")!=NULL){
        $postVars = array('id', 'status'); // Form fields names
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'status':  $projectObj->$postVar = filter_input(INPUT_GET, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_GET, $postVar, FILTER_VALIDATE_INT)) :  0; 
                                if($projectObj->$postVar == 1) {$projectObj->$postVar = 0;} 
                                elseif($projectObj->$postVar == 0) {$projectObj->$postVar = 1;}
                                break;
                default     :   $projectObj->$postVar = filter_input(INPUT_GET, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_GET, $postVar)) :  ''; 
                                if($projectObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        if(count($errorArr) < 1)   {
            echo Project::updateSingle($dbObj, ' status ',  $projectObj->status, $projectObj->id); 
        }
        else{ 
            $json = array("status" => 0, "msg" => $errorArr); 
            $dbObj->close();//Close Database Connection
            if(array_key_exists('callback', $_GET)){
                header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
                echo $_GET['callback'].'('.json_encode($json).');';
            }else{ header('Content-Type: application/json'); echo json_encode($json); }
        }

    }