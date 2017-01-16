<?php
require 'scripts/header.php';
require 'scripts/functions.php';

$userInfo = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM dewey_members WHERE id='{$_SESSION['id']}'"));

if($_POST['submit']=='Update')
{
    // Checking whether the Update form has been submitted
	
	$err = array();
	// Will hold our errors
    
    if(!$_POST['oldpass'] || !$_POST['newpass'] || !$_POST['newpass_confirm']) 
    {
		$err[] = 'All the fields must be filled in.';
    }
    
    if(strlen($_POST['newpass']) < 7 || strlen($_POST['newpass']) > 32)
	{
		$err[]='Your new password must be between 6 and 32 characters.';
	}
    
    if($_POST['newpass'] != $_POST['newpass_confirm'])
    {
        $err[]='The passwords do not match.';
    }
    
    $row = mysqli_fetch_assoc(mysqli_query($link, "SELECT id,usr,pass FROM dewey_members WHERE id='{$_SESSION['id']}'"));
    
    if($row['pass'] != md5($_POST['oldpass'])) {
        $err[]='Your old password is incorrect.';
    }
    
    if(!count($err))
	{
        $_POST['oldpass'] = mysqli_real_escape_string($link, $_POST['oldpass']);
        $_POST['newpass'] = mysqli_real_escape_string($link, $_POST['newpass']);
        // Escaping all input data
        
        mysqli_query($link, "UPDATE dewey_members SET pass='".md5($_POST['newpass'])."' WHERE id='".$_SESSION['id']."'");
        
        $_SESSION = array();
	    session_destroy();
        
        $_SESSION['msg']['success']='Password successfully updated. You may now login again.';
    }
    
    if(count($err))
	{
		$_SESSION['msg']['err'] = implode('<br />',$err);
	}
}

if($_POST['submit']=='Add')
{
    // Checking whether the Supervisor form has been submitted
	
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
        
        $_SESSION['msg']['success']='Supervisor successfully updated.';
        header("Location: registered");
        exit();
    }
    
    if(count($err))
	{
		$_SESSION['msg']['err'] = implode('<br />',$err);
	}
}

if($_POST['submit']=='Remove Supervisor')
{
    mysqli_query($link, "UPDATE dewey_members SET supervisor='none' WHERE id='".$_SESSION['id']."'");
    $_SESSION['msg']['success']='Supervisor removed.';
    header("Location: registered");
    exit();
}

if($_POST['request']=='Send Request')
{

	$err = array();
	// Will hold our errors
        
    if(!$_POST['explain'])
	{
		$err[]='You must provide an explanation for this request.';
	}
    
    if(!$_POST['privilege'])
	{
		$err[]='You must choose a privilege to request.';
	}
    
    if(!count($err))
	{        
        send_mail('noreply@deweyhaftaacademy.x10host.com',
              'admin@deweyhaftaacademy.x10host.com',
              'User Management - Privilege Change Request',
              $_SESSION['usr'].' has requested for their rights to be changed to '.$_POST['privilege'].'. Their reason for this request is "'.$_POST['explain'].'"'
             );
        
        $_SESSION['msg']['success']='Request sent.';
        header('Location: ../registered');
        exit();
    }
    
    if(count($err))
	{
		$_SESSION['msg']['err'] = implode('<br />',$err);
	}
}
?>

<!DOCTYPE html>
<html>
<head>    
<?php include_once('templates/htmlHeader.php'); ?>
<script src="scripts/typeahead.min.js"></script>

<script>
    function sasstivate() {
        var xhttp = new XMLHttpRequest();
        <?php echo 'xhttp.open("GET", "scripts/update.php?usr='.$_SESSION['id'].'&sass=1", true);'; ?>
        xhttp.send();
    } 
    function deleteAccount() {
        $confirm = confirm("Are you sure you want to delete your account? This cannot be undone.");
            if($confirm === true) {
            var xhttp = new XMLHttpRequest();
            <?php echo 'xhttp.open("GET", "../scripts/delete.php?user='.$_SESSION['id'].'", true);'; ?>
            xhttp.send();
            window.location.assign(<?php echo '"http://www.deweyhaftaacademy.x10host.com/main"'; ?>);
        }
    }
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
    
