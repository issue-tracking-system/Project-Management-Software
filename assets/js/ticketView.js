//status change
var tid = ticketView.tid;
var closedid = ticketView.closeid;

function changeStatus(ticket_id, to_status_id) {
    $.ajax({
        type: "POST",
        url: ticketView.dashboard_url,
        data: {ticketid: ticket_id, tostatusid: to_status_id, userid: ticketView.user_id, projectid: ticketView.project_id}
    }).done(function (data) {
        if (data == 0) {
            alert(lang.dash_save_status_err);
        } else {
            $(".changed").fadeIn(500).delay(2000).fadeOut(500);
        }
    });
}
$('#status').on('change', function () {
    changeStatus(tid, $(this).val());
    if ($("#status option:selected").val() == 4) {
        $('.close-ticket-btn').addClass('disabled');
    } else {
        $('.close-ticket-btn').removeClass('disabled');
    }
});

$('.close-ticket-btn').on('click', function () {
    changeStatus(tid, closedid);
    window.location = ticketView.dashboard_location;
});
//comments
function load_cke() {
    if ($("#cke_comment")) {
        $("#cke_comment").remove();
    }
    var hEd = CKEDITOR.instances['comment'];
    if (hEd) {
        CKEDITOR.remove(hEd);
    }
    CKEDITOR.replace('comment');
}
function clearLikeFirstUse() {
    $("[name='timeupdated']").val(0);
    $("[name='id']").val(0);
    $("[name='sub_for']").val(0);
    $('.cancel-edit').hide();
}
$('.reply').click(function () {
    $('a[href="#add-comment"]').tab('show');
    $('.cancel-edit').show();
    $('#comment').val('');
    var into = $(this).attr('data-subfor-id');
    $("[name='sub_for']").val(into);
});
$('.edit').click(function () {
    load_cke();
    $('.cancel-edit').show();
    $('a[href="#add-comment"]').tab('show');
    var unique_id = $(this).attr('data-unique-id');
    var sub_for = $(this).attr('data-subfor-id');

    $("[name='sub_for']").val(sub_for);
    $("[name='timeupdated']").val(1);
    $("[name='id']").val(unique_id);

    var html = $(this).prevAll("div.media-comment").first().html();
    $('#comment').val(html);
});
$('.cancel-edit, [href="#comments-users"]').click(function () {
    $('a[href="#comments-users"]').tab('show');
    clearLikeFirstUse();
    $('#comment').val('');
    load_cke();
});
//tracker
function tracker(event) {
    $.ajax({
        type: "POST",
        url: ticketView.tracker_url,
        data: {status: event, ticket_id: ticketView.result_id, user_id: ticketView.user_id, project_id: ticketView.project_id}
    }).done(function (data) {
        var arr = JSON.parse(data);
        if (arr.error) {
            $(".the-tracker p.track-msg").text(arr.error).show().delay(5000).fadeOut();
        } else if (arr.success) {
            location.reload();
        }
    });
}
$("[name='adddate']").click(function () {
    if ($(this).is(':checked'))
        $('.adddatefield').show();
    else
        $('.adddatefield').hide();
    $('.date-pick').val('');
});
var needStop = false;
function tracktimer(value) {
    if (value == 'start') {
        if (needStop) {
            needStop = false;
            return;
        }
        int_s = parseInt($('.s').text()) + 1;
        int_m = parseInt($('.m').text());
        int_h = parseInt($('.h').text());
        int_d = parseInt($('.d').text());
        if (int_s >= 60) {
            int_s = 1;
            int_m = int_m + 1;
            if (int_m >= 60) {
                int_m = 0;
                int_h = int_h + 1;
                if (int_h >= 24) {
                    int_h = 0;
                    int_d = int_d + 1;
                }
            }
        }
        $('.s').text(int_s);
        $('.m').text(int_m);
        $('.h').text(int_h);
        $('.d').text(int_d);
        setTimeout(tracktimer, 1000, value);
    }
}
tracktimer(ticketView.track_timer);

function changeNumberTimersText() {
    var num_started = $('a[data-timer-indicator]').length;
    if (num_started > 0) {
        $('div.started-timers p.text-center').text(lang.you_have + ' ' + num_started + ' ' + lang.active_timer + ' ');
    } else {
        $('div.started-timers p.text-center').text(lang.you_dont_have_active_timers + ' ');
    }
}

