$(document).ready(function () {
    $('[data-toggle="tab"], [data-toggle="tooltip"]').tooltip();
    $(".next-step").click(function (e) {
        var $active = $('.wizard-inner .nav-tabs li.active');
        $active.next().removeClass('disabled');
        $active.addClass('disabled');
        $($active).next().find('a[data-toggle="tab"]').click();

    });
    $(".prev-step").click(function (e) {
        var $active = $('.wizard-inner .nav-tabs li.active');
        $active.addClass('disabled');
        $($active).prev().find('a[data-toggle="tab"]').click();
    });
    $("#create-page").click(function (e) {
        var errors = new Array();
        var space_val = $('#for_space option:selected').val();
        var page_name = $('[name="title"]').val();
        if (parseInt(space_val) <= 0) {
            errors[0] = '<strong>' + lang.no_selected_space + '</strong> ' + lang.space_must_be_selected;
        }
        if (jQuery.trim(page_name).length < 3) {
            errors[1] = '<strong>' + lang.too_short_page_name + '</strong> ' + lang.page_name_must_be;
        }
        if (errors.length > 0) {
            $("#reg-errors").show();
            for (i = 0; i < errors.length; i++) {
                if (i == 0)
                    $("#reg-errors").empty();
                if (typeof errors[i] !== 'undefined') {
                    $("#reg-errors").append(errors[i] + "<br>");
                }
            }
        } else {
            document.getElementById("page-create-form").submit();
        }
    });
});
$('[href="#step-c-page"]').on('hidden.bs.tab', function (e) {
    $(".prev-step").hide();
    $("#create-page").hide();
    $(".next-step").show();
});
$('[href="#step-c-page"]').on('shown.bs.tab', function (e) {
    $(".prev-step").show();
    $(".next-step").hide();
    $("#create-page").show();
});
$(".template-box").on("click", function () {
    $(".template-box").removeClass('active');
    $(this).addClass('active');
    var template_id = $(this).attr("data-cat-id");
    $('[name="page_template"]').val(template_id);
});
$("#remove-parent, #remove-parent-move").on("click", function () {
    if ($(this).attr('id') == 'remove-parent-move') {
        $('[name="sub_for_move"]').val(0);
        $('[name="suggesions_move"]').val('');
        $("#suggestions-move").empty().hide();
    } else {
        $('[name="sub_for"]').val(0);
        $('[name="suggesions"]').val('');
        $("#suggestions").empty().hide();
    }
    $(this).hide();
});
$('[name="suggesions"], [name="suggesions_move"]').keyup(function (e) {
    if ($(this).attr('name') == 'suggesions') {
        var who = 1;
        var space = 0;
    } else {
        var who = 2;
        var space = $("#for_space_move option:selected").val();
    }
    var searched_page = $(this).val();
    if (!jQuery.trim(searched_page) || 0 === jQuery.trim(searched_page).length) {
        if (who == 1) {
            $('[name="sub_for"]').val(0);
        } else {
            $('[name="sub_for_move"]').val(0);
        }
    } else {
        $.ajax({
            type: "POST",
            url: generalWiki.parent_suggestions,
            data: {find: searched_page, proj_id: generalWiki.project_id, space: space}
        }).done(function (data) {
            if (data != 0) {
                if (who == 1) {
                    $("#suggestions").empty().show().append(data);
                } else {
                    $("#suggestions-move").empty().show().append(data);
                }
            }
        });
        if (who == 1) {
            $("#remove-parent").show();
        } else {
            $("#remove-parent-move").show();
        }
    }
});
function addParent(name, sub_for, space) {
    if (space > 0) {
        $('[name="sub_for_move"]').val(sub_for);
        $("#suggestions-move").empty().hide();
        $('[name="suggesions_move"]').val(name);
    } else {
        $('[name="sub_for"]').val(sub_for);
        $("#suggestions").empty().hide();
        $('[name="suggesions"]').val(name);
    }
}
$('[name="wiki_search"]').keyup(function (e) {
    var search_q = $(this).val();
    if (jQuery.trim(search_q).length > 0) {
        $("nav.tickets-wiki form.form-s img.s-loading").show();
        $.ajax({
            type: "POST",
            url: generalWiki.wiki_search,
            data: {find: search_q, proj: generalWiki.project_name, proj_id: generalWiki.project_id}
        }).done(function (data) {
            if (data != 0) {
                $("nav.tickets-wiki form.form-s img.s-loading").hide();
                $("#wiki-query-result").empty().show().append(data);
            } else {
                $("nav.tickets-wiki form.form-s img.s-loading").hide();
                $("#wiki-query-result").empty().show().append('<a class="list-group-item select-suggestion" style="background-color:#f2dede !important;">' + lang.there_are_no_results + '</a>');
            }
        });
    } else {
        $("#wiki-query-result").empty().hide();
    }
});