<div id="header">
    <a href="main" id="title">Dewey Hafta Academy</a>
    <span id="usrHeader">
    <?php if(!$_SESSION['id']): ?>
    <a href="../sign_in">Login</a> - <a href="../sign_up">Sign up</a>
    <?php else: ?>
        <div class="menu-dropdown">
            <ul class="menu nolist">
                <li class="menu-item-has-children" style="text-align:right;"><a href="../profile"><?php echo $_SESSION['usr']; ?></a> <span class="sidebar-menu-arrow"></span>
                    <ul class="sub-menu-dropdown nolist" style="display:none;">
                        <li><a href="?logoff">Logout</a></li>
                    </ul>
                </li>
                </ul>
            </div>
    <?php endif; ?>
</span>
    
</div>
    
<?php include_once("templates/menu.php"); ?>
    
<div id="main">
    <div class="container">
    
    <?php if(!$_SESSION['id']): ?>

        <p>This page is for registered users only. Please, <a href="sign_in">login</a> and come back later.</p>
        
    <?php elseif($_GET['requestchange']): ?>  
        
        <p>Use this form to request a change to your user rights.</p>
        <form action="" method="post">
            <input type='radio' name='privilege' value='unapproved'><label for='unapproved'>Unapproved</label><br>
            <input type='radio' name='privilege' value='student'><label for='student'>Student</label><br>
            <input type='radio' name='privilege' value='teacher'><label for='teacher'>Teacher</label><br>
            <input type='radio' name='privilege' value='admin'><label for='admin'>Administrator</label><br>
            <input type='text' name='explain' style="width:80%;" placeholder='Please provide a brief explanation as to why this should be changed.'><br>
            <input type='submit' name='request' value='Send Request' />
        </form>
        
    <?php else: ?>
        <h1>Preferences</h1>
        <h3>Current account status</h3>
        <i style="font-size:110%;">
        <?php echo $userInfo['privilege']; ?></i> <input type="button" onclick="location.href='?requestchange=1';" value="Request Change" style="margin-left:20px;"/>
        
        <h3>Update Password</h3>
        <form action="" method="post">               
            <input class="field" type="password" name="oldpass" value="" size="23" placeholder="Old Password"/>
            <input class="field" type="password" name="newpass" value="" size="23" placeholder="New Password"/>
            <input class="field" type="password" name="newpass_confirm" value="" size="23" placeholder="Confirm New Password"/>
            <input type="submit" name="submit" value="Update" />
        </form>
        
        <h3>Supervisor</h3>
        <p>Current supervisor: <i>
        <?php 
            $supervisorID = mysqli_fetch_assoc(mysqli_query($link, "SELECT supervisor FROM dewey_members WHERE id='{$_SESSION['id']}'"));
            if($supervisorID['supervisor'] == 0) { 
                echo 'none'; 
            }
            else {
                $supervisorName = mysqli_fetch_assoc(mysqli_query($link, "SELECT usr FROM dewey_members WHERE id='{$supervisorID['supervisor']}'"));
                echo $supervisorName['usr'];
            }
            ?></i></p>
        <form action="" method="post">
            <input type="text" name="supervisor" id="supervisor" class="field typeahead tt-query" autocomplete="off" spellcheck="false" value="" placeholder="Supervisor Username"/>
            <span class='supervisor_buttons'>
                <input type="submit" name="submit" value="Add" class="bt_add" />
                <input type="submit" name="submit" value="Remove Supervisor" />
            </span>
        </form>
        
        <h3>Sass</h3>    
        <div class="onoffswitch">
            <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" <?php if($userInfo['sass'] == 1) echo "checked"; ?>>
            <label class="onoffswitch-label" for="myonoffswitch" onclick="sasstivate();">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
            </label>
        </div>
        
        <h3>Delete Account</h3>
        <p>Do not delete your account unless necessary. It cannot be undone.</p>
        <input type='button' class='warning' onclick='deleteAccount();' name='delete' value='Delete' />
        
    <?php endif; ?>
        
    </div>
</div>

<script src="scripts/typeahead.min.js"></script>
<?php require 'scripts/jsload.php'; ?>
    
    
</body>
</html>
