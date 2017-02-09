<div id="header">
    <a href="../main" id="title">Dewey Hafta Academy</a>
    <span id="usrHeader">
        <?php
            if(!$_SESSION['id']):
        ?>

        <a id="modal_trigger" href="#modal">Login / Sign up</a>

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

<div id="modal" class="popupContainer" style="display:none;">
    <header class="popupHeader">
        <span class="header_title">Login</span>
        <span class="modal_close"><i class="fa fa-times"></i></span>
    </header>

    <section class="popupBody">
        <!-- Social Login -->
        <div class="social_login">
            <div class="">
                <a href="#" class="social_box fb">
                    <span class="icon"><i class="fa fa-facebook"></i></span>
                    <span class="icon_title">Connect with Facebook</span>

                </a>

                <a href="#" class="social_box google">
                    <span class="icon"><i class="fa fa-google-plus"></i></span>
                    <span class="icon_title">Connect with Google</span>
                </a>
            </div>

            <div class="centeredText">
                <span>Or use your Email address</span>
            </div>

            <div class="action_btns">
                <div class="one_half"><a href="#" id="login_form" class="login_btn">Login</a></div>
                <div class="one_half last"><a href="#" id="register_form" class="login_btn">Sign up</a></div>
            </div>
        </div>       

        <!-- Username & Password Login form -->
        <div class="user_login">
            <form class="clearfix" action="" method="post">
                <label>Username <input type="text" name="username" id="username"/></label><span id="username_error"></span><br>
                <label>Password <input type="password" name="password" id="password"/></label><span id="password_error"></span><br>

                <div class="checkbox">
                    <label><input id="remember" type="checkbox" name="rememberMe"> Remember me on this computer</label>
                </div>

                <div class="action_btns">
                    <div class="one_half">
                        <a class="login_btn back_btn" href="#">Back</a>
                    </div>

                    <div class="one_half last">
                        <a class="login_btn login_btn_color" href="#" id="login_btn">Login</a>
                    </div>
                </div>
            </form>

            <a class="forgot_password" href="?action=forgot_password">Forgot password?</a>
        </div>   

        <!-- Register Form -->
        <div class="user_register">
            <form>
                <label>Username
                <input type="text" name="username" id="username_reg" oninput="checkUsername()" onblur="checkUsernameExists()"/></label>
                <span id="username_reg_error"></span>
                <br />

                <label>Email Address
                <input type="email" name="email" id="email" onblur="checkEmail()"/></label>
                <span id="email_error"></span>
                <br />

                <label>Password
                <input type="password" name="pass" id="pass_reg" oninput="checkPassword2()" onblur="checkPassword()"/></label>
                <span id="password_reg_error"></span>
                <br />

                <label>Confirm Password
                <input type="password" name="passagain" id="passagain" oninput="checkPasswordMatch2()" onblur="checkPasswordMatch()"/></label>
                <span id="pass_again_error"></span>
                <br />

                <span id='privilege'>
                    <p>I plan to use this site as a...</p>
                    <label><input type='radio' name='privilege' value='student'>Student</label>
                    <label><input type='radio' name='privilege' value='teacher'>Teacher</label>
                </span><br>

                <div class="action_btns">
                    <div class="one_half"><a href="#" class="login_btn back_btn"><i class="fa fa-angle-double-left"></i> Back</a></div>
                    <div class="one_half last"><a href="#" class="login_btn login_btn_color" id="register_btn">Register</a></div>
                </div>
            </form>
        </div>
    </section>
</div>