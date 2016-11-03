function ajax_stat_change(ticket_id, to_status_id, old_status, reload) {
    $.ajax({
        type: "POST",
        url: dashboard.dashboard_url,
        data: {ticketid: ticket_id, tostatusid: to_status_id, userid: dashboard.user_id, projectid: dashboard.project_id}
    }).done(function (data) {
        if (data == 0) {
            alert(lang.dash_save_status_err);
            $('td').sortable('cancel');
        } else {
            $('th[data-id="' + old_status + '"] .the-num').text(function (index, currentNum) {
                return parseInt(currentNum) - 1;
            });
            $('th[data-id="' + to_status_id + '"] .the-num').text(function (index, currentNum) {
                return parseInt(currentNum) + 1;
            });
            var res = $('[data-ticket-id="' + ticket_id + '"] div.timer span').hasClass('text-danger');
            $('th[data-id="' + old_status + '"] .exp-the-num').text(function (index, currentNum) {
                var new_val = parseInt(currentNum) - 1;
                if (res == true) {
                    if (new_val <= 0) {
                        $(this, ' .expired').parent('span').hide();
                    }
                    return new_val;
                }
            });
            $('th[data-id="' + to_status_id + '"] .exp-the-num').text(function (index, currentNum) {
                var new_val = parseInt(currentNum) + 1;
                if (res == 1) {
                    $(this, ' .expired').parent('span').show();
                    return new_val;
                }
            });
        }
    }).always(function () {
        $("#dashboard span.loading").hide();
    });
    if (reload == 1) {
        location.reload();
    }
}

$(document).ready(function () {
    $("td select").click(function () {
        $("td").sortable("disable");
    });
    $("td select").focusout(function () {
        $("td").sortable("enable");
    });
    if (dashboard.num_all_tickets > 0) {
        $("td").droppable({
            activeClass: "droppable-active",
            hoverClass: "droppable-hover",
            drop: function (event, ui) {
                to_status_id = $(this).attr('data-status-id');
            }
        }).sortable({
            connectWith: 'td',
            start: function (event, ui) {
                stat_status = ui.item.attr('data-have-status');
                ticket_id = ui.item.attr("data-ticket-id");
                $(this).css({"background-color": "#fff"});
                ui.item.css({"transform": "rotate(7deg)", "opacity": "0.5"});
            },
            stop: function (event, ui) {
                ui.item.css({"transform": "none", "opacity": "1"});
                $(this).css({"background-color": ""});
                if (to_status_id != stat_status) {
                    var old_status = ui.item.attr("data-have-status");
                    ui.item.attr("data-have-status", to_status_id);
                    $("#dashboard span.loading").show();
                    ajax_stat_change(ticket_id, to_status_id, old_status);
                } else {
                    $(this).sortable('cancel');
                }
            },
            cancel: ".disable-sort-item"
        });
    }
    $('[data-toggle="tooltip"]').tooltip();
    $('.search-panel .dropdown-menu').find('a').click(function (e) {
        e.preventDefault();
        var param = $(this).attr("href").replace("#", "");
        var concept = $(this).text();
        $('.search-panel span#search_concept').text(concept);
        $('.input-group #search_param').val(param);
    });
    $('.date-pick').datepicker({
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true,
        format: 'dd.mm.yyyy'
    });
    $(window).scroll(function () {
        if ($(this).scrollTop() != 0) {
            $('#toTop').fadeIn();
        } else {
            $('#toTop').fadeOut();
        }
    });
    $('#toTop').click(function () {
        $("html, body").animate({scrollTop: 0}, 1000);
        return false;
    });

    var is_active_ord = $('.fast-filter .ord-type li.active').text();
    $('.fast-filter .ord-btn .innet-txt').text(is_active_ord);
    $(".fast-filter a, .fast-filter [name='assign-checkbox'], .fast-filter [name='watch-checkbox']").click(function () {
        var order_by = $(this).attr('data-order-by');
        var order_type = $(this).attr('data-order-type');
        if (order_by !== undefined) {
            $('[name="order_by"]').val(order_by);
        }
        if (order_type !== undefined) {
            $('[name="order_type"]').val(order_type);
        }
        document.getElementById("filter-form").submit();
    });
});