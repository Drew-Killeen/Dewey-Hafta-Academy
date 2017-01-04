<?php 

require '../scripts/header.php';

if(isset($_POST['submit']))
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
    
    $current_courses = mysqli_fetch_assoc(mysqli_query($link, "SELECT id,course FROM courses WHERE course='".$_POST['course']."'"));
    
    if(!empty($current_courses))
    {
        $err[]='There is already a course with this title.';
    }
    
    if(!count($err))
	{
        $_POST['course'] = mysqli_real_escape_string($_POST['course']);
        mysqli_query($link, "INSERT INTO courses(course,createdby,dt)
        VALUES(
        '".$_POST['course']."',
        '".$_SESSION['usr']."',
        NOW()							
        )");
        
        $_SESSION['msg']['success']='Course successfully created.';
        header("Location: create_exam?".$_POST['course']);
	    exit;
    }
    else $err[]='Error creating course.';
    
    if(count($err))
	{
		$_SESSION['msg']['err'] = implode('<br />',$err);
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
    
<?php 
    include_once("../templates/login.php"); 
    include_once("../templates/menu.php"); 
?>
    
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
            <label class="grey" for="course">Course Name:</label>
            <input class="field" type="text" name="course" id="course" value="" size="23"/>
            <input type="submit" name="submit" value="&#10004;"/>
        </form>
        
    <?php
	   endif;
    ?>
    </div>
</div>

    <?php require '../scripts/jsload.php'; ?>

</body>
</html>