$('.the-tracker .track-event').click(function () {
    if ($(this).hasClass("disabled") || $(this).hasClass("active")) {
        return;
    }
    var tr_event = $(this).attr('data-track-event');
    if (tr_event == 'stop') {
        var you_sure_stop = confirm(ticketView.stop_and_save_time);
        if (!you_sure_stop) {
            return;
        }
    }
    if (tr_event == 'clear') {
        var you_sure_clear = confirm(ticketView.clear_worked_time);
        if (!you_sure_clear) {
            return;
        }

    }
    if (typeof you_sure_clear != 'undefined' || typeof you_sure_stop != 'undefined') {
        $('.track-event').addClass('disabled');
        $('[data-track-event="start"]').removeClass('disabled');
        $('.s, .h, .m, .d').text('0');
    }
    tracker(tr_event);
    tracktimer(tr_event);
    if (tr_event != 'start') {
        needStop = true;
    }
    $('.track-event').removeClass('active');
    if (tr_event == 'start' || tr_event == 'pause') {
        $(this).addClass('active');
    }
    if (tr_event == 'start') {
        $('.track-event').removeClass('disabled');
        if (!$('[data-timer-indicator="' + ticketView.timer_indicator + '"]').length) {
            $('.started-timers').append('<a href="' + ticketView.started_timers_href + '" data-toggle="tooltip" title="' + lang.started_on + ' ' + ticketView.started_on + ', ' + lang.current_status + lang.start + '" class="btn btn-sq-sm btn-warning" data-timer-indicator="' + ticketView.project_abbr + '"><i class="glyphicon glyphicon-time"></i><div class="text-center">' + ticketView.project_abbr + '</div></a>');
        }
        changeNumberTimersText();
    }

    if (tr_event == 'stop' || tr_event == 'clear') {
        $('[data-timer-indicator="' + ticketView.project_abbr + '"]').remove();
        changeNumberTimersText();
    }
});

function watcher(status) {
    $.ajax({
        type: "POST",
        url: ticketView.watchers_url,
        data: {status: status, u_id: ticketView.user_id, t_id: ticketView.result_id}
    }).done(function (data) {
        if (data == 0) {
            alert(lang.watchers_problem);
        }
    });
}

function assignToMe() {
    var my_name = ticketView.fullname;
    $.ajax({
        type: "POST",
        url: ticketView.assigntome_url,
        data: {ticket_id: ticketView.result_id, assigntome: ticketView.user_id}
    }).done(function (data) {
        if (data != 0) {
            $(".assigned").empty().append(my_name);
        } else {
            alert(lang.ajax_err);
        }
    });
}

$(document).ready(function () {
    CKEDITOR.replace('comment');
    $('.date-pick').datepicker({
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true,
        format: 'dd.mm.yyyy'
    });
    $('#watch-status').click(function () {
        var text = $(this).text();
        if (text == ticketView.unwatch_word) {
            var str = $("#watchers-list").text();
            var res = str.split(",");
            var index = res.indexOf(ticketView.fullname);
            res.splice(index, 1);
            $("#watchers-list").text(res);
            $(this).text(ticketView.watch_word);
            watcher('delete');
        } else if (text == ticketView.watch_word) {
            var str = $("#watchers-list").text();
            var res = str.split(",");
            var index = res.indexOf("");
            if (index >= 0) {
                res.splice(index, 1);
            }
            res.push(ticketView.fullname);
            $("#watchers-list").text(res);
            $(this).text(ticketView.unwatch_word);
            watcher('add');
        }
    });
});

function currency_ajax_convert(sum) {
    var from = ticketView.pph_c;
    var to = $('#select_cur').val();
    $(".loading-conv").show();
    $.ajax({
        type: "POST",
        url: ticketView.currency_conv_url,
        data: {sum: sum, from: from, to: to}
    }).done(function (data) {
        $(".loading-conv").hide();
        $("#new_currency").empty().append(data);
    });
}

$('#modalConvertor').on('hidden.bs.modal', function (e) {
    $("#new_currency").empty();
});