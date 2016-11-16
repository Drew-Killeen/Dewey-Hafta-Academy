<?php
require 'templates/header.php';

$userInfo = mysql_fetch_assoc(mysql_query("SELECT * FROM dewey_members WHERE id='{$_SESSION['id']}'"));

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
    
    $row = mysql_fetch_assoc(mysql_query("SELECT id,usr,pass FROM dewey_members WHERE id='{$_SESSION['id']}'"));
    
    if($row['pass'] != md5($_POST['oldpass'])) {
        $err[]='Your old password is incorrect.';
    }
    
    if(!count($err))
	{
        $_POST['oldpass'] = mysql_real_escape_string($_POST['oldpass']);
        $_POST['newpass'] = mysql_real_escape_string($_POST['newpass']);
        // Escaping all input data
        
        mysql_query("UPDATE dewey_members SET pass='".md5($_POST['newpass'])."' WHERE id='".$_SESSION['id']."'");
        
        $_SESSION = array();
	    session_destroy();
        
        $_SESSION['msg']['update-success']='Password successfully updated. You may now login again.';
    }
    else $err[]='Error updating password.';
    
    if(count($err))
	{
		$_SESSION['msg']['update-err'] = implode('<br />',$err);
	}
}

if($_POST['submit']=='Add')
{
    // Checking whether the Supervisor form has been submitted
	
	$err = array();
	// Will hold our errors
    
    if(!$_POST['supervisor']) 
    {
		$err[] = 'Field must be filled in.';
    }
    
    else if(strlen($_POST['supervisor']) < 4 || strlen($_POST['supervisor']) > 32)
	{
		$err[]='The Supervisor username must be between 3 and 32 characters.';
	} 
    
    else if(empty(mysql_fetch_assoc(mysql_query("SELECT id,usr FROM dewey_members WHERE usr='{$_POST['supervisor']}'")))) {
        $err[]='There is no user with the name '.$_POST['supervisor'].'.';
    }
    
    if($_SESSION['usr'] == $_POST['supervisor']) {
        $err[]='You cannot assign yourself as your own supervisor.';
    }
    
    if(!count($err))
	{
        $_POST['supervisor'] = mysql_real_escape_string($_POST['supervisor']);
        // Escaping all input data
        
        mysql_query("UPDATE dewey_members SET supervisor='".$_POST['supervisor']."' WHERE id='".$_SESSION['id']."'");
        
        $_SESSION['msg']['supervisor-success']='Supervisor successfully updated.';
    }
    else $err[]='Error updating supervisor.';
    
    if(count($err))
	{
		$_SESSION['msg']['supervisor-err'] = implode('<br />',$err);
	}
}

if($_POST['submit']=='Remove Supervisor')
{
    mysql_query("UPDATE dewey_members SET supervisor='none' WHERE id='".$_SESSION['id']."'");
}

if($_POST['submit']=='Update Info')
{
    // Checking whether the Supervisor form has been submitted
	
    $grade = preg_replace("/[^0-9]/", "", $_POST['grade']);
	$err = array();
	// Will hold our errors
        
    if($grade < 1 || $grade > 12)
	{
		$err[]='You can only be in grades 1-12.';
	}
    
    if(!count($err))
	{        
        mysql_query("UPDATE dewey_members SET grade='".$grade."', name=".$_POST['name']." WHERE id='".$_SESSION['id']."'");
        
        $_SESSION['msg']['info-success']='Supervisor successfully updated.';
    }
    else $err[]='Error updating info.';
    
    if(count($err))
	{
		$_SESSION['msg']['info-err'] = implode('<br />',$err);
	}
}
?>

<!DOCTYPE html>
<html>
<head>    
<?php include_once('templates/htmlHeader.php'); ?>
<script>    
    function sasstivate() {
        var xhttp = new XMLHttpRequest();
        <?php echo 'xhttp.open("GET", "scripts/update.php?usr='.$_SESSION['id'].'&sass=1", true);'; ?>
        xhttp.send();
        console.log("sass1");
    }
</script>
    
</head>
<body>
    
<div id="header">
    <a href="main" id="title">Dewey Hafta Academy</a>
    <?php if($_SESSION['id']): ?>
    <span id="usrHeader"><a href="?logoff">Logout</a></span>
    <?php endif; ?>
    
</div>
    
<?php include_once("templates/menu.php"); ?>
    
<div id="main">
    <div class="container">
    
    <?php if(!$_SESSION['id']): ?>

        <p>This page is for registered users only. Please, <a href="sign_in">login</a> and come back later.</p>
        
    <?php else: ?>
        <h1>Preferences</h1>
        <?php
            if($_SESSION['msg']['update-success'])
            {
                echo '<div class="success">'.$_SESSION['msg']['update-success'].'</div>';
                unset($_SESSION['msg']['update-success']);
            }
        ?>
        <h3>Current account status</h3>
        <i style="font-size:110%;">
        <?php echo $userInfo['privilege']; ?></i> <input type="button" onclick="location.href='?requestchange';" value="Request Change" style="margin-left:20px;"/>
        
        <h3>Update Password</h3>
        <form action="" method="post">               
            <?php
				if($_SESSION['msg']['update-err'])
				{
				    echo '<div class="err">'.$_SESSION['msg']['update-err'].'</div>';
				    unset($_SESSION['msg']['update-err']);
				}
            ?>
            <input class="field" type="password" name="oldpass" value="" size="23" placeholder="Old Password"/>
            <input class="field" type="password" name="newpass" value="" size="23" placeholder="New Password"/>
            <input class="field" type="password" name="newpass_confirm" value="" size="23" placeholder="Confirm New Password"/>
            <input type="submit" name="submit" value="Update" />
        </form>
        
        <h3>Add Supervisor</h3>
        <p>Current supervisor: <i>
        <?php 
            $current_data = mysql_fetch_assoc(mysql_query("SELECT supervisor FROM dewey_members WHERE id='{$_SESSION['id']}'"));
            echo $current_data['supervisor'];
            ?></i></p>
        <form action="" method="post">
            <?php
				if($_SESSION['msg']['supervisor-err'])
				{
				    echo '<div class="err">'.$_SESSION['msg']['supervisor-err'].'</div>';
				    unset($_SESSION['msg']['supervisor-err']);
				}
            ?>
            <input class="field" type="text" name="supervisor" id="supervisor" value="" size="23" placeholder="Supervisor Username"/>
            <input type="submit" name="submit" value="Add" class="bt_add" />
            <input type="submit" name="submit" value="Remove Supervisor" />
        </form>
        
        <form action="" method="post">
            <?php
				if($_SESSION['msg']['info-err'])
				{
				    echo '<div class="err">'.$_SESSION['msg']['info-err'].'</div>';
				    unset($_SESSION['msg']['info-err']);
				}
            ?>
            <h3>Personal Info</h3>
            <input class="field" type="text" name="name" value="" size="23" placeholder="Full Name" />
            <input class="field" type="text" name="grade" value="" size="23" placeholder="Grade"/>
            <input type="submit" name="submit" value="Update Info" />
        </form>
        
        <h3>Sass</h3>    
        <div class="onoffswitch">
            <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" <?php if($userInfo['sass'] == 1) echo "checked"; ?>>
            <label class="onoffswitch-label" for="myonoffswitch" onclick="sasstivate();">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
            </label>
        </div>

        
    <?php endif; ?>
        
    </div>
</div>


</body>
</html>
