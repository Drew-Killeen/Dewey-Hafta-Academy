<?php 

require 'templates/header.php'; 

if($_POST['submit']=='Submit')
{
    // If the form has been submitted
    
    mysql_query("UPDATE dewey_members SET enrollment='".$_POST['course']."' WHERE id='".$_SESSION['id']."'");

    $_SESSION['msg']['update-success']='Enrollment successfully updated.';
}
?>

<!DOCTYPE html>
<html>
<head>

<?php include_once("templates/htmlHeader.php") ?>

</head>
<body>
    
<div id="header">
    <a href="main" id="title">Dewey Hafta Academy</a>
    
    <?php include_once("templates/login.php"); ?>
</div>
    
<?php include_once("templates/menu.php"); ?>
    
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
        <h2>Course Enrollment</h2>
        <?php
            $current_data = mysql_fetch_assoc(mysql_query("SELECT enrollment FROM dewey_members WHERE id='{$_SESSION['id']}'"));
            if(empty($current_data))
            {
                echo "<p>You are not currently enrolled in any courses. Select one below.</p>";
            } else 
            {
               echo "<p>You are currently enrolled in ".$current_data['enrollment'].".</p>";
            }
        ?>
        
        <p>Choose a course to update your enrollment. You can only be enrolled in one course at a time. To see past enrollments, click <a href=''>here</a>.</p>
        <?php
            if($_SESSION['msg']['update-success'])
            {
                echo '<div class="success">'.$_SESSION['msg']['update-success'].'</div>';
                unset($_SESSION['msg']['update-success']);
            }
        ?>
        <form action="" method="post">
            <?php                    
                $courses = mysql_query ("SELECT course FROM courses WHERE public=1");
                if (mysql_num_rows($courses) > 0) 
                {
                    // output data of each row
                    while($row = mysql_fetch_assoc($courses)) {
                       echo "<input class='field' type='radio' name='course' value='".$row["course"]."'/><label class='grey' for='".$row["course"]."'>".$row["course"]."</label><br>";
                }
                } else 
                {
                    echo "0 results";
                }
            ?>
            <input type="submit" name="submit" value="Submit"/>
        </form>
    <?php
	   endif;
    ?>
    </div>
</div>


</body>
</html>
