<?php 

require '../templates/header.php'; 
require '../templates/functions.php';

if($_POST['approve']=='Approve')
{
    // If the form has been submitted
    
    $pass = substr(md5($_SERVER['REMOTE_ADDR'].microtime().rand(1,100000)),0,6);
    // Generate a random password
    
    mysql_query("UPDATE dewey_members SET pass='".md5($pass)."', privilege='student' WHERE id='".$_POST['user']."'");
    
    $email = mysql_fetch_assoc(mysql_query("SELECT email FROM dewey_members WHERE id='".$_POST['user']."'"));
    send_mail('noreply@deweyhaftaacademy.x10host.com',
              $email['email'],
              'Registration System - Your Password',
              "Your account has been approved. Your password is ".$pass.". You can now log into your account where you can change your password."
             );
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
        <h3>Unapproved Users</h3>
        <?php
        $unapproved_users = mysql_query("SELECT id,usr FROM dewey_members WHERE privilege='unapproved'");
        if (mysql_num_rows($unapproved_users) > 0) 
        {
            echo "<form action='' method='post'>";
            // output data of each row
            while($row = mysql_fetch_assoc($unapproved_users)) {
                echo "<input class='field' type='radio' name='user' value='".$row["id"]."'/><label class='grey' for='".$row["id"]."'>".$row["usr"]."</label><br>";
            }
            echo '<input type="submit" name="approve" value="Approve"/></form>';
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
                echo "<li>".$row['usr']."</li><br>";
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
                echo "<li>".$row['usr']."</li><br>";
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
                echo "<li>".$row['usr']."</li><br>";
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
                echo "<li>".$row['usr']."</li><br>";
            }
            echo "</ul>";
        } else 
        {
            echo "0 results";
        }
        endif; 
    ?>
    </div>
</div>


</body>
</html>