<?php 
error_reporting(E_ALL);          // Report all PHP errors
ini_set('display_errors', 1);    // Display errors on the page (for development)
ini_set('log_errors', 1);        


include_once ('../append/connection.php');
include_once 'cls_functions.php';

if(isset($_GET['destroy'])){
    session_destroy();
}
$store = (isset($_GET['store']) && $_GET['store'] != '') ? $_GET['store'] : "managedashboard.myshopify.com";
$functions = new Client_functions();
