<?php
if (!defined('APPLICATION_LOADED') || !APPLICATION_LOADED) {
    die('No direct script access.');
}

$this->title = $this->project_name . ' - ' . $this->lang_php['title_create_ticket'];

$ticket_edit = false;
$issue_links = array();
if (url_segment(3) !== false && validTicketAbbr(url_segment(3)) !== false) {
    $editable = ticketAbbrParse(url_segment(3));
    $info = $this->getTicketForEdit($editable['abbr'], $editable['id']);
    if ($editable['abbr'] == $this->project_abbr && $info != null && !isset($_POST['setticket'])) {
        foreach ($info as $key => $val) {
            $_POST[$key] = $val;
        }
        $who_watch = $this->getWatchers($info['id']);
        $issue_links = $this->getIssueLinks($info['id']);
        if (!empty($who_watch)) {
            $_POST['watchers'] = implode(',', $who_watch['ids']);
        }
        $ticket_edit = true;
        secondsToTime($_POST['estimated_seconds'], true);
    } elseif ($editable['abbr'] == $this->project_abbr && $info != null && isset($_POST['setticket'])) {
        $ticket_edit = true;
    }
    $edittable = $info['id'];
}

if (!in_array($GLOBALS['CONFIG']['PERMISSIONS']['TICKETS']['ADD_EDIT_MINE_TICKETS'], $this->permissions)) {
    die($this->lang_php['not_permissions']);
}

if (isset($_POST['setticket'])) {
    unset($_POST['sendEmail']);
    if ($_POST['receiver_email'] != null && preg_match($GLOBALS['CONFIG']['EMAILREGEX'], $_POST['receiver_email'])) {
        $isHTML = false;
        if ($_POST['isHTML'] !== null) {
            $isHTML = true;
        }
        $sync_info = $this->getSyncInfo($pro['sync']);
        $send_email = array(
            'hostname' => $sync_info[0]['smtp_hostname'],
            'port' => $sync_info[0]['smtp_port'],
            'ssl' => $sync_info[0]['smtp_ssl'],
            'username' => $sync_info[0]['username'],
            'password' => $sync_info[0]['password'],
            'subject' => $_POST['subject'],
            'body' => $_POST['description'],
            'is_html' => $isHTML,
            'fromName' => 'support',
            'to_email' => $_POST['receiver_email']
        );
        $result_send = $this->sendEmail($send_email);
        $_POST['send'] = 1;
        $_POST['message_to_email'] = $_POST['receiver_email'];
        $_POST['message_id'] = $this->lastMessageId;
    }

    unset($_POST['attachments'], $_POST['isHTML']);
    if ($_POST['receiver_email'] == null || ($_POST['receiver_email'] != null && $result_send === true)) {
        unset($_POST['receiver_email']);
        if (isset($this->serialized_send_files)) {
            $_POST['message_attachments'] = $this->serialized_send_files;
        }
        $result = $this->setTicket($_POST, $ticket_edit);
        if ($result === true) {
            if ($ticket_edit === false) {
                $this->set_alert($this->lang_php['ticket_added'] . '!', 'success');
                $goto = $this->url . '/dashboard';
            } else {
                $this->set_alert($this->lang_php['ticket_updated'] . '!', 'success');
                $goto = $this->url . '/view/' . url_segment(3);
            }
            redirect(base_url($goto));
        } elseif ($result === false) {
            $this->set_alert($this->lang_php['ticket_add_err'], 'danger');
        } elseif (is_array(($result))) {
            $this->set_alert(implode("<br>", $result), 'danger');
        }
    } elseif ($_POST['receiver_email'] != null && $result_send === false) {
        $this->set_alert($this->lang_php['problem_send_email'], 'danger');
    }
    redirect();
}

