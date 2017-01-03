<?php
define('INCLUDE_CHECK',true);

if(!defined('INCLUDE_CHECK')) die('You are not allowed to execute this file directly');

/* Database config */
$db_host		= 'localhost';
$db_user		= 'deweyhaf_admin';
$db_pass		= 'rJv&?yF#+%eT';
$db_database	= 'deweyhaf_maindata'; 

$link = mysqli_connect($db_host,$db_user,$db_pass);

if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

mysqli_select_db($link,$db_database);
mysqli_query($link, "SET names UTF8");

session_name('deweyLogin');
session_set_cookie_params(2*7*24*60*60);
session_start();

if($_SESSION['id'] && !isset($_COOKIE['deweyRemember']) && !$_SESSION['rememberMe'])
{
	// If you are logged in, but you don't have the deweyRemember cookie (browser restart)
	// and you have not checked the rememberMe checkbox:

	$_SESSION = array();
	session_destroy();
	
	// Destroy the session
}

if(isset($_GET['logoff']))
{
	$_SESSION = array();
	session_destroy();
	
	header("Location: ../main");
	exit;
}

?>
