<?php 

require 'header.php';
require 'functions.php';


if($_POST['action'] == 'login') {

    $username = mysqli_real_escape_string($link, $_POST['username']);
    $password = mysqli_real_escape_string($link, $_POST['password']);
    $remember = (int)$_POST['remember'];

    // Escaping all input data

    $res = mysqli_query($link, "SELECT id,usr,privilege FROM dewey_members WHERE usr='{$username}' AND pass='".md5($password)."'");
    $row = mysqli_fetch_assoc($res);
    
    if(mysqli_num_rows($res) == 1) {
        
        session_start();
        $_SESSION['usr'] = $row['usr'];
        $_SESSION['id'] = $row['id'];
        $_SESSION['privilege'] = $row['privilege'];
        $_SESSION['rememberMe'] = $remember;

        // Store some data in the session

        setcookie('deweyRemember',$remember);
        
        echo "login";
    }
    else {
        echo "error";
    }
}


else if($_POST['action'] == 'signup') {

    $_POST['email'] = mysqli_real_escape_string($link, $_POST['email']);
    $_POST['username'] = mysqli_real_escape_string($link, $_POST['username']);
    $_POST['password'] = mysqli_real_escape_string($link, $_POST['password']);
    $_POST['privilege'] = mysqli_real_escape_string($link, $_POST['privilege']);
    // Escape the input data

    mysqli_query($link, "INSERT INTO dewey_members(usr,email,pass,regIP,dt,privilege)
            VALUES(
            '".$_POST['username']."',
            '".$_POST['email']."',
            '".md5($_POST['password'])."',
            '".$_SERVER['REMOTE_ADDR']."',
            NOW(),
            '".$_POST['privilege']."'
            )");

    send_mail(	'noreply@deweyhaftaacademy.x10host.com',
                $_POST['email'],
                'Dewey Hafta Academy - New Account',
                "Your account has been successfully created, welcome to Dewey Hafta Academy! Please confirm your email by clicking <a href=''>here</a>.");

    $_SESSION['msg']['success']='Welcome to Dewey Hafta Academy! Please use this page to finish setting up your account.';

    $res = mysqli_query($link, "SELECT id,usr,privilege FROM dewey_members WHERE usr='{$_POST['username']}' AND email='".$_POST['email']."'");
    $row = mysqli_fetch_assoc($res);
    
    if(mysqli_num_rows($res) == 1) {
        $_SESSION['usr'] = $row['usr'];
        $_SESSION['id'] = $row['id'];
        $_SESSION['privilege'] = $row['privilege'];
        $_SESSION['signup'] = 1;
    
        echo "signup";
    }
    else {
        echo "error";
    }
}


else if($_POST['action'] == 'checkusername') {
    $_POST['username'] = mysqli_real_escape_string($link, $_POST['username']);
    
    $username = mysqli_num_rows(mysqli_query($link, "SELECT * FROM dewey_members WHERE usr='".$_POST['username']."'"));
    
    if($username > 0) {
        echo "error";
    }
    else {
        echo "good";
    }
}


else if($_POST['action'] == 'checkemail') {
    $_POST['email'] = mysqli_real_escape_string($link, $_POST['email']);
    
    $email = mysqli_num_rows(mysqli_query($link, "SELECT id FROM dewey_members WHERE email='".$_POST['email']."'"));
    
    if($email > 0) {
        echo "error";
    }
    else {
        echo "good";
    }
}

?>