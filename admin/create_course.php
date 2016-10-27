<?php 

require '../templates/header.php';

if($_POST['submit']=='Submit')
{
    // If the form has been submitted
    
    $err = array();
	
	if(strlen($_POST['course'])<1 || strlen($_POST['course'])>32)
	{
		$err[]='The course name must be between 1 and 32 characters.';
	}
	
	if(preg_match('/[^a-z0-9\s]/i',$_POST['course']))
	{
		$err[]='The course name contains invalid characters.';
	}
    
    $current_courses = mysql_fetch_assoc(mysql_query("SELECT id,course FROM courses WHERE course='".$_POST['course']."'"));
    
    if(!empty($current_courses))
    {
        $err[]='There is already a course with this title.';
    }
    
    if(!count($err))
	{
        mysql_query("INSERT INTO courses(course,createdby,dt)
        VALUES(
        '".$_POST['course']."',
        '".$_SESSION['usr']."',
        NOW()							
        )");
        
        mysql_query("ALTER TABLE dewey_members ADD ".$_POST['course']." tinyint(1) NOT NULL default '0'");
        
        $_SESSION['msg']['create-success']='Course successfully created.';
        header("Location: create_exam?".$_POST['course']);
	    exit;
    }
    else $err[]='Error creating course.';
    
    if(count($err))
	{
		$_SESSION['msg']['create-err'] = implode('<br />',$err);
        header("Location: create_course");
	    exit;
	}
}
?>

<!DOCTYPE html>
<html>
<head>

<?php include_once("../templates/htmlHeader.php") ?>
</head>
<body>
    
<div id="header">
    <a href="../main" id="title">Dewey Hafta Academy</a>
    <?php include_once("../templates/login.php"); ?>
</div>
    
<?php include_once("../templates/subfolder_menu.php"); ?>
    
<div id="main">
    <div class="container">
    <?php
        if(!$_SESSION['id'] || ($_SESSION['privilege'] != admin && $_SESSION['privilege'] != sysop)):
    ?>
        <!--Unauthorized-->
        <p>You do not have sufficient privileges to access this page.</p>
    <?php
        else:
    ?>
        <!--Authorized-->
        <h1>Administrator Control Panel</h1>
        <p>You can use this page to create a new course. Please only create the course if you intend to immediately begin creating exams. The less empty courses lying around the better.</p>
        <form action="" method="post">
                <?php
						
                if($_SESSION['msg']['create-err'])
				{
				    echo '<div class="err">'.$_SESSION['msg']['create-err'].'</div>';
				    unset($_SESSION['msg']['create-err']);
				}
						
				if($_SESSION['msg']['create-success'])
				{
					echo '<div class="success">'.$_SESSION['msg']['create-success'].'</div>';
					unset($_SESSION['msg']['create-success']);
				}
				?>
            <label class="grey" for="course">Course Name:</label>
            <input class="field" type="text" name="course" id="course" value="" size="23"/>
            <input type="submit" name="submit" value="Submit"/>
        </form>
        
    <?php
	   endif;
    ?>
    </div>
</div>


</body>
</html>