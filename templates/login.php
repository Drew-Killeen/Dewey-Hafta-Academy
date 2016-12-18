<span id="usrHeader">
    <?php
        if(!$_SESSION['id']):
    ?>
    
    <a href="../sign_in">Login</a> - 
    <a href="../sign_up">Sign up</a>
    
    <?php
        else:
    ?>

        <div class="menu-dropdown">
            <ul class="menu">
                <li class="menu-item-has-children" style="text-align:right;"><a href="profile">Profile</a> <span class="sidebar-menu-arrow"></span>
                    <ul class="sub-menu-dropdown">
                        <li><a href="../registered">Preferences</a></li>
                        <li><a href="?logoff">Logout</a></li>
                    </ul>
                </li>
                </ul>
            </div>
    
    <?php
        endif;
    ?>
</span>