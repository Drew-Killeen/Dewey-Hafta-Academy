<?php

require 'scripts/header.php';
require 'scripts/functions.php';

if($_POST['submit']=='Login') {
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

if($_POST['submit'] == 'Reset') {
    $username = mysqli_real_escape_string($link, $_POST['username']);
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $match = mysqli_query($link, "SELECT id FROM dewey_members WHERE usr='".$username."' AND email='".$email."'");
    if(mysqli_num_rows($match) > 0) {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        $pass = implode($pass); //turn the array into a string

        send_mail(	'noreply@deweyhaftaacademy.x10host.com',
                    $email,
                    'Dewey Hafta Academy - Reset Password',
                    'Your password has been reset. You can update it and access your account <a href="http://deweyhaftaacademy.x10host.com/sign_in?key='.$pass.'">here</a>.');

        mysqli_query($link, "UPDATE dewey_members SET pass='".md5($pass)."' WHERE usr='".$username."' AND email='".$email."'");
        //$_SESSION['msg']['success'] = "Check your email for instructions on how to reset your password.";
        header("Location: main");
        exit();
    }
    else {
        //$_SESSION['msg']['err'] = "Error: An account was not found with that username and email.";
    }
}

if($_POST['submit'] == 'Update') {
    if($_POST['newpass'] != $_POST['confirmpass']) {
        $_SESSION['msg']['err'] = "Passwords do not match";
        header("Location: sign_in?key=".$_GET['key']);
        exit();
    }
    else {
        $newPass = md5(mysqli_real_escape_string($link, $_POST['newpass']));
        $key = md5(mysqli_real_escape_string($link, $_GET['key']));
        mysqli_query($link, "UPDATE dewey_members SET pass='".$newPass."' WHERE pass='".$key."'");
        $_SESSION['msg']['success'] = "Password updated. You may now log in to your account.";
        header("Location: sign_in");
        exit();
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
        if($_GET['key']):
            $key = mysqli_real_escape_string($link, $_GET['key']);
            $accounts = mysqli_query($link, "SELECT id FROM dewey_members WHERE pass='".md5($key)."'");
            if(mysqli_num_rows($accounts) > 0) {
                echo "
                    <h1>Reset Password</h1>
                    <form method='post' action=''>
                        <input type='password' name='newpass' size='23' placeholder='New password' />
                        <input type='password' name='confirmpass' size='23' placeholder='Confirm password' />
                        <input type='submit' name='submit' value='Update' />
                    </form>";
            }
            else {
                $_SESSION['msg']['err'] = "Error: Account not found in system";
                header("Location: sign_in");
                exit();
            } 
        elseif(!$_SESSION['id'] && $_GET['action'] != 'forgot_password'):
    ?>
        <div class="left">
            <!-- Login Form -->
            <form class="clearfix" action="" method="post">
                <h1>Member Login</h1>
                <label>Username: <input class="field" type="text" name="username" id="username" value="" size="23" /></label>
                <label>Password: <input class="field" type="password" name="password" id="password" size="23" /></label>
                <label><input name="rememberMe" id="rememberMe" type="checkbox" checked="checked" value="1" /> &nbsp;Remember me</label>
                <div class="clear"></div>
                <input type="submit" name="submit" value="Login" class="bt_login" /> <a href="?action=forgot_password">Forgot password?</a>
            </form>
        </div>
    <?php
        elseif(!$_SESSION['id']):
    ?>
        <h1>Reset Password</h1>
        <form action="" method="post">
            <input class="field" type="text" name="username" id="username" value="" size="23" placeholder="Username" /> 
            <input class="field" type="text" name="email" id="email" value="" size="23" placeholder="Email address" /> 
            <input type="submit" name="submit" value="Reset"/>
            <p class="note">You will receive an email with instructions on how to reset your password.</p>
        </form>
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
