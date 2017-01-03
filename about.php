<?php 

    require 'scripts/header.php'; 

    if(!$_GET['article'] && $_GET['action'] != 'new') {
        header('Location: ../about?article=1');
    }
    if($_POST['submit'] == 'Submit') {
        mysql_query("UPDATE help SET content='".$_POST['textarea']."', public=".$_POST['public']." WHERE id=".$_GET['article']);
        header('Location: ?article='.$_GET['article']);
        exit();
    }
?>

<!DOCTYPE html>
<html>
<head>

<?php if($_GET['action'] != "edit") include_once("templates/htmlHeader.php");
    else echo "<script src='//cdn.tinymce.com/4/tinymce.min.js'></script>";
?>
    
<script>
  tinymce.init({
    selector: '#textarea',
    plugins: "code",
    toolbar: "code",
  });
</script>
    
</head>

<?php if($_GET['action'] != "edit"): ?>  

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
        <p>This site is for members only. Click <a href="../sign_in">here</a> to login. To apply for a membership, please visit the <a href="../sign_up">sign up</a> page.</p>
    <?php
        else:
    ?>
        <!--Authorized-->
    <?php
        if(($_SESSION['privilege'] == 'administrator' || $_SESSION['privilege'] == 'sysop') && $_GET['action'] != "edit") {
            echo "<div class='helpEdit'><input type='button' value='Edit' onclick='window.location.assign(\"../about?article=".$_GET['article']."&action=edit\")'/> <input type='button' value='New' onclick='window.location.assign(\"../about?action=new\")'/></div>";
        }
        if($_GET['article'] == 1) {
            $content = mysql_fetch_assoc(mysql_query("SELECT title,content FROM help WHERE id=1"));
            echo "<p>".$content['content']."</p>";
        
            $articles = mysql_query("SELECT id,title FROM help WHERE public=1");
            echo "<ul>";
            while($row = mysql_fetch_assoc($articles)) {
                echo "<li><a href='?article=".$row['id']."'>".$row['title']."</a></li>";
            }
            echo "</ul>";
        }
        else {
            $content = mysql_fetch_assoc(mysql_query("SELECT title,content FROM help WHERE id=".$_GET['article']));
            echo "<p>".$content['content']."</p>";
        }
    ?>
    </div>
</div>
    
<?php endif; require 'scripts/jsload.php'; ?>

</body>
    
<?php 
    else: 
    include_once("templates/htmlHeader.php");
?>
    
    
<body>
    
<?php include_once("templates/login.php");  ?>
    
    <div class='editor-container'>
    <?php
        /*if($_GET['action'] && ($_SESSION['privilege'] != 'administrator' || $_SESSION['privilege'] != 'sysop')) {
        $_SESSION['msg']['err'] = "You do not have sufficient privileges to perform this action.";
        header('Location: ../about?article=1');
        exit();
        }
        else*/ if($_GET['action'] == 'new') {

        }
        else if($_GET['action'] == 'edit') {
            $content = mysql_fetch_assoc(mysql_query("SELECT title,content,public FROM help WHERE id=".$_GET['article']));
            echo 
                "<form method='post'>
                    <textarea id='textarea' name='textarea'><p>".$content['content']."</p></textarea>
                    <b>Public:</b>
                    <input type='radio' name='public' value='1' ";
                    if($content['public'] == 1) echo "checked";
                    echo "/><label for='1'>Yes</label>
                    <input type='radio' name='public' value='0' ";
                    if($content['public'] == 0) echo "checked";
                    echo "/><label for='0'>No</label>
                    <input type='submit' name='submit' class='editor-submit' value='Submit'/>
                </form>";
        }
    ?>
    </div>
</body>

<?php
    require 'scripts/jsload.php';
    endif;
?>

</html>
