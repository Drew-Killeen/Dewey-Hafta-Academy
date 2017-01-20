<?php

require 'scripts/header.php';
require 'scripts/functions.php';

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
            $response=file_get_contents();
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
		
		$_POST['email'] = mysqli_real_escape_string($link, $_POST['email']);
		$_POST['username'] = mysqli_real_escape_string($link, $_POST['username']);
        $_POST['pass'] = mysqli_real_escape_string($link, $_POST['pass']);
		// Escape the input data
		
		mysqli_query($link, "INSERT INTO dewey_members(usr,email,pass,regIP,dt)
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
            
            $row = mysqli_fetch_assoc(mysqli_query($link, "SELECT id,usr,privilege FROM dewey_members WHERE usr='{$_POST['username']}' AND email='".$_POST['email']."'"));
            $_SESSION['usr'] = $row['usr'];
			$_SESSION['id'] = $row['id'];
            $_SESSION['privilege'] = $row['privilege'];
            $_SESSION['signup'] = 1;
	}

	if(count($err))
	{
		$_SESSION['msg']['err'] = implode('<br />',$err);
        header("Location: sign_up");
	    exit;
	}	
	
    else 
    {
	   header("Location: sign_up");
	   exit;
    }
}

if($_POST['submit']=='Submit') {
    if($_POST['privilege']) {
        $_POST['privilege'] = mysqli_real_escape_string($link, $_POST['privilege']);
        mysqli_query($link, "UPDATE dewey_members SET privilege='".$_POST['privilege']."' WHERE id=".$_SESSION['id']);
        $_SESSION['privilege'] = $_POST['privilege'];
    }
    if($_POST['supervisor']) {
        $err = array();
        // Will hold our errors
        $teacher = mysqli_fetch_assoc(mysqli_query($link, "SELECT id,privilege FROM dewey_members WHERE usr='{$_POST['supervisor']}'"));

        if(!$_POST['supervisor']) {
            $err[] = 'Field must be filled in.';
        }
        else if($_SESSION['usr'] == $_POST['supervisor']) {
            $err[]='You cannot assign yourself as your own supervisor.';
        }

        else if(strlen($_POST['supervisor']) < 4 || strlen($_POST['supervisor']) > 32) {
            $err[]='The Supervisor username must be between 3 and 32 characters.';
        } 

        else if(empty($teacher['id'])) {
            $err[]='There is no user with the name '.$_POST['supervisor'].'.';
        }

        else if($teacher['privilege'] != 'teacher' && $teacher['privilege'] != 'admin' && $teacher['privilege'] != 'sysop') {
            $err[]=$_POST['supervisor'].' does not have sufficient privileges to be a supervisor.';
        }

        if(!count($err)) {
            $supervisor = mysqli_real_escape_string($link, $_POST['supervisor']);
            // Escaping all input data

            $supervisorID = mysqli_fetch_assoc(mysqli_query($link, "SELECT id FROM dewey_members WHERE usr='".$supervisor."'"));
            mysqli_query($link, "UPDATE dewey_members SET supervisor='".$supervisorID['id']."' WHERE id=".$_SESSION['id']);
        }
    }
    if(count($err))
    {
        $_SESSION['msg']['err'] = implode('<br />',$err);
    }
    else {
        $_SESSION['msg']['success'] = "Preferences updated";
        unset($_SESSION['signup']);
        header("Location: main");
        exit();
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    
<?php include_once('templates/htmlHeader.php'); ?>
<script src="scripts/typeahead.min.js"></script>
<script>
    $(document).ready(function(){
        $('input.typeahead').typeahead({
            name: 'typeahead',
            remote:'scripts/search.php?key=%QUERY',
            limit : 8
        });
    });
</script>
</head>
<body>

<?php 
    if($_SESSION['signup'] == 1) {
        include_once("templates/login.php");
    }
    else { 
        echo '<div id="header"><a href="main" id="title">Dewey Hafta Academy</a><span id="usrHeader"><a href="sign_in">Login</a></span></div>';
    }
?>
    
<div id="main">
    <div class="container">
    <?php
        if(!$_SESSION['id']):
    ?>
        <!-- Register Form -->
        <h1>Not a member yet? Sign Up!</h1>	
        <form action="" method="post">
            <input class="field" type="text" name="username" id="username" value="" size="23" placeholder="Username"/><br>
            <input class="field" type="text" name="email" id="email" size="23" placeholder="Email"/><br>
            <input class="field" type="password" name="pass" id="pass" size="23" placeholder="Password"/><br>
            <input class="field" type="password" name="pass_again" id="pass_again" size="23" placeholder="Confirm Password"/><br><br>
            <div class="g-recaptcha" data-sitekey="6Lf2VQwUAAAAAB6g1e1p0DrjETPHCcrGGfCUe7I2"></div>
            <p class="note">An administrator will be notified of your account and will respond within 48 hours.</p>
            <input type="submit" name="submit" value="Register" class="bt_register" />
        </form>
        
    <?php elseif($_SESSION['signup'] == 1): ?>
        <h1>Not a member yet? Sign Up!</h1>	
        <p>Please complete the following steps to finish your account.</p>
        
        <p>I am a...</p>
        <form action="" method="post">
            <span id='privilege'>
                <label><input type='radio' name='privilege' value='student'>Student</label><br>
                <label><input type='radio' name='privilege' value='teacher'>Teacher</label><br>
            </span>
            <br>
            <input type="text" name="supervisor" id="supervisor" class="field typeahead tt-query nodisplay" autocomplete="off" spellcheck="false" value="" placeholder="Supervisor Username"/><br>
            <input type='submit' name='submit' value='Submit' />
        </form>
    <?php
        else:
        
        header("Location: main");
        
	   endif;
    ?>
    </div>
</div>

<?php require 'scripts/jsload.php'; ?>
    
    <script>
    document.getElementById("privilege").addEventListener("click", function() {
        if($('input[name="privilege"]:checked').val() == "student") {
            document.getElementById("supervisor").classList.remove("nodisplay");
            console.log('display');
        }
        else if($('input[name="privilege"]:checked').val() == "teacher") {
            document.getElementById("supervisor").classList.add("nodisplay");
            console.log('nodisplay');
        }
    });
    </script>

</body>
</html>