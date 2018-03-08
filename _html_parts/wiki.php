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
    $this->url = 'wiki/' . $this->project_name;
    $navi_projects = $this->getNaviProjects($project_name);
}

if (empty($page_name))
    $page_name = 'home';

$check = $this->checkUserProjectPremission($pro[0]['id']);
if ($check == 0) {
    response(403);
}

$spaces = $this->getSpaces(null, $this->project_id);
$page_templates = $this->getPageTemplates();

$notifications = $this->getWikiNumNotifications();
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
                <a class="navbar-brand" href="#"><?= $this->lang_php['wiki'] ?></a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-menu-hamburger"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?= base_url('tickets/' . $project_name) ?>" title="Go to tickets"><i class="fa fa-ticket"></i> <?= $this->lang_php['tickets'] ?></a></li>
                            <li class="active"><a href="<?= base_url('wiki/' . $project_name) ?>" title="Go to wiki"><i class="fa fa-wikipedia-w"></i> <?= $this->lang_php['wiki'] ?></a></li>
                            <li class="divider" role="separator"></li>
                            <li><a href="<?= base_url('home') ?>"><i class="fa fa-home"></i> <?= $this->lang_php['home'] ?></a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= $this->lang_php['projects'] ?> <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-header"><?= $this->lang_php['current_project'] ?></li>
                            <li><a href="<?= base_url('wiki/' . $navi_projects['current']['name'] . '/' . $page_name) ?>"><?= $navi_projects['current']['name'] ?></a></li>
                            <li role="separator" class="divider"></li>
                            <li class="dropdown-header"><?= $this->lang_php['recent_projects'] ?></li>
                            <?php
                            if (!empty($navi_projects['recents'])) {
                                foreach ($navi_projects['recents'] as $recent_project) {
                                    ?>
                                    <li><a href="<?= base_url('wiki/' . $recent_project['name'] . '/' . $page_name) ?>"><?= $recent_project['name'] ?></a></li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                    </li>
                    <li <?= $page_name == false || $page_name == 'home' ? 'class="active"' : '' ?>><a href="<?= base_url('wiki/' . $project_name) ?>"><?= $this->lang_php['home'] ?></a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?= $this->lang_php['spaces'] ?> <b class="caret"></b></a>
                        <ul class="dropdown-menu drop-spaces">
                            <?php
                            if (!empty($spaces)) {
                                foreach ($spaces as $space) {
                                    ?>
                                    <li class="<?= url_segment(3) != false && $space['key_space'] == url_segment(3) ? 'active' : '' ?>"><a href="<?= base_url('wiki/' . $project_name . '/display/' . $space['key_space']) ?>"><img class="sp-logo" src="<?= base_url(returnSpaceImageUrl($space['image'])) ?>" alt="logo"> <?= $space['name'] ?></a></li>
                                    <?php
                                }
                            } else {
                                ?>
                                <li class="dropdown-header"><?= $this->lang_php['no_spaces'] ?></li>
                            <?php } ?>
                            <?php if (in_array($GLOBALS['CONFIG']['PERMISSIONS']['WIKI']['ADD_NEW_SPACES'], $this->permissions)) { ?>
                                <li class="divider" role="separator"></li>
                                <li><a href="<?= base_url('wiki/' . $project_name . '/createspace') ?>"><i class="fa fa-plus"></i> <?= $this->lang_php['create_new_one'] ?></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php if (in_array($GLOBALS['CONFIG']['PERMISSIONS']['WIKI']['ADD_NEW_PAGES'], $this->permissions)) { ?>
                        <li><a href="#" class="btn btn-nav add-wiki-page" data-toggle="modal" data-target="#create"><?= $this->lang_php['create'] ?></a></li>
                    <?php } ?>
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
                <div class="pull-right wiki-search-box">
                    <form class="navbar-form form-s" role="search">
                        <div class="input-group">
                            <input type="text" autocomplete="off" class="form-control" value="" placeholder="<?= $this->lang_php['search'] ?>" name="wiki_search">
                            <div class="input-group-btn">
                                <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                            </div>
                        </div>
                        <div class="list-group" id="wiki-query-result">

                        </div>
                        <img src="<?= base_url('assets/imgs/settings-search-spinner.gif') ?>" alt="loading" class="s-loading">
                    </form>
                </div>
            </div>
        </div>
    </nav>
    <script>
        var urlsWiki = {
            notifications_wiki: '<?= base_url('notifications_wiki') ?>',
            project_id: <?= $this->project_id ?>,
            user_id: <?= $this->user_id ?>
        };
    </script>
    <script src="<?= base_url('assets/js/wikiNotifications.js') ?>"></script>
    <div class="container-fluid">
        <?php
        echo $this->get_alert();
        if (file_exists('_html_parts/wiki/' . strtolower($page_name) . '.php')) {
            include '_html_parts/wiki/' . strtolower($page_name) . '.php';
        } else {
            ?>
            <div class="alert alert-danger"><?= $this->lang_php['page_not_exists'] ?></div>
            <?php
        }
        ?>
    </div>
    <!-- Modal Create Page -->
    <div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?= $this->lang_php['create_a_page'] ?></h4>
                </div>
                <div class="modal-body">
                    <?php if (!empty($spaces)) { ?>
                        <div class="alert alert-danger" id="reg-errors" style="display:none;"></div>
                        <form method="POST" id="page-create-form" action="<?= base_url('wiki/' . $project_name . '/editpage') ?>">
                            <input type="hidden" value="1" name="firstedit">
                            <input type="hidden" value="1" name="page_template">
                            <div class="wizard-create-page">
                                <div class="wizard-inner">
                                    <div class="connecting-line"></div>
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li role="presentation" class="active">
                                            <a href="#step-s-space" data-toggle="tab" aria-controls="step1" role="tab" title="Step 1">
                                                <span class="round-tab">
                                                    <i class="glyphicon glyphicon-folder-open"></i>
                                                </span>
                                            </a>
                                        </li>
                                        <li role="presentation" class="disabled">
                                            <a href="#step-c-page" data-toggle="tab" aria-controls="step2" role="tab" title="Step 2">
                                                <span class="round-tab">
                                                    <i class="glyphicon glyphicon-pencil"></i>
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="step-s-space">
                                    <label><?= $this->lang_php['select_space'] ?></label>
                                    <?php if (!empty($spaces)) { ?>
                                        <select name="for_space" id="for_space" class="selectpicker form-control">
                                            <?php
                                            foreach ($spaces as $space) {
                                                ?>
                                                <option value="<?= $space['id'] ?>"><?= $space['name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } else { ?>
                                        No spaces available! <a href="<?= base_url('') ?>">Create</a> first!
                                    <?php } ?>
                                    <hr>
                                    <div id="parent-page">
                                        <label><?= $this->lang_php['parent_page'] ?>:</label>
                                        <input type="hidden" name="sub_for" value="0">
                                        <input type="text" name="suggesions" value=""  autocomplete="off" class="form-control">
                                        <div class="list-group" id="suggestions">

                                        </div>
                                        <a href="javascript:void(0);" id="remove-parent">
                                            <span class="glyphicon glyphicon-remove"></span>
                                        </a>
                                    </div>
                                    <hr>
                                    <label><?= $this->lang_php['choose_template'] ?></label>
                                    <div class="row">
                                        <?php foreach ($page_templates as $template) { ?>
                                            <div class="col-sm-6" style="margin-bottom:20px;">
                                                <div class="template-box <?= $template['id'] == 1 ? 'active' : '' ?>" data-cat-id="<?= $template['id'] ?>">
                                                    <i class="fa fa-file-text-o"></i> <?= $template['name'] ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="step-c-page">
                                    <label><?= $this->lang_php['page_name'] ?>:</label>
                                    <input type="text" name="title" class="form-control">
                                </div>
                            </div>
                        </form>
                    <?php } else { ?>
                        <p><?= $this->lang_php['no_spaces'] ?> <a href="<?= base_url('wiki/' . $project_name . '/createspace') ?>">Create</a> new one!</p>
                    <?php } ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= $this->lang_php['close'] ?></button>
                    <button type="button" class="btn btn-primary prev-step" style="display:none;"><?= $this->lang_php['previous'] ?></button>
                    <button type="button" class="btn btn-primary next-step"><?= $this->lang_php['next'] ?></button>
                    <button type="button" class="btn btn-primary" id="create-page" style="display:none;"><?= $this->lang_php['create'] ?></button>
                </div>
            </div>
        </div>
    </div>
    <script>
        var generalWiki = {
            parent_suggestions: '<?= base_url('parent_suggestions') ?>',
            project_id: <?= $this->project_id ?>,
            wiki_search: '<?= base_url('wiki_search') ?>',
            project_name: '<?= $project_name ?>'
        };
    </script>
    <script src="<?= base_url('assets/js/wiki.js') ?>"></script>
</div>