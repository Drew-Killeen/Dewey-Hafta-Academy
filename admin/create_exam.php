<?php 

require '../templates/header.php'; 

if($_POST['submit']=='Submit')
{
    // If the form has been submitted
    
    $err = array();
	
	if(strlen($_POST['title'])<4 || strlen($_POST['title'])>32)
	{
		$err[]='The exam title must be between 4 and 32 characters.';
	}
    
    if(strlen($_POST['module'])<1 || strlen($_POST['module'])>2)
	{
		$err[]='The module must be a one or two digit integer.';
	}
	
	if(preg_match('/[^a-z0-9\-\_\.]+/i',$_POST['exam']))
	{
		$err[]='The exam title contains invalid characters.';
	}
    
    $current_courses = mysql_fetch_assoc(mysql_query("SELECT id,module FROM exams WHERE course='".$_POST['course']."' AND module='".$_POST['module']."'"));
    
    if(!empty($current_courses))
    {
        $err[]='You are trying to create a duplicate exam.';
    }
    
    if(!count($err))
	{
        $_POST['module'] = mysql_real_escape_string($_POST['module']);
        $_POST['title'] = mysql_real_escape_string($_POST['title']);
        mysql_query("INSERT INTO exams(course,module,title,createdby,dt)
            VALUES(
            '".$_POST['course']."',
            '".$_POST['module']."',
            '".$_POST['title']."',
            '".$_SESSION['usr']."',
            NOW()
        )");
        $_SESSION['msg']['create-success']='Exam successfully created.';
        $examNum = mysql_fetch_assoc(mysql_query("SELECT id FROM exams ORDER BY id DESC LIMIT 1;"));
        header("Location: edit_exam?exam=".$examNum['id']);
    }
    else $err[]='Error creating exam.';
    
    if(count($err))
	{
		$_SESSION['msg']['create-err'] = implode('<br />',$err);
        header("Location: create_exam");
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
        <p>You can use this page to create new exams for any of the courses available. As with the courses, please do not create an exam unless you intend to immediately begin filling it with questions.</p>
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
        <form action="" method="post">
            <label class="grey" for="course">Select Course</label><br>
            <?php
            $courses = mysql_query ("SELECT course FROM courses");
            if (mysql_num_rows($courses) > 0) 
            {
                // output data of each row
                while($row = mysql_fetch_assoc($courses)) {
                    if(isset($_GET[$row['course']])) echo "<input class='field' type='radio' name='course' value='".$row["course"]."' checked/><label class='grey' for='".$row["course"]."'>".$row["course"]."</label><br>";
                    else echo "<input class='field' type='radio' name='course' value='".$row["course"]."'/><label class='grey' for='".$row["course"]."'>".$row["course"]."</label><br>";
            }
            } else 
            {
                echo "0 results";
            }
            ?>
            <label class="grey" for="course">Exam module:</label><input class="field" type="text" name="module" value="" size="5" /><br>
            <label class="grey" for="course">Exam title:</label><input class="field" type="text" name="title" value="" size="23" /><br>
            <input type="submit" name="submit" value="Submit"/>
        </form>
        
        
    <?php
	   endif;
    ?>
    </div>
</div>


</body>
</html>