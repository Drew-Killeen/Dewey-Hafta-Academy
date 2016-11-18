<?php

require 'templates/header.php';
require 'templates/functions.php';

if($_POST['submit']=='Register')
{
	// If the Register form has been submitted
	
	$err = array();
    
    if(!$_POST['username'] || !$_POST['email'] || !$_POST['pass'] || !$_POST['pass_again']) 
    {
        $err[]='All fields must be filled in.';
    }
	else {
        if(strlen($_POST['username'])<4 || strlen($_POST['username'])>32)
        {
            $err[]='Your username must be between 3 and 32 characters.';
        }

        if(preg_match('/[^a-z0-9\-\_\.]+/i',$_POST['username']))
        {
            $err[]='Your username contains invalid characters.';
        }

        if(!checkEmail($_POST['email']))
        {
            $err[]='Your email is not valid.';
        }

        if($_POST['pass'] != $_POST['pass_again'])
        {
            $err[]='Passwords do not match.';
        }
        
        if(!$_POST['g-recaptcha-response'])
        {
            $err[]='Please check the captcha form.';
        }
        else {
            $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LcoYwoUAAAAAJs-KgcBmMX4RLT3LJllpnax6U9K&response=".$_POST['g-recaptcha-response']."&remoteip=".$_SERVER['REMOTE_ADDR']);
            $responseKeys = json_decode($response,true);

            if(intval($responseKeys["success"]) !== 1) 
            {
                $err[]='CAPTCHA cannot be verified. Please try again later.';
            }
        }
    }
    
	if(!count($err))
	{
		// If there are no errors
		
		$_POST['email'] = mysql_real_escape_string($_POST['email']);
		$_POST['username'] = mysql_real_escape_string($_POST['username']);
        $_POST['pass'] = mysql_real_escape_string($_POST['pass']);
		// Escape the input data
		
		mysql_query("INSERT INTO dewey_members(usr,email,pass,regIP,dt)
				VALUES(
				'".$_POST['username']."',
				'".$_POST['email']."',
                '".md5($_POST['pass'])."',
				'".$_SERVER['REMOTE_ADDR']."',
				NOW()
                )");
        
			send_mail(	'noreply@deweyhaftaacademy.x10host.com',
						'admin@deweyhaftaacademy.x10host.com',
						'Registration System - New Account',
						'A new account was registered at '.date("Y-m-d h:i:sa").' from IP '.$_SERVER['REMOTE_ADDR'].'. Email address '.$_POST['email'].', username '.$_POST['username'].'.');
            send_mail(	'noreply@deweyhaftaacademy.x10host.com',
						$_POST['email'],
						'Registration System - New Account',
						"Your account has been successfully created. An administrator must approve your account before you have full access to the site. If you do not receive an email confirming your account's approval or denial within 48 hours, please contact admin@deweyhaftaacademy.x10host.com.");
            
			$_SESSION['msg']['success']='Account successfully created. An administrator has been notified and will approve your account shortly.';
	}

	if(count($err))
	{
		$_SESSION['msg']['err'] = implode('<br />',$err);
        header("Location: sign_up");
	    exit;
	}	
	
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
    <span id="usrHeader"><a href="sign_in">Login</a></span>
</div>
    
<div id="main">
    <div class="container">
    <?php
        if(!$_SESSION['id']):
    ?>
        <!-- Register Form -->
        <form action="" method="post">
            <h1>Not a member yet? Sign Up!</h1>		
            <input class="field" type="text" name="username" id="username" value="" size="23" placeholder="Username"/><br>
            <input class="field" type="text" name="email" id="email" size="23" placeholder="Email"/><br>
            <input class="field" type="password" name="pass" id="pass" size="23" placeholder="Password"/><br>
            <input class="field" type="password" name="pass_again" id="pass_again" size="23" placeholder="Confirm Password"/><br><br>
            <div class="g-recaptcha" data-sitekey="6LcoYwoUAAAAAPSXMPSxnKOx91BX26ByuDD1NMfA"></div>
            <p class="note">An administrator will be notified of your account and will respond within 48 hours.</p>
            <input type="submit" name="submit" value="Register" class="bt_register" />
        </form>
    <?php
        else:
        
        header("Location: main");
        
	   endif;
    ?>
    </div>
</div>

<?php require 'templates/jsload.php'; ?>

</body>
</html>