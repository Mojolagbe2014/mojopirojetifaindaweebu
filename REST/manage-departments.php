<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$departmentObj = new Department($dbObj); // Create an object of DepartmentCategory class
$errorArr = array(); //Array of errors

if(filter_input(INPUT_POST, "fetchDepartments") != NULL){
    $requestData= $_REQUEST;
    $columns = array(0 => 'status', 1 =>'id', 2 => 'name', 3 => 'faculty');

    // getting total number records without any search
    $query = $dbObj->query("SELECT * FROM department ");
    $totalData = mysqli_num_rows($query);
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    $sql = "SELECT * FROM department WHERE 1=1 "; //id, name, short_name, category, start_date, code, description, media, amount, date_registered
    if(!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
            $sql.=" AND ( name LIKE '%".$requestData['search']['value']."%' ";    
            $sql.=" OR id LIKE '%".$requestData['search']['value']."%' ";
            $sql.=" OR faculty LIKE '%".$requestData['search']['value']."%' ) ";
    }
    $query = $dbObj->query($sql);
    $totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
    $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
    /* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	

    echo $departmentObj->fetchForJQDT($requestData['draw'], $totalData, $totalFiltered, $sql);
}

if(filter_input(INPUT_POST, "deleteThisDepartment")!=NULL){
    $postVars = array('id'); // Form fields names
    foreach ($postVars as $postVar){
        switch($postVar){
            default     :   $departmentObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                            if($departmentObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                            break;
        }
    }
    if(count($errorArr) < 1)   {
        echo $departmentObj->delete();
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

if(filter_input(INPUT_POST, "departmentForm") == 'update'){
    $postVars = array('id','name','faculty'); // Form fields names
    foreach ($postVars as $postVar){
        switch($postVar){
            default     :   $departmentObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                            if($departmentObj->$postVar == "") {array_push ($errorArr, "Please enter $postVar ");}
                            break;

        }
    }
    if(count($errorArr) < 1)   { echo $departmentObj->update(); }
    else{ 
        $json = array("status" => 0, "msg" => $errorArr); 
        $dbObj->close();//Close Database Connection
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            echo $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); echo json_encode($json); }
    }
} 

if(filter_input(INPUT_POST, "departmentForm") == 'add'){
    $postVars = array('name','faculty'); // Form fields names
    //Validate the POST variables and add up to error message if empty
    foreach ($postVars as $postVar){
        switch($postVar){
            default     :   $departmentObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                            if($departmentObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                            break;
        }
    }
    if(count($errorArr) < 1)   {  echo $departmentObj->add(); }
    else{ 
        $json = array("status" => 0, "msg" => $errorArr); 
        $dbObj->close();//Close Database Connection
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            echo $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); echo json_encode($json); }
    }
} 