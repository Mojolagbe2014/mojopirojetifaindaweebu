<?php
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$departmentObj = new Department($dbObj); // Create an object of Project class
$errorArr = array(); //Array of errors

echo $departmentObj->fetch();