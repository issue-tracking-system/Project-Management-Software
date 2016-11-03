$(document).ready(function () {
    $(".alert.top-20").css("margin-left", "270px");
    $('.show-childrens').click(function () {
        $(this).nextAll('.children').toggle(300, function () {
            var isVisible = $(this).is(':visible');
            if (isVisible) {
                $(this).prevAll('.show-childrens').empty().append('<i class="fa fa-chevron-down"></i>');
            } else {
                $(this).prevAll('.show-childrens').empty().append('<i class="fa fa-chevron-right"></i>');
            }
        });
    });
    $("#share-me").popover({
        title: '<h4>' + lang.share_link + '</h4>',
        content: wikiDisplay.share_content,
        template: '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content share-link-content"></div></div>',
        html: true,
        placement: 'left'
    });
});

function watcher(status) {
    $.ajax({
        type: "POST",
        url: wikiDisplay.watchers_url,
        data: {status: status, u_id: wikiDisplay.user_id, p_id: wikiDisplay.p_id}
    }).done(function (data) {
        if (data == 0) {
            alert(lang.watchers_problem);
        }
    });
}

$('#watch-status').click(function () {
    var text = $("#watch-stat").text();
    if (text == wikiDisplay.unwatch_word) {
        $("#watch-stat").text(wikiDisplay.watch_word);
        watcher('delete');
    } else if (text == wikiDisplay.watch_word) {
        $("#watch-stat").text(wikiDisplay.unwatch_word);
        watcher('add');
    }
});

$('#pageMove .move-page').click(function () {
    var parent = $('[name="sub_for_move"]').val();
    var space = $('[name="for_space_move"]').val();
    $.ajax({
        type: "POST",
        url: wikiDisplay.page_move_url,
        data: {parent: parent, space: space, page_id: wikiDisplay.p_id}
    }).done(function (data) {
        if (data == 0) {
            $("#move-result").empty().show().append(lang.page_move_problem);
        } else {
            location.href = wikiDisplay.locat_href;
        }
    });
});