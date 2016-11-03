$(document).ready(function () {
    $('.mine-dropdown .dropdown-menu').on('click', function (event) {
        event.stopPropagation();
    });
    $('#notifPopover').on('show.bs.popover', function () {
        getNotifs(true, 0, 10);
    });
    $('.mine-dropdown').on('hidden.bs.dropdown', function () {
        $('#notifPopover').popover('hide');
    })
    $("#notifPopover").popover({
        title: '<h3 class="custom-title">' + lang.notifications + '</h3>',
        content: "",
        template: '<div class="popover popover-notif" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div><a href="#" id="load-more-notifs" style="display:none;" onClick="showMoreNotif(false)">' + lang.load_more + '</a></div>',
        html: true,
        placement: 'auto'
    });
});

function getNotifs(remove) {
    $.ajax({
        type: "POST",
        url: urlsTickets.notifications_tickets,
        data: {project_id: urlsTickets.project_id, user_id: urlsTickets.user_id, from: notif_from, to: notif_to}
    }).done(function (data) {
        var result = JSON.parse(data);
        if (remove === true) {
            $("div.popover-notif .popover-content").empty();
        }
        $("div.popover-notif .popover-content").append(result.html);
        $("#notifPopover span.badge").empty().append(result.num);
        if (result.num > 0) {
            $("#load-more-notifs").show();
        } else {
            $("#load-more-notifs").hide();
        }
    });
}

notif_from = 0;
notif_to = 10;
function showMoreNotif(remove) {
    notif_from += 10;
    notif_to += 10;
    getNotifs(remove, notif_from, notif_to);
}