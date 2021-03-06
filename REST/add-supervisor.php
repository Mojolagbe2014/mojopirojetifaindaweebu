<?php
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$supervisorObj = new Supervisor($dbObj); // Create an object of Supervisor class
$errorArr = array(); //Array of errors

if(filter_input(INPUT_POST, "adminId") != NULL){
    $postVars = array('id','name','passWord','department'); // Form fields names
    //Validate the POST variables and add up to error message if empty
    foreach ($postVars as $postVar){
        switch($postVar){
            default     :   $supervisorObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                            if($supervisorObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                            break;
        }
    }
    if(count($errorArr) < 1)   {
        echo $supervisorObj->add();
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
else{ 
        $json = array("status" => 0, "msg" => 'You are not logged in.'); 
        $dbObj->close();//Close Database Connection
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            echo $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); echo json_encode($json); }
    }