function setProject(url) {
    var name = $("#name").val();
    var abbr = $("#abbr").val();
    var update = $("[name='update']").val();
    var conn_settings;
    if ($('[name="sync"]').is(':checked')) {
        var _ssl = 0;
        var smtp_ssl = 0;
        var self_signed_cert = 0;
        if ($('[name="_ssl"]').is(':checked')) {
            _ssl = 1
        }
        if ($('[name="self_signed_cert"]').is(':checked')) {
            self_signed_cert = 1
        }
        if ($('[name="smtp_ssl"]').is(':checked')) {
            smtp_ssl = 1
        }
        var conn_settings = {
            hostname: $('[name="hostname"]').val(),
            smtp_hostname: $('[name="smtp_hostname"]').val(),
            protocol: $('#protocol').val(),
            _ssl: _ssl,
            smtp_ssl: smtp_ssl,
            self_signed: self_signed_cert,
            port: $('[name="port"]').val(),
            smtp_port: $('[name="smtp_port"]').val(),
            folder: $('[name="folder"]').val(),
            username: $('[name="username"]').val(),
            password: $('[name="password"]').val()
        };
    }
    $.post(url, {name: name, abbr: abbr, update: update, conn_settings: conn_settings}, function (result) {
        $("#result").empty();
        if (result > 0) {
            if (update == 0) {
                var event_txt = lang.created;
            } else {
                var event_txt = lang.updated;
            }
            $("#result").append('<div class="alert alert-success">' + lang.project_add_is + ' ' + event_txt + ' ' + lang.project_add_succ + '!</div>');
        } else if (result == 0) {
            $("#result").append('<div class="alert alert-danger">' + lang.problem_with_db + '!</div>');
        } else {
            $("#result").append('<div class="alert alert-danger">' + result + '</div>');
        }

    });
}
$('#createModal').on('hidden.bs.modal', function () {
    location.reload();
});
$("[name='title']").on('input', function (e) {
    var val = $(this).val();
    if (val.length <= 3) {
        $("[name='abbr']").val(val.toUpperCase());
    }
});