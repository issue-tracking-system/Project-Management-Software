<?php
if (!defined('APPLICATION_LOADED') || !APPLICATION_LOADED) {
    die('No direct script access.');
}
$project_name = $this->project_name = url_segment(1);
$page_name = url_segment(2);

$pro = $this->getProjects($project_name);
if (empty($project_name) || empty($pro)) {
    response(404);
} else {
    $this->url = 'tickets/' . $this->project_name;
    $navi_projects = $this->getNaviProjects($project_name);
}

$check = $this->checkUserProjectPremission($pro[0]['id']);
if ($check == 0) {
    response(403);
}

if (empty($page_name))
    $page_name = 'activity';

$notifications = $this->getTicketNumNotifications();
$notif_num = $notifications < MAX_NUM_NOTIF ? $notifications : MAX_NUM_NOTIF . '+';
?>
<div id="content">
    <nav class="navbar navbar-static-top tickets-wiki" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="#"><?= $this->lang_php['tickets'] ?></a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-menu-hamburger"></span></a>
                        <ul class="dropdown-menu">
                            <li class="active"><a href="<?= base_url('tickets/' . $project_name) ?>" title="<?= $this->lang_php['go_to_tickets'] ?>"><i class="fa fa-ticket"></i> <?= $this->lang_php['tickets'] ?></a></li>
                            <li><a href="<?= base_url('wiki/' . $project_name) ?>" title="<?= $this->lang_php['go_to_wiki'] ?>"><i class="fa fa-wikipedia-w"></i> <?= $this->lang_php['wiki'] ?></a></li>
                            <li class="divider" role="separator"></li>
                            <li><a href="<?= base_url('home') ?>"><i class="fa fa-home"></i> <?= $this->lang_php['home'] ?></a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= $this->lang_php['projects'] ?> <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-header"><?= $this->lang_php['current_project'] ?></li>
                            <li><a href="<?= base_url('tickets/' . $navi_projects['current']['name'] . '/' . $page_name) ?>"><?= $navi_projects['current']['name'] ?></a></li>
                            <li role="separator" class="divider"></li>
                            <li class="dropdown-header"><?= $this->lang_php['recent_projects'] ?></li>
                            <?php
                            if (!empty($navi_projects['recents'])) {
                                foreach ($navi_projects['recents'] as $recent_project) {
                                    ?>
                                    <li><a href="<?= base_url('tickets/' . $recent_project['name'] . '/' . $page_name) ?>"><?= $recent_project['name'] ?></a></li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                    </li>
                    <li <?= $page_name == false || $page_name == 'activity' ? 'class="active"' : '' ?>><a href="<?= base_url($this->url) ?>"><?= $this->lang_php['activity'] ?></a></li>
                    <?php if (in_array($GLOBALS['CONFIG']['PERMISSIONS']['TICKETS']['ADD_EDIT_MINE_TICKETS'], $this->permissions)) {
                        ?>
                        <li <?= $page_name == 'newissue' ? 'class="active"' : '' ?>><a href="<?= base_url($this->url . '/newissue') ?>"><?= $this->lang_php['new_issue'] ?></a></li>
                    <?php } ?>
                    <li <?= $page_name == 'dashboard' ? 'class="active"' : '' ?>><a href="<?= base_url($this->url . '/dashboard') ?>"><?= $this->lang_php['dashboard'] ?></a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown mine-dropdown"> 
                        <a class="dropdown-toggle" aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" href="#">
                            <img src="<?= base_url(returnImageUrl($this->image)) ?>" alt="profile" class="profile-img">
                            <?= $this->fullname ?>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="<?= base_url('profile') ?>"><i class="fa fa-user"></i> <?= $this->lang_php['profile'] ?></a></li>
                            <li>
                                <a href="javascript:void(0);" id="notifPopover" data-toggle="popover">
                                    <i class="fa fa-flag-o"></i> <?= $this->lang_php['notifications'] ?>
                                    <span class="badge"><?= $notif_num ?></span>
                                </a>
                            </li>
                            <li class="divider" role="separator"></li>
                            <li><a href="<?= base_url('logout') ?>"><i class="fa fa-sign-out"></i> <?= $this->lang_php['logout'] ?></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <script>
    var urlsTickets = {
        notifications_tickets: '<?= base_url('notifications_tickets') ?>',
        project_id: <?= $this->project_id ?>,
        user_id: <?= $this->user_id ?>
    };
    </script>
    <script src="<?= base_url('assets/js/ticketsNotifications.js') ?>"></script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3">
                <?php
                $active_timers = $this->getStartetTimers();
                $num_timers = count($active_timers);
                ?>
                <div class="started-timers bordered">
                    <p class="text-center">
                        <?php if ($num_timers != 0) { ?>
                            <?= $this->lang_php['you_have'] ?> <?= $num_timers ?> <?= $this->lang_php['active_timer'] ?> <?= $num_timers > 1 ? $this->lang_php['s'] : '' ?>!
                        <?php } else { ?>
                            <?= $this->lang_php['no_started_timers'] ?>
                        <?php } ?>
                    </p>
                    <?php
                    foreach ($active_timers as $timer) {
                        ?>
                        <a href="<?= base_url($this->url . '/view/' . $this->project_abbr . '-' . $timer['ticket_id']) ?>" data-toggle="tooltip" title="<?= $this->lang_php['started_on'] ?>: <?= date(TICKETS_DATE_TYPE, $timer['started']) ?>, <?= $this->lang_php['current_status'] ?>: <?= $timer['status'] ?>" class="btn btn-sq-sm btn-warning" data-timer-indicator="<?= $this->project_abbr . '-' . $timer['ticket_id'] ?>">
                            <i class="glyphicon glyphicon-time"></i>
                            <div class="text-center"><?= $this->project_abbr . '-' . $timer['ticket_id'] ?></div>
                        </a>
                    <?php }
                    ?>
                </div>
            </div>
        </div>
        <?php
        echo $this->get_alert();
        if (file_exists('_html_parts/tickets/' . strtolower($page_name) . '.php')) {
            include '_html_parts/tickets/' . strtolower($page_name) . '.php';
        } else {
            ?>
            <div class="alert alert-danger"><?= $this->lang_php['page_not_exists'] ?></div>
            <?php
        }
        ?>
    </div>
</div>