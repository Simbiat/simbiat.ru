function login_init()
{
    $("#new_login").on("input", function(){userregcheck("name");});
    $("#new_email").on("input", function(){userregcheck("email");});
    $("#new_password").on("input", function(){userregcheck("password");});
    $("#new_password2").on("input", function(){userregcheck("password2");});
    $("#new_login, #new_email, #new_password, #new_password2, #register-butt").on("keypress", function(e){if(e.which == 13) {user_register_reset()}});
    $("#register-butt").on("click", function(){user_register_reset()});
    $("#register-call, #regform-close").on("click", function(){regform_show()});
    $("#login-submit").on("click", function(){user_login("login")});
    $("#login, #password, #login-submit").on("keypress", function(e){if(e.which == 13) {user_login("login")}});
    $("#verify_user").on("click", function(){user_verify_mail()});
    $("#prelogout").on("click", function(){$("#fulloutwarn").show();});
    $("#fulloutconfirm").on("click", function(){user_login("fulllogout")});
}

function user_verify_mail()
{
    var url = "https://"+defaulthost+"/api/user/verify/";
    postdata = {
        userid: $("#verify_user").attr('data-id'),
    };
    $("#verify_resend").html('<i class="fonticon-cog spin" title="Communicting with server..." style="height: 40px;line-height: 40px;padding-top: 3.5px;color:#F3F2F2;"></i>');
    $.ajax({
        headers: {
            'X-Csrftoken': $('meta[name="X-Csrftoken"]').attr('content')
        },
        "url": url,
        "method": "POST",
        "processData": true,
        "dataType": "json",
        "data": postdata,
        "success": function(data, response, jqXHR){ajaxverifymail(data, response, jqXHR);},
        "error": function(data, response, jqXHR){ajaxverifymail(data, response, jqXHR);},
    });
}

function ajaxverifymail(data, response, jqXHR)
{
    if (data === true) {
        $("#verify_resend").html('<i class="fonticon-check"></i>Mail successfully resent!').css("color", "lightgreen");
    } else {
        $("#verify_resend").html('<i class="fonticon-cross"></i>Failed to resend mail!').css("color", "red");
    }
}

function user_login(action)
{
    var url = "https://"+defaulthost+"/api/user/"+action+"/";
    if (action == "login") {
        var $login = $("#login").val();
        var $password = $("#password").val();
        if ($login.length > 0 && $password.length > 0) {
            postdata = {
                username: $login,
                password: $password,
            };
        } else {
            return;
        }
    } else if (action == "fulllogout") {
        postdata = {};
    }
    $.ajax({
        headers: {
            'X-Csrftoken': $('meta[name="X-Csrftoken"]').attr('content')
        },
        "url": url,
        "method": "POST",
        "processData": true,
        "data": postdata,
        "dataType": "json",
        "success": function(data, response, jqXHR){ajaxlogin(data, response, jqXHR);},
        "error": function(data, response, jqXHR){ajaxlogin(data, response, jqXHR);},
    });
}

function ajaxlogin(data, response, jqXHR)
{
    if (data === true) {
        location.reload();
    } else {
        alert(data);
    }
}

function user_register_reset()
{
    if (
        (
            (
                ($("#logincheck>i").hasClass('fonticon-check') && $("#new_email").val().length == 0)
                ||
                ($("#emailcheck>i").hasClass('fonticon-check') && $("#new_login").val().length == 0)
            )
            && $("#new_password").val().length == 0 && $("#new_password2").val().length == 0
        )
        ||
        (
            $("#logincheck>i").hasClass('fonticon-check')
            &&
            $("#emailcheck>i").hasClass('fonticon-check')
            &&
            $("#passcheck>i").hasClass('fonticon-check')
            &&
            $("#passcheck2>i").hasClass('fonticon-check')
        )
    ) {
        userregajax($("#register-result"), {
            username: $("#new_login").val(),
            email: $("#new_email").val(),
            password: $("#new_password").val(),
            password2: $("#new_password2").val(),
        });
    }
}

function regform_show()
{
    if ($("#register-form").css("display") == "none") {
        $("#register-form").css("display", "flex");
    } else {
        $("#register-form").css("display", "none");
    }
}

