<?php
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$projectObj = new Project($dbObj); // Create an object of Project class
$errorArr = array(); //Array of errors

if(filter_input(INPUT_GET, "approveProject")!=NULL){
    echo Project::updateSingle($dbObj, ' status ',  ' 1 ', filter_input(INPUT_GET, 'id'));   
} 