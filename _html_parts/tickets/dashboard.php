<?php
if (!defined('APPLICATION_LOADED') || !APPLICATION_LOADED) {
    die('No direct script access.');
}

$this->title = $this->project_name . ' - ' . $this->lang_php['title_dashboard'];
$this->description = 'Manage your tickets';

$statuses = $this->getStatuses();
$priorities = $this->getPriority();
$types = $this->getTicketTypes();
$tickets = $this->getTickets(false, false, $_GET);

if (in_array($GLOBALS['CONFIG']['PERMISSIONS']['TICKETS']['ADD_EDIT_ALL_TICKETS'], $this->permissions)) {
    $can_change = 'all';
} elseif (in_array($GLOBALS['CONFIG']['PERMISSIONS']['TICKETS']['ADD_EDIT_MINE_TICKETS'], $this->permissions)) {
    $can_change = 'mine';
} else {
    $can_change = 'none';
}
?>
<link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-datepicker.min.css') ?>">
<div id="toTop" class="btn btn-info"><span class="glyphicon glyphicon-chevron-up"></span> <?= $this->lang_php['back_to_top'] ?></div>
<div id="dashboard">
    <div id="loading"></div>
    <h1><?= $this->lang_php['dashboard'] ?></h1>
    <hr>
    <div class="dash-options bordered text-center pull-left">
        <?= $this->lang_php['priorities'] ?>
        <ul class="priorities-list">
            <?php foreach ($priorities as $priority) { ?>
                <li><span style="border-left:5px solid <?= $priority['color'] ?>"><?= $priority['name'] ?></span></li>
            <?php } ?>
        </ul>
    </div>
    <form role="form" method="GET" id="filter-form" class="form-inline filter">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group fast-filter">
                    <input type="hidden" name="order_by" value="<?= isset($_GET['order_by']) && $_GET['order_by'] != null ? $_GET['order_by'] : 'ticket_priority.power' ?>">
                    <input type="hidden" name="order_type" value="<?= isset($_GET['order_type']) && $_GET['order_type'] != null ? $_GET['order_type'] : 'ASC' ?>">
                    <label><?= $this->lang_php['order_by'] ?>:</label>
                    <div class="dropdown inline">
                        <button type="button" class="btn btn-default dropdown-toggle ord-btn" data-toggle="dropdown">
                            <span class="innet-txt"><?= $this->lang_php['priority'] ?></span>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu ord-type">
                            <li class="<?= !isset($_GET['order_by']) || $_GET['order_by'] == 'ticket_priority.power' || $_GET['order_by'] == null ? 'active' : '' ?>"><a href="javascript:void(0)" data-order-by="ticket_priority.power"><?= $this->lang_php['priority'] ?></a></li>
                            <li class="<?= $_GET['order_by'] == 'timecreated' ? 'active' : '' ?>"><a href="javascript:void(0)" data-order-by="timecreated"><?= $this->lang_php['date_created'] ?></a></li>
                            <li class="<?= $_GET['order_by'] == 'lastupdate' ? 'active' : '' ?>"><a href="javascript:void(0)" data-order-by="lastupdate"><?= $this->lang_php['date_updated'] ?></a></li>
                            <li class="<?= $_GET['order_by'] == 'duedate' ? 'active' : '' ?>"><a href="javascript:void(0)" data-order-by="duedate"><?= $this->lang_php['due_date'] ?></a></li>
                        </ul>
                        <a href="javascript:void(0)" data-toggle="tooltip" data-order-type="<?= !isset($_GET['order_type']) || $_GET['order_type'] == 'ASC' || $_GET['order_type'] == null ? 'DESC' : 'ASC' ?>" title="<?= !isset($_GET['order_type']) || $_GET['order_type'] == 'ASC' || $_GET['order_type'] == null ? 'is ascending' : 'is descending' ?>"><span class="glyphicon <?= !isset($_GET['order_type']) || $_GET['order_type'] == 'ASC' || $_GET['order_type'] == null ? 'glyphicon-arrow-up' : 'glyphicon-arrow-down' ?>"></span></a>
                    </div>
                </div>
                <div class="checkbox fast-filter">
                    <label>
                        <input type="checkbox" name="assign-checkbox" autocomplete="off" <?= isset($_GET['assign-checkbox']) && $_GET['assign-checkbox'] != null ? 'checked' : '' ?>> <?= $this->lang_php['assigned_to_me'] ?>
                    </label>
                </div>
                <div class="checkbox fast-filter">
                    <label>
                        <input type="checkbox" name="watch-checkbox" autocomplete="off" <?= isset($_GET['watch-checkbox']) && $_GET['watch-checkbox'] != null ? 'checked' : '' ?>> <?= $this->lang_php['who_i_watch'] ?>
                    </label>
                </div>
                <div class="btn-more pull-right">
                    <button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#advanced-filter">
                        <i class="fa fa-bars"></i> <?= $this->lang_php['more'] ?>
                    </button>
                </div>
            </div>
        </div>

        <div id="advanced-filter" class="collapse">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group">
                        <label><?= $this->lang_php['search_for'] ?>:</label>
                        <div class="input-group">
                            <div class="input-group-btn search-panel">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="Everywhere">
                                    <span id="search_concept"><?= isset($_GET['serach_in']) && $_GET['serach_in'] != null ? $_GET['serach_in'] : $this->lang_php['find_in'] ?></span> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li class="<?= $_GET['serach_in'] == 'subject' ? 'active' : '' ?>"><a href="#subject"><?= $this->lang_php['title'] ?></a></li>
                                    <li class="<?= $_GET['serach_in'] == 'description' ? 'active' : '' ?>"><a href="#description"><?= $this->lang_php['description'] ?></a></li>
                                    <li class="divider"></li>
                                    <li class="<?= $_GET['serach_in'] == 'Everywhere' || $_GET['serach_in'] == null ? 'active' : '' ?>"><a href="#all"><?= $this->lang_php['everywhere'] ?></a></li>
                                </ul>
                            </div>
                            <input type="hidden" name="serach_in" value="<?= isset($_GET['serach_in']) ? $_GET['serach_in'] : '' ?>" id="search_param">         
                            <input type="text" class="form-control" name="search" value="<?= isset($_GET['search']) ? urldecode($_GET['search']) : '' ?>" placeholder="<?= $this->lang_php['search_term'] ?>...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?= $this->lang_php['created_between'] ?>:</label>
                        <div class="input-group">
                            <span class="input-group-addon" id="from"><?= $this->lang_php['from'] ?>:</span>
                            <input type="text" class="form-control date-pick" name="from_date" value="<?= isset($_GET['from_date']) ? $_GET['from_date'] : '' ?>" placeholder="Date" aria-describedby="from">
                            <span class="input-group-addon" id="to"><?= $this->lang_php['to'] ?>:</span>
                            <input type="text" class="form-control date-pick" name="to_date" value="<?= isset($_GET['to_date']) ? $_GET['to_date'] : '' ?>" placeholder="Date" aria-describedby="to">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type-select"><?= $this->lang_php['type'] ?>:</label>
                        <select class="selectpicker form-control show-tick show-menu-arrow" name="type_select" id="type-select">
                            <option value=""><?= $this->lang_php['select_type'] ?></option>
                            <?php foreach ($types as $type) { ?>
                                <option <?= isset($_GET['type_select']) && $_GET['type_select'] == $type['id'] ? 'selected' : '' ?> value="<?= $type['id'] ?>"><?= $type['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status-select"><?= $this->lang_php['status'] ?>:</label>
                        <select class="selectpicker form-control show-tick show-menu-arrow" name="status_select" id="status-select">
                            <option value="">Select Status</option>
                            <?php foreach ($statuses as $status) { ?>
                                <option <?= isset($_GET['status_select']) && $_GET['status_select'] == $status['id'] ? 'selected' : '' ?> value="<?= $status['id'] ?>"><?= $status['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="priority-select"><?= $this->lang_php['priority'] ?>:</label>
                        <select class="selectpicker form-control show-tick show-menu-arrow" name="priority_select" id="priority-select">
                            <option value="">Select Priority</option>
                            <?php foreach ($priorities as $priority) { ?>
                                <option <?= isset($_GET['priority_select']) && $_GET['priority_select'] == $priority['id'] ? 'selected' : '' ?> value="<?= $priority['id'] ?>"><?= $priority['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div>
                        <input type="submit" value="GO!" class="btn btn-primary">
                        <button type="button" class="btn btn-primary" onclick="reset()"><?= $this->lang_php['clear'] ?></button>
                        <a href="javascript:void(0)" class="btn btn-default" data-toggle="collapse" data-target="#advanced-filter"><?= $this->lang_php['close'] ?></a>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="clearfix"></div>
    <hr>
    <div class="table-responsive"> 
        <table class="table dashboard">
            <thead>
                <tr>
                    <?php
                    $i = 0;
                    $stat_order = array();
                    foreach ($statuses as $status) {
                        if (isset($tickets['nums']) && $tickets['nums'] !== null && (array_key_exists($status['name'], $tickets['nums']) && $tickets['nums'][$status['name']]['expired'] > 0) && strtolower($status['name']) != 'closed') {
                            $num_exp = $tickets['nums'][$status['name']]['expired'];
                            $exp_visible = '';
                        } else {
                            $num_exp = 0;
                            $exp_visible = 'none-me';
                        }
                        ?>
                        <th data-id="<?= $status['id'] ?>"><?= $status['name'] ?> <span class="num-issues"> <span class="the-num"><?= isset($tickets['nums']) && $tickets['nums'] !== null && array_key_exists($status['name'], $tickets['nums']) ? $tickets['nums'][$status['name']]['num'] : '0' ?></span> Issues <span class="expired <?= $exp_visible ?>"><span class="exp-the-num"><?= $num_exp ?></span> Expired</span></th>
                        <?php
                        $i++;
                        $stat_order[$i] = $status['id'];
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($tickets['tickets'])) {
                    $num_all_tickets = 0;
                    ?>
                    <tr>
                        <td colspan="<?= count((array) $statuses) ?>"><div class="alert alert-danger"><?= $this->lang_php['no_tickets'] ?>!</div></td>
                    </tr>
                    <?php
                } else {
                    $num_all_tickets = count($tickets['tickets']);
                    ?>
                    <tr>
                        <?php
                        $percent_width = 100 / count($stat_order);
                        foreach ($stat_order as $key => $val) {
                            ?>
                            <td data-status-id="<?= $val ?>" style="width:<?= $percent_width ?>%">
                                <?php
                                foreach ($tickets['tickets'] as $ticket) {
                                    if ($ticket['status'] == $val) {
                                        $disabled = false;
                                        if ($can_change == 'mine') {
                                            if ($ticket['addedby'] != $this->user_id && $ticket['assignee_id'] != $this->user_id) {
                                                $disabled = true;
                                            }
                                        } elseif ($can_change == 'none') {
                                            $disabled = true;
                                        }
                                        ?>
                                        <div class="issue-box <?= $disabled == true ? 'disable-sort-item' : '' ?>" data-have-status="<?= $val ?>" data-ticket-id="<?= $ticket['ticket_id'] ?>" style="border-left:5px solid <?= $ticket['color'] ?>">
                                            <div style="width:80%">
                                                <div><a href="<?= base_url('tickets/' . $this->project_name . '/view/' . $this->project_abbr . '-' . $ticket['ticket_id']) ?>"><?= $this->project_abbr . '-' . $ticket['ticket_id'] ?></a></div>
                                                <div class="ticket-subject"><span><?= word_limiter($ticket['subject'], 100) ?></span></div></div>
                                            <div><span class="timecreated" data-toggle="tooltip" title="<?= $this->lang_php['ticket_type'] ?>"><span class="glyphicon glyphicon-pushpin"></span> <?= $ticket['type_name'] ?></span></div>
                                            <div><span class="timecreated" data-toggle="tooltip" title="<?= $this->lang_php['assignee'] ?>: <?= $ticket['fullname'] ?>"><span class="glyphicon glyphicon-user"></span> <?= $ticket['fullname'] ?></span></div>
                                            <div><span class="timecreated" data-toggle="tooltip" title="Date Created"><span class="glyphicon glyphicon-time"></span> <?= date(TICKETS_DATE_TYPE, $ticket['timecreated']) ?></span></div>
                                            <div class="timer"><span class="<?= $ticket['duedate'] != 0 && $ticket['duedate'] < time() ? 'text-danger' : '' ?>" data-toggle="tooltip" title="<?= $this->lang_php['must_done_to'] ?>"><span class="glyphicon glyphicon-hourglass"></span> <?= $ticket['duedate'] != 0 ? date(TICKETS_DUEDATE_TYPE, $ticket['duedate']) : 'none' ?></span></div>
                                            <?php if ($disabled == false) { ?>
                                                <div class="visible-xs">
                                                    <span class="glyphicon glyphicon-sort"></span> 
                                                    <select onchange="ajax_stat_change(<?= $ticket['ticket_id'] ?>, this.value, 2, 1);">
                                                        <?php foreach ($statuses as $status) { ?>
                                                            <option value="<?= $status['id'] ?>" <?= $status['id'] == $ticket['status'] ? 'selected' : '' ?> name="<?= $status['name'] ?>"><?= $status['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            <?php } ?>
                                            <div class="profile-img">
                                                <?php if ($ticket['message_uid'] > 0 || $ticket['send'] > 0) { ?>
                                                    <a><i data-toggle="tooltip" title="<?= $ticket['message_from_email'] ?>" class="fa fa-3x fa-envelope-o" aria-hidden="true"></i></a>
                                                <?php } else { ?>
                                                    <a href="<?= base_url('profile/' . $ticket['username']) ?>"><img src="<?= base_url(returnImageUrl($ticket['assignee_image'])) ?>" class="profile-img img-circle" data-toggle="tooltip" title="Assignee: <?= $ticket['fullname'] ?>"></a>
                                                    <?php } ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </td>
                            <?php
                        }
                        ?>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script src="<?= base_url('assets/js/jquery-ui.min.css') ?>"></script>
<script src="<?= base_url('assets/js/jquery-ui.min.js') ?>"></script>
<script src="<?= base_url('assets/js/bootstrap-datepicker.min.js') ?>"></script>
<script>
var dashboard = {
    dashboard_url: '<?= base_url('dashboard') ?>',
    user_id: <?= $this->user_id ?>,
    project_id: <?= $this->project_id ?>,
    num_all_tickets: <?= $num_all_tickets ?>
};
</script>
<script src="<?= base_url('assets/js/dashboardPage.js') ?>"></script>