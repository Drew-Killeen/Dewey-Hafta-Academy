// Plugin options and our code
$("#modal_trigger").leanModal({
		top: 100,
		overlay: 0.6,
		closeButton: ".modal_close"
});

$(function() {
		// Calling Login Form
		$("#login_form").click(function() {
				$(".social_login").hide();
				$(".user_login").show();
				return false;
		});

		// Calling Register Form
		$("#register_form").click(function() {
				$(".social_login").hide();
				$(".user_register").show();
				$(".header_title").text('Register');
				return false;
		});

		// Going back to Social Forms
		$(".back_btn").click(function() {
				$(".user_login").hide();
				$(".user_register").hide();
				$(".social_login").show();
				$(".header_title").text('Login');
				return false;
		});
});
        
$(document).ready(function() {

    $('#login_btn').click(function() {
        var username=$("#username").val();
        var password=$("#password").val();
        var remember=$("#remember").val();
        if($.trim(username).length>2 && $.trim(password).length>2) {
            $.ajax({
                type: "POST",
                url: "../scripts/login_core.php",
                data: 'action=login&username='+username+'&password='+password+'&remember='+remember,
                cache: false,
                beforeSend: function(){ $(".user_login").val('<img src="../images/ripple.gif" alt="Loading...">');},
                success: function(data) {
                    if(data == 'login') {
                        $("body").fadeOut(500).load(window.location.href).fadeIn(500);
                    }
                    else {
                        $("#password_error").html("<span style='color:#cc0000'>Wrong username or password.</span>");
                    }
                }
            });

        }
        else {
            if($.trim(username).length<3) {
                $("#username_error").html("<span style='color:#cc0000'>Please enter a valid username.</span>");
            }
            else {
                $("#username_error").html("");
            }
            if($.trim(password).length<3) {
                $("#password_error").html("<span style='color:#cc0000'>Please enter a valid password.</span>");
            }
            else {
                $("#password_error").html("");
            }
        }

        return false;
        
    });

    
    
    $('#register_btn').click(function() {
        var username=$("#username_reg").val();
        var password=$("#pass_reg").val();
        var passagain=$("#passagain").val();
        var email=$("#email").val();
        var privilege=$('input[name="privilege"]:checked').val();
        
        if(privilege == undefined) {
            privilege = 'student';
        }
        var atpos = email.indexOf("@");
        var dotpos = email.lastIndexOf(".");
        if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length) {
            email = false;
        }
        if($.trim(username).length>2 && $.trim(password).length>7 && password == passagain && email !== false) {
            $.ajax({
                type: "POST",
                url: "../scripts/login_core.php",
                data: 'action=signup&email='+email+'&username='+username+'&password='+password+'&privilege='+privilege,
                cache: false,
                //beforeSend: function(){ $("#login").val('Connecting...');},
                success: function(data) {
                    if(data == 'signup') {
                        $("body").fadeOut(500).load("registered.php").fadeIn(800);
                    }
                    else {
                        alert("wrong username/password");
                    }
                }
            });

        }
        else {
            if($.trim(username).length<3) {
                $("#username_reg_error").html("<span style='color:#cc0000'>Please enter a valid username.</span>");
            }
            else {
                $("#username_reg_error").html("");
            }
            if($.trim(password).length<8) {
                $("#password_reg_error").html("<span style='color:#cc0000'>Password must be at least 8 characters.</span>");
            }
            else {
                $("#password_reg_error").html("");
            }
            if($.trim(passagain).length<8) {
                $("#pass_again_error").html("<span style='color:#cc0000'>Password must be at least 8 characters.</span>");
            }
            else if(password != passagain) {
                $("#pass_again_error").html("<span style='color:#cc0000'>Passwords do not match.</span>");
            }
            else {
                $("#pass_again_error").html("");
            }
            if(email == false) {
                $("#email_error").html("<span style='color:#cc0000'>Please enter a valid email address.</span>");
            }
            else {
                $("#email_error").html("");
            }
        }

        return false;
        
    });
    
    $('.social_box').click(function() {
        alert("This function is not currently supported. Please login with your email address.");
    });
    
});
        
function checkUsername() {
    var username=$("#username_reg").val();
    if(/^[a-zA-Z0-9]+$/.test(username) == false && username != "") {
        $("#username_reg_error").html("<span style='color:#cc0000'>Only alphanumeric characters are allowed.</span>");
    }
    else {
        $("#username_reg_error").html("");
    }
}
        
function checkUsernameExists() {
    var username=$("#username_reg").val();
    $.ajax({
        type: "POST",
        url: "../scripts/login_core.php",
        data: 'action=checkusername&username='+username,
        cache: false,
        success: function(data) {
            if(data == 'error') {
                $("#username_reg_error").html("<span style='color:#cc0000'>Username already exists.</span>");
            }
            else {
                $("#username_reg_error").html("");
            }
        }
    });
}
        
function checkEmail() {
    var email=$("#email").val();
    var atpos = email.indexOf("@");
    var dotpos = email.lastIndexOf(".");
    if ((atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length) && email != "") {
        $("#email_error").html("<span style='color:#cc0000'>Please enter a valid email address.</span>");
    }
    else {
        $("#email_error").html("");
    }
}
        
/*function checkEmailExists() {
    var email=$("#email").val();
    $.ajax({
        type: "POST",
        url: "../scripts/login_core.php",
        data: 'action=checkemail&email='+email,
        cache: false,
        success: function(data) {
            if(data == 'error') {
                $("#email_error").html("<span style='color:#cc0000'>This email is already associated with an account.</span>");
            }
            else {
                $("#email_error").html("");
            }
        }
    });
}*/
        
function checkPassword() {
    var password=$("#pass_reg").val();
    if($.trim(password).length<8 && password != "") {
        $("#password_reg_error").html("<span style='color:#cc0000'>Password must be at least 8 characters.</span>");
    }
    else if((password.search(/\d/) == -1 || password.search(/[a-zA-Z]/) == -1) && password != "") {
        $("#password_reg_error").html("<span style='color:#cc0000'>Password must contain a number and a letter.</span>");
    }
}
        
function checkPassword2() {
    var password=$("#pass_reg").val();
    if($.trim(password).length>7 && password.search(/\d/) != -1 && password.search(/[a-zA-Z]/) != -1) {
        if(password.search(/[^a-zA-Z0-9\!\@\#\$\%\^\&\*\(\)\_\+]/) != -1) {
            $("#password_reg_error").html("<span style='color:#cc0000'>Password contains an invalid character.</span>");
        }
        else {
            $("#password_reg_error").html("");
        }
    }
}
        
function checkPasswordMatch() {
    var password=$("#pass_reg").val();
    var passagain=$("#passagain").val();
    if(password != passagain && passagain != "") {
        $("#pass_again_error").html("<span style='color:#cc0000'>Passwords do not match.</span>");
    }
    
}
        
function checkPasswordMatch2() {
    var password=$("#pass_reg").val();
    var passagain=$("#passagain").val();
    if(password == passagain) {
        $("#pass_again_error").html("");
    }
}