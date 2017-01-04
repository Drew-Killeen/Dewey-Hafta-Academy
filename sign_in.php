<?php

require 'scripts/header.php';

if($_POST['submit']=='Login')
{
	// Checking whether the Login form has been submitted
	
	$err = array();
	// Will hold our errors
	
	
	if(!$_POST['username'] || !$_POST['password'])
		$err[] = 'All the fields must be filled in.';
	
	if(!count($err))
	{
		$_POST['username'] = mysqli_real_escape_string($link, $_POST['username']);
		$_POST['password'] = mysqli_real_escape_string($link, $_POST['password']);
		$_POST['rememberMe'] = (int)$_POST['rememberMe'];
		
		// Escaping all input data

		$row = mysqli_fetch_assoc(mysqli_query($link, "SELECT id,usr,privilege FROM dewey_members WHERE usr='{$_POST['username']}' AND pass='".md5($_POST['password'])."'"));

		if($row['usr'])
		{
			// If everything is OK login
			
			$_SESSION['usr'] = $row['usr'];
			$_SESSION['id'] = $row['id'];
            $_SESSION['privilege'] = $row['privilege'];
			$_SESSION['rememberMe'] = $_POST['rememberMe'];
			
			// Store some data in the session
			
			setcookie('deweyRemember',$_POST['rememberMe']);
		}
		else $err[]='Wrong username and/or password.';
	}
	
	if($err) 
    {
	   $_SESSION['msg']['err'] = implode('<br />',$err);
       header("Location: sign_in");
	   exit;
    }
	// Save the error messages in the session

    else 
    {
	   header("Location: main");
	   exit;
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    
<?php include_once('templates/htmlHeader.php'); ?>
    
</head>
<body>
    
<div id="header">
    <a href="main" id="title">Dewey Hafta Academy</a>
    <span id="usrHeader"><a href="sign_up">Sign up</a></span>
</div>
    
<div id="main">
    <div class="container">
    <?php
        if(!$_SESSION['id']):
    ?>
        <div class="left">
            <!-- Login Form -->
            <form class="clearfix" action="" method="post">
                <h1>Member Login</h1>
                <label class="grey" for="username">Username:</label>
                <input class="field" type="text" name="username" id="username" value="" size="23" />
                <label class="grey" for="password">Password:</label>
                <input class="field" type="password" name="password" id="password" size="23" />
                <label><input name="rememberMe" id="rememberMe" type="checkbox" checked="checked" value="1" /> &nbsp;Remember me</label>
                <div class="clear"></div>
                <input type="submit" name="submit" value="Login" class="bt_login" />
            </form>
        </div>
    <?php
        else:
        
        header("Location: main");
        
	   endif;
    ?>
    </div>
</div>

    <?php require 'scripts/jsload.php'; ?>
    
</body>
</html>
