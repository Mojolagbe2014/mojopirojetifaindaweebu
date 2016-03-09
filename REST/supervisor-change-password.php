<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$supervisorObj = new Supervisor($dbObj); // Create an object of Supervisor class
$errorArr = array(); //Array of errors
$newPassword ="";

if(filter_input(INPUT_POST, "LoggedInSupervisorId")!=NULL){
    $postVars = array('oldPassword', 'newPassword', 'confirmPassword'); // Form fields names
    //Validate the POST variables and add up to error message if empty
    foreach ($postVars as $postVar){
        switch($postVar){
            case 'confirmPassword':    if(filter_input(INPUT_POST,$postVar) !== filter_input(INPUT_POST, "newPassword")){
                            array_push ($errorArr, "Password Mismatch !!! ");
                            if(filter_input(INPUT_POST, $postVar) == "") {array_push ($errorArr, "Please confirm your password. ");}}
                            break;
            case 'oldPassword'     :   $supervisorObj->passWord = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                            if($supervisorObj->passWord == "") {array_push ($errorArr, "Please enter $postVar ");}
                            break;
            default:        if(filter_input(INPUT_POST, $postVar) == "") {array_push ($errorArr, "Please enter $postVar. ");}
                            break;
        }
    }
    //If validated and not empty submit it to database
    if(count($errorArr) < 1)   {
        $supervisorObj->id = filter_input(INPUT_POST, "LoggedInSupervisorId");
        $newPassword =  mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, 'newPassword'));
        echo  $supervisorObj->changePassword($newPassword);
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

else if(filter_input(INPUT_GET, "LoggedInSupervisorId")!=NULL){
    $postVars = array('oldPassword', 'newPassword', 'confirmPassword'); // Form fields names
    //Validate the POST variables and add up to error message if empty
    foreach ($postVars as $postVar){
        switch($postVar){
            case 'confirmPassword':    if(filter_input(INPUT_GET,$postVar) !== filter_input(INPUT_GET, "newPassword")){
                            array_push ($errorArr, "Password Mismatch !!! ");
                            if(filter_input(INPUT_GET, $postVar) == "") {array_push ($errorArr, "Please confirm your password. ");}}
                            break;
            case 'oldPassword'     :   $supervisorObj->passWord = filter_input(INPUT_GET, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_GET, $postVar)) :  ''; 
                            if($supervisorObj->passWord == "") {array_push ($errorArr, "Please enter $postVar ");}
                            break;
            default:        if(filter_input(INPUT_GET, $postVar) == "") {array_push ($errorArr, "Please enter $postVar. ");}
                            break;
        }
    }
    //If validated and not empty submit it to database
    if(count($errorArr) < 1)   {
        $supervisorObj->id = filter_input(INPUT_GET, "LoggedInSupervisorId");
        $newPassword =  mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_GET, 'newPassword'));
        echo  $supervisorObj->changePassword($newPassword);
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