$types = $this->getTicketTypes();
$statuses = $this->getStatuses();
$users = $this->getUsers();
$priorities = $this->getPriority();
$currencies = $this->getCurrencies();
?>
<link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-datepicker.min.css') ?>">
<script src="<?= base_url('assets/js/ckeditor/ckeditor.js') ?>"></script>
<div id="newissue">
    <h1><?= $ticket_edit === true ? $this->lang_php['update_ticket'] : $this->lang_php['new_issue'] ?></h1>
    <?= $this->get_alert() ?>
    <form class="form-horizontal newissue" role="form" method="post" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label class="control-label col-sm-2" for="type"><?= $this->lang_php['type'] ?> *</label>
            <div class="col-sm-10">
                <select class="selectpicker form-control show-tick show-menu-arrow" name="type" id="type">
                    <?php foreach ($types as $type) { ?>
                        <option <?= isset($_POST['type']) && $_POST['type'] == $type['id'] ? 'selected' : '' ?> value="<?= $type['id'] ?>"><?= $type['name'] ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2" for="subject"><?= $this->lang_php['subject'] ?> *</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="subject"  maxlength="200" value="<?= isset($_POST['subject']) ? $_POST['subject'] : '' ?>" id="subject" placeholder="<?= $this->lang_php['enter_subject'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2" for="description"><?= $this->lang_php['description'] ?></label>
            <div class="col-sm-10">
                <textarea name="description" id="description" rows="50" class="form-control"><?= isset($_POST['description']) ? $_POST['description'] : '' ?></textarea>
                <script>
                    CKEDITOR.replace('description');
                </script>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2" for="status"><?= $this->lang_php['status'] ?> *</label>
            <div class="col-sm-10">
                <select class="selectpicker form-control show-tick show-menu-arrow" id="status" name="status" value="<?= $_POST['status'] ?>">
                    <?php foreach ($statuses as $status) { ?>
                        <option <?= isset($_POST['status']) && $_POST['status'] == $status['id'] ? 'selected' : '' ?> value="<?= $status['id'] ?>"><?= $status['name'] ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2" for="priority"><?= $this->lang_php['priority'] ?> *</label>
            <div class="col-sm-10">
                <select class="selectpicker form-control show-tick show-menu-arrow" id="priority" name="priority">
                    <?php foreach ($priorities as $priority) { ?>
                        <option style="border-left:5px solid <?= $priority['color'] ?>;" <?= isset($_POST['priority']) && $_POST['priority'] == $priority['id'] ? 'selected' : '' ?> value="<?= $priority['id'] ?>"><?= $priority['name'] ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2" for="assignee"><?= $this->lang_php['assignee'] ?></label>
            <div class="col-sm-10">
                <select class="selectpicker form-control show-tick show-menu-arrow" name="assignee" id="assignee">
                    <option></option>
                    <?php foreach ($users as $user) { ?>
                        <option <?= isset($_POST['assignee']) && $_POST['assignee'] == $user['id'] ? 'selected' : '' ?> value="<?= $user['id'] ?>"><?= $user['fullname'] ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-group" id="watcher">
            <input type="hidden" name="watchers" value="<?= isset($_POST['watchers']) ? $_POST['watchers'] : '' ?>">
            <label class="control-label col-sm-2"><?= $this->lang_php['watchers'] ?></label>
            <div class="col-sm-10">
                <div class="row">
                    <div class="col-sm-11">
                        <div class="tags-input">

                        </div>
                    </div>
                    <div class="col-sm-1 right-add">
                        <button type="button" class="btn btn-default pull-right" data-toggle="modal"  title="add worked time" data-target="#listUsers"><span class="glyphicon glyphicon-plus"></span></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2" for="duedate"><?= $this->lang_php['due_date'] ?></label>
            <div class="col-sm-10">
                <input type="text" class="form-control date-pick" name="duedate" id="duedate" value="<?= $ticket_edit === true && !isset($_POST['setticket']) && $_POST['duedate'] > 0 ? date('d.m.Y', $_POST['duedate']) : isset($_POST['duedate']) ? $_POST['duedate'] : '' ?>" placeholder="dd.mm.yyy">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2" for="pph"><?= $this->lang_php['price_per_hour'] ?></label>
            <div class="col-sm-10">
                <div class="row">
                    <div class="col-xs-6">
                        <input type="text" class="form-control" name="pph" id="pph" value="<?= isset($_POST['pph']) ? $_POST['pph'] : '' ?>" placeholder="<?= $this->lang_php['price'] ?>/<?= $this->lang_php['hour'] ?>">
                        <span id="the-currency"></span>
                    </div>
                    <div class="col-xs-6">
                        <select class="selectpicker form-control show-tick show-menu-arrow" data-live-search="true" name="pph_c" id="pph_c">
                            <?php foreach ($currencies as $currency) { ?>
                                <option <?= isset($_POST['pph_c']) && $_POST['pph_c'] == $currency['currency'] || !isset($_POST['pph_c']) && $currency['def'] == 1 ? 'selected' : '' ?> value="<?= $currency['currency'] ?>"><?= $currency['country'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2"><?= $this->lang_php['issue_links'] ?></label>
            <div class="col-sm-10">
                <?php
                $num = 1;
                if ($ticket_edit == true && !empty($issue_links)) {
                    $num = count($issue_links);
                }
                for ($i = 1; $i <= $num; $i++) {
                    $ii = $i - 1;
                    if (!empty($issue_links) && $ticket_edit == true) {
                        $original_updates_iss_links .= $issue_links[$ii]['id'] . ',';
                    }
                    if ($num > 1 && $i == 2) {
                        ?>
                        <div id="cloned-conns">
                            <?php
                        }
                        ?>
                        <div class="row ticket-connect" <?= !empty($issue_links) ? 'style="display:block;"' : '' ?>>
                            <div class="col-sm-6 left">
                                <select class="form-control" name="issue_links_types[]">
                                    <?php
                                    foreach ($GLOBALS['CONFIG']['ISSUE_LINKS'] as $issue_type_key => $issue_type_val) {
                                        $selected = '';
                                        if (!empty($issue_links) && $issue_links[$ii]['who_is'] == 1) {
                                            if ($issue_type_key == $issue_links[$ii]['type']) {
                                                $selected = 'selected';
                                            }
                                        } else {
                                            if ($issue_type_val == $issue_links[$ii]['type']) {
                                                $selected = 'selected';
                                            }
                                        }
                                        ?>
                                        <option <?= $selected ?> value="<?= $issue_type_key ?>"><?= $this->lang_php[$issue_type_key] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-sm-5 fields">
                                <input type="text" placeholder="<?= $this->lang_php['find_by_word'] ?>" value="<?= !empty($issue_links) ? $issue_links[$ii]['subject'] . ' / ' . $issue_links[$ii]['project_abbr'] . '-' . $issue_links[$ii]['ticket_id'] : '' ?>" class="form-control find-issue">
                                <div class="list-group newissue-q-result">

                                </div>
                                <img src="<?= base_url('assets/imgs/settings-search-spinner.gif') ?>" alt="loading" class="s-loading">
                            </div>
                            <div class="col-sm-1">
                                <button type="button" value="" class="btn btn-default pull-right remove-connect-ticket"><span class="glyphicon glyphicon-remove"></span></button>
                            </div>
                            <input type="hidden" name="issue_links[]" class="issue-links" value="<?= !empty($issue_links) && $issue_links[$ii]['who_is'] == 1 ? $issue_links[$ii]['ticket_2'] : $issue_links[$ii]['ticket_1'] ?>">
                            <input type="hidden" name="issue_links_updates[]" value="<?= !empty($issue_links) && $ticket_edit == true ? $issue_links[$ii]['id'] : '' ?>">
                            <input type="hidden" name="issue_links_who_is[]" value="<?= !empty($issue_links) && $issue_links[$ii]['who_is'] == 1 ? '1' : '0' ?>">
                        </div>
                        <?php
                        if ($num > 1 && $i == $num) {
                            ?>
                        </div>
                        <?php
                    }
                } if ($num == 1) {
                    ?>
                    <div id="cloned-conns"></div>
                <?php }
                ?>
                <input type="hidden" name="orig_issue_links_up" value="<?= $original_updates_iss_links ?>">
                <button type="button" value="" id="connect-ticket" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span> <?= $this->lang_php['add_issue_link'] ?></button>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2" for="estimated"><?= $this->lang_php['estimated_time'] ?></label>
            <div class="col-sm-10">
                <div class="row">
                    <div class="col-sm-4">
                        <label><?= $this->lang_php['days'] ?>:</label>
                        <input type="text" class="form-control" value="<?= isset($_POST['estimated_days']) ? $_POST['estimated_days'] : '' ?>" name="estimated_days" placeholder="<?= $this->lang_php['in_days'] ?>">
                    </div>
                    <div class="col-sm-4">
                        <label><?= $this->lang_php['hours'] ?>:</label>
                        <input type="text" class="form-control" value="<?= isset($_POST['estimated_hours']) ? $_POST['estimated_hours'] : '' ?>" name="estimated_hours" placeholder="<?= $this->lang_php['in_hours'] ?>">
                    </div>
                    <div class="col-sm-4">
                        <label><?= $this->lang_php['minutes'] ?>:</label>
                        <input type="text" class="form-control" value="<?= isset($_POST['estimated_minutes']) ? $_POST['estimated_minutes'] : '' ?>" name="estimated_minutes" placeholder="<?= $this->lang_php['in_minutes'] ?>">
                    </div>
                </div>
            </div>
        </div>
        <?php if ($pro[0]['sync'] > 0 && $ticket_edit == false) { ?>
            <div class="form-group">
                <label class="control-label col-sm-2" for="estimated"></label>
                <div class="col-sm-10">
                    <div class="checkbox">
                        <label><input name="sendEmail" value="1" type="checkbox"> <?= $this->lang_php['send_email'] ?></label>
                    </div>
                    <div id="email_info">
                        <input type="text" name="receiver_email" class="form-control" placeholder="<?= $this->lang_php['email'] ?>">
                        <div class="checkbox">
                            <label><input name="isHTML" value="1" type="checkbox"> <?= $this->lang_php['send_in_html'] ?></label>
                        </div>
                        <label><?= $this->lang_php['attachments'] ?>:</label>
                        <input type="file" name="attachments[]" multiple />
                    </div>
                </div>
            </div>
        <?php } if ($ticket_edit === false) { ?>
            <input type="hidden" name="timecreated" value="<?= time() ?>">
            <input type="hidden" name="addedby" value="<?= $this->user_id ?>">
        <?php } else { ?>
            <input type="hidden" name="id" value="<?= isset($_POST['id']) ? $_POST['id'] : '' ?>">
        <?php } ?>
        <div class="form-group">        
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="setticket" class="btn btn-primary"><?= $ticket_edit == false ? $this->lang_php['create'] : $this->lang_php['update'] ?></button>
                <?php if ($ticket_edit === true) { ?>
                    <a href="<?= base_url($this->url . '/view/' . url_segment(3)) ?>" class="btn btn-default"><?= $this->lang_php['cancel'] ?></a>
                <?php } ?>
            </div>
        </div>
    </form>
</div>
<!-- Modal List Users -->
<div class="modal fade" id="listUsers" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?= $this->lang_php['add_watchers'] ?></h4>
            </div>
            <div class="modal-body">
                <div class="well list-users">
                    <div class="row list-filter">
                        <div class="col-md-2">
                            <div class="btn-group">
                                <a class="btn btn-default selector" title="select all"><i class="glyphicon glyphicon-unchecked"></i></a>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="input-group">
                                <input type="text" name="SearchInUsers" class="form-control" placeholder="<?= $this->lang_php['search'] ?>" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-search"></span></span>
                            </div>
                        </div>
                    </div>
                    <ul class="list-group">
                        <?php foreach ($users as $user) { ?>
                            <li class="list-group-item" data-watcher-id="<?= $user['id'] ?>"><i class="fa fa-user"></i> <?= $user['fullname'] ?></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= $this->lang_php['close'] ?></button>
                <button type="button" class="btn btn-primary" id="add-from-list" data-dismiss="modal"><?= $this->lang_php['add'] ?></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.date-pick').datepicker({
            calendarWeeks: true,
            autoclose: true,
            todayHighlight: true,
            format: 'dd.mm.yyyy'
        });

<?php if (isset($_POST['watchers']) && !empty($_POST['watchers'])) { ?>
            var str = $('[name = "watchers"]').val();
            str.split(', ').map(function (id) {
                var txt = $('#listUsers .list-users ul.list-group li[data-watcher-id="' + id + '"]').text();
                $('#listUsers .list-users ul.list-group li[data-watcher-id="' + id + '"]').remove();
                addUserToList(id, txt);
            });
    <?php
}
?>
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
            var my_ticket = <?= $ticket_edit == true ? $edittable : 0 ?>;
            if (jQuery.trim(search_q).length > 0) {
                $.ajax({
                    type: "POST",
                    url: "<?= base_url('find_ticket') ?>",
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
</script>
<script src="<?= base_url('assets/js/bootstrap-datepicker.min.js') ?>"></script>