$(document).ready(function () {
    $('.date-pick').datepicker({
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true,
        format: 'dd.mm.yyyy'
    });

    $('body').on('click', '.list-group .list-group-item', function () {
        $(this).toggleClass('active');
    });

    $('.list-users .selector').click(function () {
        var $checkBox = $(this);
        if (!$checkBox.hasClass('selected')) {
            $checkBox.addClass('selected').closest('.well').find('ul li:not(.active)').addClass('active');
            $checkBox.children('i').removeClass('glyphicon-unchecked').addClass('glyphicon-check');
        } else {
            $checkBox.removeClass('selected').closest('.well').find('ul li.active').removeClass('active');
            $checkBox.children('i').removeClass('glyphicon-check').addClass('glyphicon-unchecked');
        }
    });

    $('[name="SearchInUsers"]').keyup(function (e) {
        var code = e.keyCode || e.which;
        if (code == '9')
            return;
        if (code == '27')
            $(this).val(null);
        var $rows = $(this).closest('.list-users').find('.list-group li');
        var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
        $rows.show().filter(function () {
            var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
            return !~text.indexOf(val);
        }).hide();
    });

    $("#add-from-list").click(function () {
        //get values, delete from list and add labels
        var s_wach_users = $("#listUsers .list-users ul.list-group li.active")
                .map(function () {
                    var w_id = $(this).attr("data-watcher-id");
                    var w_name = $(this).text();
                    addUserToList(w_id, w_name);
                    $(this).remove();
                    return w_id;
                }).get().join();

        //add id values to hidden name=watchers input
        var str = $('[name="watchers"]').val();
        var res = str.split(",");
        var index = res.indexOf("");
        if (index >= 0) {
            res.splice(index, 1);
        }
        res.push(s_wach_users);
        $('[name="watchers"]').val(res);
    });

    //price per hour
    var pph_c = $("#pph_c").val();
    $("#the-currency").text(pph_c);
    $("#pph_c").change(function () {
        $("#the-currency").text($(this).val());
    });

    //connected tickets
    var connected = 0;
    $('#connect-ticket').click(function () {
        if (connected == 0) {
            $('#newissue div.ticket-connect').show();
        } else {
            $('#newissue div.ticket-connect').first().clone(true).appendTo("#cloned-conns");
            $('#newissue div.ticket-connect:last input').val('');
            $('#newissue div.ticket-connect:last div.newissue-q-result').empty().hide();
        }
        connected++;
    });

    $('.remove-connect-ticket').on('click', function () {
        if ($('div.ticket-connect').size() > 1) {
            $(this).closest('div.ticket-connect').remove();
        } else {
            $('div.ticket-connect').hide();
            $('#newissue div.ticket-connect:last input').val('');
            connected = 0;
        }
    });

    $('.find-issue').keyup(function (e) {
        var search_q = $(this).val();
        var s_element = $(this);
        var my_ticket = newIssue.editable;
        if (jQuery.trim(search_q).length > 0) {
            $.ajax({
                type: "POST",
                url: newIssue.url_findticket,
                data: {find: search_q, my_ticket: my_ticket}
            }).done(function (data) {
                if (data != 0) {
                    s_element.next('div.newissue-q-result').empty().show().append(data);
                } else {
                    s_element.next('div.newissue-q-result').empty().hide();
                    s_element.next('div.newissue-q-result').show().append('<div class="list-group-item select-suggestion" style="background-color:#f2dede !important;">' + lang.there_are_no_results + '</div>');
                }
            });
        } else {
            s_element.next('div.newissue-q-result').empty().hide();
        }
    });
    $('div.newissue-q-result').on('click', 'div.select-suggestion', function () {
        var selected_t = $(this).data('conticket');
        var my_div = $(this).parent().closest('div.ticket-connect');
        my_div.children('.fields').children('.find-issue').val($(this).text());
        my_div.children('.issue-links').val(selected_t);
    });
    $(document).click(function (e) {
        $('div.newissue-q-result').empty().hide();
    });
    //connected tickets end
});
var parent_selected_text, parent_selected_value;
$('#assignee').on('change', function () {
    var selected_text = $("#assignee option:selected").text();
    var selected_value = $("#assignee option:selected").val();
    if (parent_selected_text && parent_selected_value) {
        tagRemove(parent_selected_value, parent_selected_text);
    }
    parent_selected_text = selected_text;
    parent_selected_value = selected_value;
    var str = $('[name="watchers"]').val();
    var res = str.split(",");
    var index = res.indexOf("");
    var is_allready = res.indexOf(selected_value);
    if (is_allready < 0 && selected_value != "") {
        if (index >= 0) {
            res.splice(index, 1);
        }
        addUserToList(selected_value, selected_text);
        $('#listUsers .list-users ul.list-group li[data-watcher-id="' + selected_value + '"]').remove();
        res.push(selected_value);
        $('[name="watchers"]').val(res);
    }
});

$('[name="sendEmail"]').click(function () {
    if ($('[name="sendEmail"]').is(':checked')) {
        $('#email_info').show();
    } else {
        $('#email_info').hide();
    }
});

function tagRemove(id, name) {
    $('[data-rem-watch-id="' + id + '"]').remove();
    if (!$('[data-watcher-id="' + id + '"]').length) {
        $("#listUsers .list-users .list-group").append('<li class="list-group-item" data-watcher-id="' + id + '"><i class="fa fa-user"></i> ' + name + '</li>');
    }
    var arr = new Array();
    var str = $('[name="watchers"]').val();
    var arr = str.split(',').map(function (x) {
        return parseInt(x)
    });
    var index = arr.indexOf(id);
    arr.splice(index, 1);
    $('[name="watchers"]').val(arr);
}

function addUserToList(value, name) {
    $("#watcher div.tags-input").append('<span class="label label-info tag" data-rem-watch-id="' + value + '">' + name + ' <span class="remove" onClick="tagRemove(' + value + ', \'' + name + '\')"><span class="glyphicon glyphicon-remove"></soan></span></span>');
}