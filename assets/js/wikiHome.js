function goAjax(projectid, from, to) {
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
function showMore(projectid) {
    to += 10;
    from += 10;
    goAjax(projectid, from, to);
}