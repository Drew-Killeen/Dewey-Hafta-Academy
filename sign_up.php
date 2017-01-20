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
        if(!$_POST['privilege'] || ($_POST['privilege'] != 'student' && $_POST['privilege'] != 'teacher')) {
            $err[]='Please select whether you plan to use this site as a student or teacher.';
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
        $_POST['privilege'] = mysqli_real_escape_string($link, $_POST['privilege']);
		// Escape the input data
		
		mysqli_query($link, "INSERT INTO dewey_members(usr,email,pass,regIP,dt,privilege)
				VALUES(
				'".$_POST['username']."',
				'".$_POST['email']."',
                '".md5($_POST['pass'])."',
				'".$_SERVER['REMOTE_ADDR']."',
				NOW(),
                '".$_POST['privilege']."'
                )");
        
			send_mail(	'noreply@deweyhaftaacademy.x10host.com',
						'admin@deweyhaftaacademy.x10host.com',
						'Registration System - New Account',
						'A new account was registered at '.date("Y-m-d h:i:sa").' from IP '.$_SERVER['REMOTE_ADDR'].'. Email address '.$_POST['email'].', username '.$_POST['username'].'.');
            send_mail(	'noreply@deweyhaftaacademy.x10host.com',
						$_POST['email'],
						'Dewey Hafta Academy - New Account',
						"Your account has been successfully created, welcome to Dewey Hafta Academy! Please confirm your email by clicking <a href=''>here</a>.");
            
			$_SESSION['msg']['success']='Welcome to Dewey Hafta Academy! Visit your <a href="../registered">preferences</a> to complete your account.';
            
            $row = mysqli_fetch_assoc(mysqli_query($link, "SELECT id,usr,privilege FROM dewey_members WHERE usr='{$_POST['username']}' AND email='".$_POST['email']."'"));
            $_SESSION['usr'] = $row['usr'];
			$_SESSION['id'] = $row['id'];
            $_SESSION['privilege'] = $row['privilege'];
            $_SESSION['signup'] = 1;
	}

	if(count($err))
	{
		$_SESSION['msg']['err'] = implode('<br />',$err);
	}	
	
    header("Location: sign_up");
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    
<?php include_once('templates/htmlHeader.php'); ?>
</head>
<body>

<div id="header"><a href="main" id="title">Dewey Hafta Academy</a><span id="usrHeader"><a href="sign_in">Login</a></span></div>
    
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
            <span id='privilege'>
                <p>I plan to use this site as a...</p>
                <label><input type='radio' name='privilege' value='student'>Student</label><br>
                <label><input type='radio' name='privilege' value='teacher'>Teacher</label><br>
            </span>
            <br><br>
            <div class="g-recaptcha" data-sitekey="6Lf2VQwUAAAAAB6g1e1p0DrjETPHCcrGGfCUe7I2"></div><br>
            <input type="submit" name="submit" value="Register" class="bt_register" />
        </form>
        
    <?php
        else:
        
        header("Location: main");
        exit();
        
	   endif;
    ?>
    </div>
</div>

<?php require 'scripts/jsload.php'; ?>
    
    <!--<script>
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
    </script>-->

</body>
</html>