<?php
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$studentObj = new Student($dbObj); // Create an object of Student class
$errorArr = array(); //Array of errors

if(filter_input(INPUT_POST, "update") != NULL){
    $postVars = array('id', 'name','matricNo','email','department','phone'); // Form fields names
    //Validate the POST variables and add up to error message if empty
    foreach ($postVars as $postVar){
        switch($postVar){
            case 'email':   $studentObj->$postVar = filter_input(INPUT_POST, $postVar, FILTER_VALIDATE_EMAIL) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar, FILTER_VALIDATE_EMAIL)) :  ''; 
                            if($studentObj->$postVar === "") {array_push ($errorArr, "Please enter valid $postVar ");}
                            break;
            default     :   $studentObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                            if($studentObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                            break;
        }
    }
    //If validated and not empty submit it to database
    if(count($errorArr) < 1)   {
        echo $studentObj->update();
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

if(filter_input(INPUT_GET, "update") != NULL){
    $postVars = array('id', 'name','matricNo','email','department','phone'); // Form fields names
    //Validate the POST variables and add up to error message if empty
    foreach ($postVars as $postVar){
        switch($postVar){
            case 'email':   $studentObj->$postVar = filter_input(INPUT_GET, $postVar, FILTER_VALIDATE_EMAIL) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_GET, $postVar, FILTER_VALIDATE_EMAIL)) :  ''; 
                            if($studentObj->$postVar === "") {array_push ($errorArr, "Please enter valid $postVar ");}
                            break;
            default     :   $studentObj->$postVar = filter_input(INPUT_GET, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_GET, $postVar)) :  ''; 
                            if($studentObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                            break;
        }
    }
    //If validated and not empty submit it to database
    if(count($errorArr) < 1)   {
        echo $studentObj->update();
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