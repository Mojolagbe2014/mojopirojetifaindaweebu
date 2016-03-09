<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
require_once '../swiftmailer/lib/swift_required.php';

$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$studentObj = new Student($dbObj); // Create an object of Student class
$adminObj = new Admin($dbObj); // Create an object of Student class
$supervisorObj = new Supervisor($dbObj); // Create an object of Student class

$errorArr = array(); //Array of errors
$newPassword =""; $thisEmail = ""; $userType = '';

if(filter_input(INPUT_POST, "email")!=NULL){
    $postVars = array('email', 'userType'); // Form fields names
    foreach ($postVars as $postVar){
        switch($postVar){
            case 'email':   $thisEmail = filter_input(INPUT_POST, $postVar, FILTER_VALIDATE_EMAIL) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar, FILTER_VALIDATE_EMAIL)) :  ''; 
                            if($thisEmail == "") {array_push ($errorArr, "Please enter valid email ");}
                            break;
            case 'userType':   $userType = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                            if($userType == "") {array_push ($errorArr, "Please enter valid $postVar ");}
                            break;
        }
    }
    if(count($errorArr) < 1)   {
        if($userType=='Student'){
            $studentObj->email = $thisEmail;
            $studentObj->passWord = 'rst'.rand(100000, 1000000);
            $newPassword = $studentObj->passWord;
        } else if($userType=='Admin'){
            $adminObj->email = $thisEmail;
            $adminObj->passWord = 'rst'.rand(100000, 1000000);
            $newPassword = $adminObj->passWord;
        } else if($userType=='Supervisor'){
            $supervisorObj->id = $thisEmail;
            $supervisorObj->passWord = 'rst'.rand(100000, 1000000);
            $newPassword = $supervisorObj->passWord;
        }
        
        // Create the mail transport configuration
        $transport = Swift_MailTransport::newInstance();

        // Create the message
        $message = Swift_Message::newInstance();
        $message->setTo(array($thisEmail => "Unilorin Project Finder User" ));
        $message->setSubject("Password Reset Message");
        $message->setBody("Your password has been reset. Email: $thisEmail \n New Password: $newPassword");
        $message->setFrom("noreply@unilorinprojectfinder.com", "Unilorin Project Finder Admin");

        // Send the email
        $mailer = Swift_Mailer::newInstance($transport);
        $mailer->send($message);
        
        if($userType=='Student'){ echo  $studentObj->resetPassword(); }
        if($userType=='Admin'){ echo  $adminObj->resetPassword(); }
        if($userType=='Supervisor'){ echo  $supervisorObj->resetPassword(); }
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

if(filter_input(INPUT_GET, "email")!=NULL){
    $postVars = array('email', 'userType'); // Form fields names
    foreach ($postVars as $postVar){
        switch($postVar){
            case 'email':   $thisEmail = filter_input(INPUT_GET, $postVar, FILTER_VALIDATE_EMAIL) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_GET, $postVar, FILTER_VALIDATE_EMAIL)) :  ''; 
                            if($thisEmail == "") {array_push ($errorArr, "Please enter valid email ");}
                            break;
            case 'userType':   $userType = filter_input(INPUT_GET, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_GET, $postVar)) :  ''; 
                            if($userType == "") {array_push ($errorArr, "Please enter valid $postVar ");}
                            break;
        }
    }
    if(count($errorArr) < 1)   {
        if($userType=='Student'){
            $studentObj->email = $thisEmail;
            $studentObj->passWord = 'rst'.rand(100000, 1000000);
            $newPassword = $studentObj->passWord;
        } else if($userType=='Admin'){
            $adminObj->email = $thisEmail;
            $adminObj->passWord = 'rst'.rand(100000, 1000000);
            $newPassword = $adminObj->passWord;
        } else if($userType=='Supervisor'){
            $supervisorObj->id = $thisEmail;
            $supervisorObj->passWord = 'rst'.rand(100000, 1000000);
            $newPassword = $supervisorObj->passWord;
        }
        
        // Create the mail transport configuration
        $transport = Swift_MailTransport::newInstance();

        // Create the message
        $message = Swift_Message::newInstance();
        $message->setTo(array($thisEmail => "Unilorin Project Finder User" ));
        $message->setSubject("Password Reset Message");
        $message->setBody("Your password has been reset. Email: $thisEmail \n New Password: $newPassword");
        $message->setFrom("noreply@unilorinprojectfinder.com", "Unilorin Project Finder Admin");

        // Send the email
        $mailer = Swift_Mailer::newInstance($transport);
        $mailer->send($message);
        
        if($userType=='Student'){ echo  $studentObj->resetPassword(); }
        if($userType=='Admin'){ echo  $adminObj->resetPassword(); }
        if($userType=='Supervisor'){ echo  $supervisorObj->resetPassword(); }
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