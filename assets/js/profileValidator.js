$(document).ready(function () {
    //PassStrength 
    checkPass();
    $("#pwd").on('keyup', function () {
        checkPass();
    });
    //PassGenerator
    $('#GeneratePwd').pGenerator({
        'bind': 'click',
        'passwordLength': 9,
        'uppercase': true,
        'lowercase': true,
        'numbers': true,
        'specialChars': false,
        'onPasswordGenerated': function (generatedPassword) {
            $("#pwd").val(generatedPassword);
            checkPass();
        }
    });
    $('#default_lang').click(function () {
        if ($(this).prop("checked") == true) {
            $("[name='lang']").prop("disabled", true);
        } else {
            $("[name='lang']").prop("disabled", false);
        }
    });
});

function validateForm() {
    var pwd = $("[name='password']").val();
    var fname = $("[name='fullname']").val();
    var email = $('[name="email"]').val();
    var reg = profileValidator.email_regex;
    var errors = new Array();
    if (checkPass() == false && jQuery.trim(pwd).length > 0) {

        errors[0] = '<strong>' + lang.wrong_pass + '</strong> ' + lang.validation_progr;
    }
    if (jQuery.trim(fname).search(profileValidator.fullname_regex) === -1 || jQuery.trim(fname).length <= 0) {
        errors[1] = '<strong>' + full_name_wrong + '</strong> ' + lang.fullname_validation;
    }
    if (!reg.test(jQuery.trim(email))) {
        errors[2] = '<strong>' + lang.invalid_email + '</strong> ' + lang.use_valid_email;
    }
    if (errors.length > 0) {
        $("#reg-errors").show();
        for (i = 0; i < errors.length; i++) {
            if (i == 0)
                $("#reg-errors").empty();
            if (typeof errors[i] !== 'undefined') {
                $("#success-alert").hide();
                $("#reg-errors").append(errors[i] + "<br>");
            }
        }
        return false;
    } else {
        return true;
    }

}