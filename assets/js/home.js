$("#sel-proj").change(function () {
    var str = "";
    var url_wiki = urlsHome.url_wiki;
    var url_tickets = urlsHome.url_tickets;
    $("#sel-proj option:selected").each(function () {
        str += $(this).attr('value');
    });
    if (str == "") {
        alert(lang.select_err_try_again);
    } else {
        $("#gotowiki").attr("href", url_wiki + str);
        $("#gototickets").attr("href", url_tickets + str);
    }
}).change();

$("#edit-proj").change(function () {
    $("#edit-proj option:selected").each(function () {
        if ($(this).val() != 0) {
            $("[name='update']").val($(this).val());
            $("[name='title']").val($(this).text());
            $("[name='abbr']").val($(this).attr('data-abbr'));
            $('[name="sync"]').attr('checked', false);
            $('#sync, .check-sync').hide();
        } else {
            $("[name='update']").val(0);
            $("[name='title']").val('');
            $("[name='abbr']").val('');
            $('.check-sync').show()
        }
    });
});
$(document).ready(function () {
    $('[data-tooltip="tooltip"]').tooltip();
    $("#home .bg-choice a").hover(function () {
        $(this).parent("#home .bg-choice").addClass("bg-choice-color-hover");
        $(this).addClass("hover-color");
    }, function () {
        $("#home .bg-choice").removeClass("bg-choice-color-hover");
        $(this).removeClass("hover-color");
    });

    $('[name="sync"]').click(function () {
        if ($(this).is(':checked')) {
            $("#sync").show();
        } else {
            $("#sync").hide();
        }
    });
    $('#check_conn').click(function () {
        var hostname = $('[name="hostname"]').val();
        var smtp_hostname = $('[name="smtp_hostname"]').val();
        var protocol = $('#protocol').val();
        var _ssl = 0;
        var smtp_ssl = 0;
        var self_signed_cert = 0;
        if ($('[name="_ssl"]').is(':checked')) {
            _ssl = 1
        }
        if ($('[name="smtp_ssl"]').is(':checked')) {
            smtp_ssl = 1
        }
        if ($('[name="self_signed_cert"]').is(':checked')) {
            self_signed_cert = 1
        }
        var port = $('[name="port"]').val();
        var smtp_port = $('[name="smtp_port"]').val();
        var folder = $('[name="folder"]').val();
        var username = $('[name="username"]').val();
        var password = $('[name="password"]').val();

        var errors = new Array();
        if ($.trim(hostname) == '') {
            errors[0] = '<strong>' + lang.hostname + '(imap/pop3)</strong> ' + lang.required;
        }
        if ($.trim(smtp_hostname) == '') {
            errors[1] = '<strong>' + lang.hostname + '(smtp)</strong> ' + lang.required;
        }
        if ($.trim(port) == '') {
            errors[2] = '<strong>' + lang.port + '(imap/pop3)</strong> ' + lang.required;
        }
        if ($.trim(smtp_port) == '') {
            errors[3] = '<strong>' + lang.port + '(smtp)</strong> ' + lang.required;
        }
        if ($.trim(folder) == '') {
            errors[4] = '<strong>' + lang.folder + '</strong> ' + lang.required;
        }
        if ($.trim(username) == '') {
            errors[5] = '<strong>' + lang.username + '</strong> ' + lang.required;
        }
        if ($.trim(password) == '') {
            errors[6] = '<strong>' + lang.password + '</strong> ' + lang.required;
        }
        if (errors.length > 0) {
            $("#check-errors").empty().show();
            for (i = 0; i < errors.length; i++) {
                if (typeof errors[i] !== 'undefined') {
                    $("#check-errors").append(errors[i] + "<br>");
                }
            }
        } else {
            $("#check-errors").hide();
            $('#check_conn i.fa').remove();
            $('#check_conn').append('<i class="fa fa-refresh fa-spin"></i>');
            $('#err_info').hide();
            $('#err_info').empty();
            $.ajax({
                type: "POST",
                url: urlsHome.sync_url,
                data: {hostname: hostname, smtp_hostname: smtp_hostname, protocol: protocol, _ssl: _ssl, smtp_ssl: smtp_ssl, self_signed_cert: self_signed_cert, port: port, smtp_port: smtp_port, folder: folder, username: username, password: password}
            }).done(function (data) {
                if (data == 1) {
                    $('#check_conn i.fa').remove();
                    $('#check_conn').append('<i class="fa fa-check" aria-hidden="true"></i>');
                } else {
                    $('#check_conn i.fa').remove();
                    $('#check_conn').append('<i class="fa fa-times" aria-hidden="true"></i>');
                    $('#err_info').show();
                    $('#err_info').append(data);
                }
            });
        }
    });
});