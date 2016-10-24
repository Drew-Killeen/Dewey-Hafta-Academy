<span id="usrHeader">
    <?php
        if(!$_SESSION['id']):
    ?>
    
    <a href="../sign_in">Sign in</a> - 
    <a href="../sign_up">Sign up</a>
    
    <?php
        else:
    ?>
    
    <a href="../registered">Preferences</a> - 
    <a href="?logoff">Logout</a>
    
    <?php
        endif;
    ?>
</span>