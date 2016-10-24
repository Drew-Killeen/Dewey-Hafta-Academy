<?php
define('INCLUDE_CHECK',true);

require 'connect.php';

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
