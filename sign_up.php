<?php

require 'templates/header.php';
require 'templates/functions.php';

if($_POST['submit']=='Register')
{
	// If the Register form has been submitted
	
	$err = array();
	
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
	
	if(!count($err))
	{
		// If there are no errors
		
		$_POST['email'] = mysql_real_escape_string($_POST['email']);
		$_POST['username'] = mysql_real_escape_string($_POST['username']);
		// Escape the input data
		
		mysql_query("INSERT INTO dewey_members(usr,email,regIP,dt)
				VALUES(
				'".$_POST['username']."',
				'".$_POST['email']."',
				'".$_SERVER['REMOTE_ADDR']."',
				NOW()
                )");
		
		if(mysql_affected_rows($link)==1)
		{
			send_mail(	'noreply@deweyhaftaacademy.x10host.com',
						'admin@deweyhaftaacademy.x10host.com',
						'Registration System - New Account',
						'A new account was registered at '.date("Y-m-d h:i:sa").' from IP '.$_SERVER['REMOTE_ADDR'].'. Email address '.$_POST['email'].', username '.$_POST['username'].'.');
            send_mail(	'noreply@deweyhaftaacademy.x10host.com',
						$_POST['email'],
						'Registration System - New Account',
						"Your account has been successfully created. An administrator must approve your account before you may login. If you do not receive an email confirming your account's approval or denial, please contact admin@deweyhaftaacademy.x10host.com.");

			$_SESSION['msg']['reg-success']='An administrator has been notified and will approve your account shortly.';
		}
		else $err[]='This username is already taken.';
	}

	if(count($err))
	{
		$_SESSION['msg']['reg-err'] = implode('<br />',$err);
        header("Location: sign_in");
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
</div>
    
<div id="main">
    <div class="container">
    <?php
        if(!$_SESSION['id']):
    ?>
        <!-- Register Form -->
        <form action="" method="post">
            <h1>Not a member yet? Sign Up!</h1>		

            <?php

                if($_SESSION['msg']['reg-err'])
                {
                    echo '<div class="err">'.$_SESSION['msg']['reg-err'].'</div>';
                    unset($_SESSION['msg']['reg-err']);
                }

                if($_SESSION['msg']['reg-success'])
                {
                    echo '<div class="success">'.$_SESSION['msg']['reg-success'].'</div>';
                    unset($_SESSION['msg']['reg-success']);
                }
            ?>

            <label class="grey" for="username">Username:</label>
            <input class="field" type="text" name="username" id="username" value="" size="23" />
            <label class="grey" for="email">Email:</label>
            <input class="field" type="text" name="email" id="email" size="23" />
            <label>An administrator will be notified of your account and will respond within 48 hours.</label>
            <input type="submit" name="submit" value="Register" class="bt_register" />
        </form>
    <?php
        else:
        
        header("Location: main");
        
	   endif;
    ?>
    </div>
</div>

</body>
</html>