<?php 

require 'scripts/header.php'; 

if($_POST['submit']=='Update Info')
{
    // Checking whether the Supervisor form has been submitted
    
	$_POST['name'] = mysqli_real_escape_string($_POST['name']);
    $_POST['grade'] = mysqli_real_escape_string($_POST['grade']);
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
        
        $_SESSION['msg']['success']='Information successfully updated.';
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

<?php include_once("templates/htmlHeader.php") ?>

</head>
<body>
    
<?php 
    include_once("templates/login.php"); 
    include_once("templates/menu.php"); 
?>
    
<div id="main">
    <div class="container">
    <?php
        if(!$_SESSION['id']):
    ?>
        <!--Unauthorized-->
        <p>This site is for members only. Click <a href="sign_in">here</a> to login. To apply for a membership, please visit the <a href="sign_up">sign up</a> page.</p>
    <?php
        else:
    ?>
        <!--Authorized-->
        <p>--placeholder for profiles--</p>
        
        <?php if($_GET['edit']==1): ?>
         <form action="" method="post">
            <h3>Personal Info</h3>
            <input class="field" type="text" name="name" value="" size="23" placeholder="Full Name" />
            <input class="field" type="text" name="grade" value="" size="23" placeholder="Grade"/>
            <input type="submit" name="submit" value="Update Info" />
        </form>
        <?php endif; ?>
        
    <?php
	   endif;
    ?>
    </div>
</div>
    
<?php require 'scripts/jsload.php'; ?>

</body>
</html>
