function goAjax(userid, projectid, type, from, to) {
    $.ajax({
        type: "POST",
        url: "activity",
        data: {userid: userid, projectid: projectid, from: from, to: to, type: 'true'}
    }).done(function (data) {
        $("#mine-ajax").append(data);
    });
}
from_mine = 0;
to_mine = 10;
function showMore(userid, projectid) {
    from_mine += 10;
    to_mine += 10;
    goAjax(userid, projectid, true, from_mine, to_mine);
}

function goAjax_w(projectid, from, to) {
    $.ajax({
        type: "POST",
        url: "activity_wiki",
        data: {projectid: projectid, from: from, to: to}
    }).done(function (data) {
        $("#w-ajax").append(data);
    });
}
to = 10;
from = 0;
function showMoreWiki(projectid) {
    to += 10;
    from += 10;
    goAjax_w(projectid, from, to);
}