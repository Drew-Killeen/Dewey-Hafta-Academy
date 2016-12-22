<div id="header">
    <a href="main" id="title">Dewey Hafta Academy</a>
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
                <ul class="menu nolist">
                    <li class="menu-item-has-children" style="text-align:right;"><a href="../profile"><?php echo $_SESSION['usr']; ?></a> <span class="sidebar-menu-arrow"></span>
                        <ul class="sub-menu-dropdown nolist" style="display:none;">
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
</div>