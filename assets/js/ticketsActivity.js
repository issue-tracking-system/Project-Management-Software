function goAjax(userid, projectid, type, from, to) {
    $.ajax({
        type: "POST",
        url: "activity",
        data: {userid: userid, projectid: projectid, from: from, to: to, type: type}
    }).done(function (data) {
        if (type === true) {
            $("#mine-ajax").append(data);
        } else {
            $("#other-ajax").append(data);
        }
    });
}
from_mine = 0;
from_other = 0;
to_mine = 10;
to_other = 10;
function showMore(userid, projectid, type) {
    if (type === true) {
        from_mine += 10;
        to_mine += 10;
        goAjax(userid, projectid, true, from_mine, to_mine);
    } else {
        from_other += 10;
        to_other += 10;
        goAjax(userid, projectid, false, from_other, to_other);
    }
}