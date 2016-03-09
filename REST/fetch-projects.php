<?php
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$projectObj = new Project($dbObj); // Create an object of Project class
$errorArr = array(); //Array of errors

if(filter_input(INPUT_POST, "fetchProjects")!=NULL){
    $postVars = array('author'); // Form fields names
    $requestData= $_REQUEST;
    $columns = array(0 => 'status', 1 =>'id', 2 => 'title', 3 => 'abstract', 4 => 'supervisor', 5 => 'category', 6 => 'year', 7 => 'project_file', 7 => 'date_uploaded');

    // getting total number records without any search
    $query = $dbObj->query("SELECT * FROM project WHERE  author = '".$requestData['author']."' ");
    $totalData = mysqli_num_rows($query);
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    $sql = "SELECT * FROM project WHERE author = '".$requestData['author']."' "; //id, name, short_name, category, start_date, code, description, media, amount, date_registered
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

    echo $projectObj->fetchForStudentJQDT($requestData['draw'], $totalData, $totalFiltered, $sql);
} 
elseif(filter_input(INPUT_POST, "fetchAllProjects")!=NULL){
    $requestData= $_REQUEST;
    $columns = array(0 => 'title', 1 => 'abstract', 2 => 'department', 3 => 'supervisor', 4 => 'category', 5 => 'year', 6 => 'project_file', 7 => 'date_uploaded');

    // getting total number records without any search
    $query = $dbObj->query("SELECT * FROM project WHERE status = 1 ");
    $totalData = mysqli_num_rows($query);
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    $sql = "SELECT * FROM project WHERE status = 1  "; //id, name, short_name, category, start_date, code, description, media, amount, date_registered
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

    echo $projectObj->fetchForGuestJQDT($requestData['draw'], $totalData, $totalFiltered, $sql);
} 
elseif(filter_input(INPUT_POST, "fetchForSupervisor")!=NULL){
    $postVars = array('supervisor','department'); // Form fields names
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
        echo $projectObj->fetch("*", "  supervisor LIKE '".$projectObj->supervisor."' AND department LIKE  '".$projectObj->department."' AND status = 0 ", " date_uploaded ");
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
elseif(filter_input(INPUT_GET, "fetchForSupervisor")!=NULL){
    $postVars = array('supervisor','department'); // Form fields names
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
        echo $projectObj->fetch("*", "  supervisor LIKE '".$projectObj->supervisor."' AND department LIKE  '".$projectObj->department."' AND status = 0 ", " date_uploaded ");
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
elseif(filter_input(INPUT_GET, "fetchForMobileGuest")!=NULL){
    //Get all needed parameters
    $totalNo = filter_input(INPUT_GET, "totalNo", FILTER_VALIDATE_INT) 
            ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_GET, "totalNo", FILTER_VALIDATE_INT)) :  100;
    $offset = filter_input(INPUT_GET, "offset", FILTER_VALIDATE_INT) 
            ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_GET, "offset", FILTER_VALIDATE_INT)) :  0;
    $searchParam = filter_input(INPUT_GET, "searchParam")
            ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_GET, "searchParam")) :  "";
    
    if($searchParam!=""){ echo $projectObj->fetch("*", " status = 1 AND (category LIKE '%$searchParam%' OR year LIKE '%$searchParam%' OR title LIKE '%$searchParam%') ", " year DESC LIMIT $totalNo OFFSET $offset "); }
    else{ echo $projectObj->fetch("*", " status = 1 ", " year DESC LIMIT $totalNo OFFSET $offset "); }
}
elseif(filter_input(INPUT_POST, "fetchForMobileStudent")!=NULL){
    //Get all needed parameters
    $thisAuthor = filter_input(INPUT_POST, "author", FILTER_VALIDATE_INT) 
            ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, "author", FILTER_VALIDATE_INT)) :  100;
    
    echo $projectObj->fetch("*", " author = $thisAuthor ", " status "); 
}
elseif(filter_input(INPUT_GET, "fetchForMobileStudent")!=NULL){
    //Get all needed parameters
    $thisAuthor = filter_input(INPUT_GET, "author", FILTER_VALIDATE_INT) 
            ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_GET, "author", FILTER_VALIDATE_INT)) :  100;
    
    echo $projectObj->fetch("*", " author = $thisAuthor ", " status "); 
}
else{ /* fetch all project */ echo $projectObj->fetch(); }