function userregcheck(what)
{
    if (what == "name") {
        var field = $("#new_login");
        var checkspan = $("#logincheck");
    } else if (what == "email") {
        var field = $("#new_email");
        var checkspan = $("#emailcheck");
    } else if (what == "password") {
        var field = $("#new_password");
        var checkspan = $("#passcheck");
    } else if (what == "password2") {
        var field = $("#new_password2");
        var checkspan = $("#passcheck2");
    }
    if (field.val().length > 0) {
        checkspan.html('');
        if (what == "email") {
            field.val(field.val().toLowerCase());
            if (field.val().length <= 100) {
                var regex = /(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/i;
                if (regex.exec(field.val()) !== null) {
                    userregajax(checkspan, {email: field.val()});
                } else {
                    checkspan.html('<i class="fonticon-cross"></i>').css("color", "red").prop('title', 'Invalid e-mail format!');
                }
            } else {
                checkspan.html('<i class="fonticon-cross"></i>').css("color", "red").prop('title', 'Too long!');
            }
        } else if (what == "name") {
            if (field.val().length <= 20) {
                userregajax(checkspan, {username: field.val()});
            } else {
                checkspan.html('<i class="fonticon-cross"></i>').css("color", "red").prop('title', 'Too long!');
            }
        } else if (what == "password2") {
            if (field.val() == $("#new_password").val()) {
                checkspan.html('<i class="fonticon-check"></i>').css("color", "lightgreen").prop('title', 'Passwords match!');
            } else {
                checkspan.html('<i class="fonticon-cross"></i>').css("color", "red").prop('title', 'Passwords do not match!');
            }
        } else if (what == "password") {
            userregajax(checkspan, {
                username: $("#new_login").val(),
                email: $("#new_email").val(),
                password: field.val(),
            });
        }
    } else {
        checkspan.html('<i class="fonticon-cross"></i>').css("color", "red").prop('title', 'Cannot be empty!');
    }
}

function userregajax(checkspan, postdata)
{
    if (checkspan[0].id == "register-result") {
        var url = "https://"+defaulthost+"/api/user/register/";
    } else {
        var url = "https://"+defaulthost+"/api/user/regcheck/";
    }
    checkspan.html('<i class="fonticon-cog spin" title="Communicting with server..." style="height: 40px;line-height: 40px;padding-top: 3.5px;color:#F3F2F2;"></i>');
    $.ajax({
        headers: {
            'X-Csrftoken': $('meta[name="X-Csrftoken"]').attr('content')
        },
        "url": url,
        "method": "POST",
        "processData": true,
        "data": postdata,
        "dataType": "json",
        "checkspan": checkspan,
        "success": function(data, response, jqXHR){userregmark(data, response, jqXHR, this.checkspan);},
        "error": function(data, response, jqXHR){userregmark(data, response, jqXHR, this.checkspan);},
    });
}
function userregmark(data, response, jqXHR, checkspan)
{
    if (data === true) {
        if (checkspan[0].id == "register-result") {
            checkspan.html('<i class="fonticon-check"></i> User registered successfully! Reloading page...').css("color", "lightgreen").prop('title', data);
            location.reload();
        } else {
            checkspan.html('<i class="fonticon-check"></i>').css("color", "lightgreen").prop('title', 'Free!');
        }
    } else if (data === false) {
        if (checkspan[0].id == "passcheck") {
            checkspan.html('<i class="fonticon-cross"></i>').css("color", "red").prop('title', 'Username or e-mail is missing!');
        } else {
            checkspan.html('<i class="fonticon-cross"></i>').css("color", "red").prop('title', 'Already used!');
        }
    } else if (data === 0 || data === 1) {
        checkspan.html('<i class="fonticon-check"></i>').css("color", "red").prop('title', 'Too weak! Should not be used!');
    } else if (data === 2 || data === 3) {
        checkspan.html('<i class="fonticon-check"></i>').css("color", "orange").prop('title', 'Relatively strong! Can be used!');
    } else if (data === 4) {
        checkspan.html('<i class="fonticon-check"></i>').css("color", "lightgreen").prop('title', 'Very strong! Can be used!');
    } else {
        checkspan.html('<i class="fonticon-cross"></i> '+data).css("color", "red").prop('title', data);
    }
}

function rememberme()
{
    if ($("#remember-me").is(':checked')) {
        $("#remember-me-lab").prop("title", "We will remember you");
    } else {
        $("#remember-me-lab").prop("title", "We will  not remember you");
    }
}