function setPort(type) {
    if (type == 'imap') {
        if ($('#protocol').val() == 'imap') {
            if ($('[name="_ssl"]').is(':checked')) {
                $("#port").val('993');
            } else {
                $("#port").val('143');
            }
        } else if ($('#protocol').val() == 'pop3') {
            if ($('[name="_ssl"]').is(':checked')) {
                $("#port").val('995');
            } else {
                $("#port").val('110');
            }
        } else if ($('#protocol').val() == 'nntp') {
            $("#port").val('119');
        }
    } else {
        if ($('[name="smtp_ssl"]').is(':checked')) {
            $("#smtp_port").val('465');
        } else {
            $("#smtp_port").val('587');
        }
    }
}