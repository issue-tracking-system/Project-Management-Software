function validateSpace() {
    var name = $("#name").val();
    var space_key = $("#sp_key").val();
    var errors = new Array();
    if (jQuery.trim(name).length <= 0 || jQuery.trim(name).length > 50) {
        errors[0] = '<strong>' + lang.space_name_err + '</strong> ' + lang.space_name_valid;
    }
    if (jQuery.trim(space_key).length > 20 || jQuery.trim(space_key).length < 3 || jQuery.trim(space_key).search(createSpace.space_key_regex) === -1) {
        errors[1] = '<strong>' + lang.space_key_wrong + '</strong> ' + lang.space_key_allowed;
    } else {
        var taken;
        $.ajax({
            type: "POST",
            async: false,
            url: createSpace.space_key_check,
            data: {space_key: jQuery.trim(space_key)}
        }).done(function (data) {
            if (data > 0) {
                taken = true;
            }
        });
        if (taken == true) {
            errors[1] = '<strong>' + lang.space_taken + '</strong> ' + lang.space_select_another;
        }
    }
    if (errors.length > 0) {
        $("#sp-result").show();
        for (i = 0; i < errors.length; i++) {
            if (i == 0)
                $("#sp-result").empty();
            if (typeof errors[i] !== 'undefined') {
                $("#sp-result").append(errors[i] + "<br>");
            }
        }
        return false;
    } else {
        return true;
    }
}