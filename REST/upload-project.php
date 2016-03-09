<?php
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$projectObj = new Project($dbObj); // Create an object of Project class
$errorArr = array(); //Array of errors
$projectMedFil = "";

if(filter_input(INPUT_POST, "title")!==NULL){
    $postVars = array('title','abstract','author', 'category', 'department', 'supervisor', 'year', 'file'); // Form fields names
    //Validate the POST variables and add up to error message if empty
    foreach ($postVars as $postVar){
        switch($postVar){
            case 'file':    $projectObj->projectFile = basename($_FILES["file"]["name"]) ? rand(100000, 1000000)."_".  strtolower(str_replace(" ", "_", 'project file')).".".pathinfo(basename($_FILES["file"]["name"]),PATHINFO_EXTENSION): ""; 
                            if($projectObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar for your project");}
                            $projectMedFil = $projectObj->projectFile; 
                            break;
            default     :   $projectObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                            if($projectObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar for your project");}
                            break;
        }
    }
    //If validated and not empty submit it to database
    if(count($errorArr) < 1)   {
        //$target_dir = PROJECT_FILES_PATH. $projectMedFil;
        $target_file = PROJECT_FILES_PATH. $projectMedFil;
        $uploadOk = 1; $msg = '';
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        // Check if file already exists
        if (file_exists($target_file)) { $msg .= "Sorry, file already exists."; $uploadOk = 0; }
        // Check file size
        if ($_FILES["file"]["size"] > 500000) { $msg .= "Sorry, your file is too large."; $uploadOk = 0; }
        // Allow certain file formats
        if($imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "docx" && $imageFileType != "ppt" ) {
            $msg .= "Sorry, only PDF, DOC, DOCX & PowerPoint files are allowed.";
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) { $msg .= "Sorry, your file was not uploaded."; } 
        else {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $msg .= "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
                $status = 'ok';
                echo $projectObj->add();
            } else {
                $msg .= "Sorry, there was an error uploading your file.";
                $json = array("status" => 0, "msg" => $msg); 
                $dbObj->close();//Close Database Connection
                if(array_key_exists('callback', $_GET)){
                    header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
                    echo $_GET['callback'].'('.json_encode($json).');';
                }else{ header('Content-Type: application/json'); echo json_encode($json); }
            }
        }
        
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

if(filter_input(INPUT_GET, "title")!==NULL){
    $postVars = array('title','abstract','author', 'category', 'department', 'supervisor', 'year', 'file'); // Form fields names
    //Validate the POST variables and add up to error message if empty
    foreach ($postVars as $postVar){
        switch($postVar){
            case 'file':    $projectObj->projectFile = basename($_FILES["file"]["name"]) ? rand(100000, 1000000)."_".  strtolower(str_replace(" ", "_", 'project file')).".".pathinfo(basename($_FILES["file"]["name"]),PATHINFO_EXTENSION): ""; 
                            if($projectObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar for your project");}
                            $projectMedFil = $projectObj->projectFile; 
                            break;
            default     :   $projectObj->$postVar = filter_input(INPUT_GET, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_GET, $postVar)) :  ''; 
                            if($projectObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar for your project");}
                            break;
        }
    }
    //If validated and not empty submit it to database
    if(count($errorArr) < 1)   {
        //$target_dir = PROJECT_FILES_PATH. $projectMedFil;
        $target_file = PROJECT_FILES_PATH. $projectMedFil;
        $uploadOk = 1; $msg = '';
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        // Check if file already exists
        if (file_exists($target_file)) { $msg .= "Sorry, file already exists."; $uploadOk = 0; }
        // Check file size
        if ($_FILES["file"]["size"] > 500000) { $msg .= "Sorry, your file is too large."; $uploadOk = 0; }
        // Allow certain file formats
        if($imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "docx" && $imageFileType != "ppt" ) {
            $msg .= "Sorry, only PDF, DOC, DOCX & PowerPoint files are allowed.";
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) { $msg .= "Sorry, your file was not uploaded."; } 
        else {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $msg .= "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
                $status = 'ok';
                echo $projectObj->add();
            } else {
                $msg .= "Sorry, there was an error uploading your file.";
                $json = array("status" => 0, "msg" => $msg); 
                $dbObj->close();//Close Database Connection
                if(array_key_exists('callback', $_GET)){
                    header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
                    echo $_GET['callback'].'('.json_encode($json).');';
                }else{ header('Content-Type: application/json'); echo json_encode($json); }
            }
        }
        
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