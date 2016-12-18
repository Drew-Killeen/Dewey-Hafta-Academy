<?php 

require '../scripts/header.php'; 
require '../scripts/functions.php';

if($_POST['update']=='Update')
{
    // If the form has been submitted
    
    mysql_query("UPDATE dewey_members SET privilege='".$_POST['privilege']."' WHERE usr='".$_GET['user']."'");
    
    $userData = mysql_fetch_assoc(mysql_query("SELECT * FROM dewey_members WHERE usr='".$_GET['user']."'"));
    
    if($userData['privilege'] == 'unapproved') {    
    send_mail('noreply@deweyhaftaacademy.x10host.com',
              $userData['email'],
              'Registration System - Your Password',
              "Your account has been approved. You can now use your account to enroll in a course."
             );
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
    
<?php include_once("../templates/menu.php"); ?>
    
<div id="main">
    <div class="container">
    <?php
        if(!$_SESSION['id'] || ($_SESSION['privilege'] != admin && $_SESSION['privilege'] != sysop)):
    ?>
        <!--Unauthorized-->
        <p>You do not have sufficient privileges to access this page.</p>
    <?php
        else:
            echo "<h1>Administrator Control Panel</h1>";
            if(!$_GET['user']):
    ?>
        <!--Authorized-->
        <h3>Unapproved Users</h3>
        <?php
        $unapproved_users = mysql_query("SELECT id,usr FROM dewey_members WHERE privilege='unapproved'");
        if (mysql_num_rows($unapproved_users) > 0) 
        {
            // output data of each row
            while($row = mysql_fetch_assoc($unapproved_users)) {
                echo "<li><a href='users?user=".$row['usr']."'>".$row['usr']."</a></li>";
            }
        } else 
        {
            echo "0 results";
        }
        ?>
        <h3>Students</h3>
        <?php
        $students = mysql_query("SELECT usr FROM dewey_members WHERE privilege='student'");
        if (mysql_num_rows($students) > 0) 
        {
            echo "<ul>";
            // output data of each row
            while($row = mysql_fetch_assoc($students)) {
                echo "<li><a href='users?user=".$row['usr']."'>".$row['usr']."</a></li>";
            }
            echo "</ul>";
        } else 
        {
            echo "0 results";
        }
        ?>
        <h3>Teachers</h3>
        <?php
        $teachers = mysql_query("SELECT usr FROM dewey_members WHERE privilege='teacher'");
        if (mysql_num_rows($teachers) > 0) 
        {
            echo "<ul>";
            // output data of each row
            while($row = mysql_fetch_assoc($teachers)) {
                echo "<li><a href='users?user=".$row['usr']."'>".$row['usr']."</a></li>";
            }
            echo "</ul>";
        } else 
        {
            echo "0 results";
        }
        ?>
        <h3>Administrators</h3>
        <?php
        $admins = mysql_query("SELECT usr FROM dewey_members WHERE privilege='admin'");
        if (mysql_num_rows($admins) > 0) 
        {
            echo "<ul>";
            // output data of each row
            while($row = mysql_fetch_assoc($admins)) {
                echo "<li><a href='users?user=".$row['usr']."'>".$row['usr']."</a></li>";
            }
            echo "</ul>";
        } else 
        {
            echo "0 results";
        }
        ?>
        <h3>Sysops</h3>
        <?php
        $sysops = mysql_query("SELECT usr FROM dewey_members WHERE privilege='sysop'");
        if (mysql_num_rows($sysops) > 0) 
        {
            echo "<ul>";
            // output data of each row
            while($row = mysql_fetch_assoc($sysops)) {
                echo "<li><a href='users?user=".$row['usr']."'>".$row['usr']."</a></li>";
            }
            echo "</ul>";
        } else 
        {
            echo "0 results";
        }
        
            else:
                $user = mysql_fetch_assoc(mysql_query("SELECT * FROM dewey_members WHERE usr='".$_GET['user']."'"));
                echo 
                    "<h3><a href='users'>&larr;</a> Editing user: ".$_GET['user']."</h3>
                    <p>Member since ".$user['dt'].", id # ".$user['id'].". Currently enrolled in ".$user['enrollment'].". Email address: ".$user['email'].".</p>
                    <form action='' method='post'>
                        <input type='radio' name='privilege' value='unapproved'><label for='unapproved'>Unapproved</label></input><br>
                        <input type='radio' name='privilege' value='student'><label for='student'>Student</label></input><br>
                        <input type='radio' name='privilege' value='teacher'><label for='teacher'>Teacher</label></input><br>
                        <input type='radio' name='privilege' value='admin'><label for='admin'>Administrator</label></input><br>
                        <input type='radio' name='privilege' value='sysop'><label for='sysop'>Sysop</label></input><br>
                        <input type='submit' name='update' value='Update' />
                    </form>";
            endif;
        endif; 
    ?>
    </div>
</div>

<?php require '../scripts/jsload.php'; ?>
    
</body>
</html>