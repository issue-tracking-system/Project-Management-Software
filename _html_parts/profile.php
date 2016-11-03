<?php
if (!defined('APPLICATION_LOADED') || !APPLICATION_LOADED) {
    die('No direct script access.');
}

$this->title = $_SERVER['HTTP_HOST'] . ' - ' . $this->lang_php['title_profile_view'];

$error = false;
if (url_segment(1) === false || url_segment(1) == null) {
    $mine = true;
    $usr = $this->username;
} else {
    $mine = false;
    $usr = url_segment(1);
    $usr_info = $this->getUsers(0, 0, null, $usr);
    if (empty($usr_info)) {
        $error = true;
    }
    if ($usr_info[0]['username'] == $this->username) {
        $mine = true;
    }
}
$projects = $this->getProjects();
$abbreviations = getFolderLanguages();

if (isset($_POST['update'])) {
    $img_name = $this->uploadImage($GLOBALS['CONFIG']['IMAGESUSERSUPLOADDIR']);
    if ($img_name !== null) {
        $_POST['image'] = $img_name;
    }
    $result = $this->updateUser($_POST);
    if ($result === true) {
        $this->set_alert($this->lang_php['user_updated'], 'success');
    } else {
        $this->set_alert($this->lang_php['user_update_err'], 'danger');
    }
    redirect('profile?mine-tab=settings');
}
?>
<div id="content">
    <div class="container" id="profile-view">
        <nav class="navbar navbar-default main-profile-nav">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#"><?= $_SERVER['HTTP_HOST'] ?></a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="<?= $mine === true && !isset($_GET['sub-page']) ? 'active' : '' ?>"><a href="<?= base_url('profile') ?>"><?= $this->lang_php['profile'] ?></a></li>
                        <li class="<?= isset($_GET['sub-page']) && $_GET['sub-page'] == 'list-users' ? 'active' : '' ?>"><a href="<?= base_url('profile?sub-page=list-users') ?>"><?= $this->lang_php['list_users'] ?></a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="<?= base_url('home') ?>"><?= $this->lang_php['home'] ?></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= $this->lang_php['goto'] ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li class="dropdown-header"><?= $this->lang_php['tickets'] ?></li>
                                <?php foreach ($projects as $project) { ?>
                                    <li><a href="<?= base_url('tickets/' . $project['name']) ?>"><?= $project['name'] ?></a></li>
                                <?php } ?>
                                <li role="separator" class="divider"></li>
                                <li class="dropdown-header"><?= $this->lang_php['wiki'] ?></li>
                                <?php foreach ($projects as $project) { ?>
                                    <li><a href="<?= base_url('wiki/' . $project['name']) ?>"><?= $project['name'] ?></a></li>
                                <?php } ?>
                            </ul>
                        </li>
                        <li><a href="<?= base_url('logout') ?>"><?= $this->lang_php['logout'] ?></a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <?php if ($error === true) { ?>
            <div class="alert alert-danger"><?= $this->lang_php['profile_not_found'] ?></div>
        <?php } elseif (!isset($_GET['sub-page']) && $mine == true) { ?>
            <div class="row profile">
                <div class="col-md-3">
                    <div class="profile-sidebar">
                        <div class="profile-userpic">
                            <img src="<?= base_url(returnImageUrl($this->image)) ?>" class="img-responsive" alt="">
                        </div>
                        <div class="profile-usertitle">
                            <div class="profile-usertitle-name">
                                <?= $this->fullname ?>
                            </div>
                            <div class="profile-usertitle-job">
                                <?= $this->profession ?>
                            </div>
                        </div>
                        <div class="profile-usermenu">
                            <ul class="nav">
                                <li class="<?= !isset($_GET['mine-tab']) || $_GET['mine-tab'] == '' ? 'active' : '' ?>"><a href="<?= base_url('profile') ?>"><i class="glyphicon glyphicon-home"></i><?= $this->lang_php['overview'] ?> </a></li>
                                <li class="<?= isset($_GET['mine-tab']) && $_GET['mine-tab'] == 'settings' ? 'active' : '' ?>"><a href="<?= base_url('profile?mine-tab=settings') ?>"><i class="glyphicon glyphicon-user"></i><?= $this->lang_php['account_settings'] ?> </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="profile-content">
                        <?php
                        if (!isset($_GET['mine-tab']) || $_GET['mine-tab'] == '') {
                            $stream_t = $this->getTicketsActivityStream($this->user_id, 0);
                            $stream_w = $this->getWikiActivityStream(0);
                            require 'templates/activitystream.php';
                            ?>
                            <div class="tabbable-panel">
                                <div class="tabbable-line">
                                    <ul class="nav nav-tabs ">
                                        <li class="active"><a href="#activity-tickets" data-toggle="tab"><?= $this->lang_php['activity_tickets'] ?></a></li>
                                        <li><a href="#activity-wiki" data-toggle="tab"><?= $this->lang_php['activity_wiki'] ?></a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="activity-tickets">
                                            <p>
                                                <?= $stream_t['mine'] !== null ? activityForeach($stream_t['mine']) : 'No Activity' ?>
                                            <div id="mine-ajax"></div>
                                            </p>
                                            <p>
                                                <a href="javascript:void(0)" class="btn btn-success" onClick="showMore(<?= $this->user_id ?>, <?= $this->project_id ?>, true)"><?= $this->lang_php['show_more'] ?></a>
                                            </p>
                                        </div>
                                        <div class="tab-pane" id="activity-wiki">
                                            <p>
                                                <?= !empty($stream_w) ? activityForeach($stream_w, 1) : $this->lang_php['no_activity'] ?>
                                            </p>
                                            <p>
                                                <a href="javascript:void(0)" class="btn btn-success" onClick="showMoreWiki(0)"><?= $this->lang_php['show_more'] ?></a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script src="<?= base_url('assets/js/profileFunctions.js') ?>"></script>
                            <?php
                        } elseif (isset($_GET['mine-tab']) && $_GET['mine-tab'] == 'settings') {
                            $_POST = $this->social;
                            $_POST['fullname'] = $this->fullname;
                            $_POST['email'] = $this->email;
                            $_POST['email_notif'] = $this->email_notif;
                            ?>
                            <div id="success-alert"><?= $this->get_alert() ?></div>
                            <div class="alert alert-danger" id="reg-errors" style="display:none;"></div>
                            <form class="form-horizontal" role="form" method="POST" action="" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><?= $this->lang_php['pass'] ?> *</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" id="pwd" name="password" type="text" value="">
                                        <span><?= $this->lang_php['pass_strenght'] ?>:</span>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0;">
                                            </div>
                                        </div>
                                        <button type="button" id="GeneratePwd" class="btn btn-default"><?= $this->lang_php['gen_pass'] ?></button> 
                                        <p id="demo"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><?= $this->lang_php['full_name'] ?> *</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" name="fullname" type="text" value="<?= isset($_POST['fullname']) ? $_POST['fullname'] : '' ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <img src="" alt="none" style="display:none;" id="cover">
                                    <label class="col-sm-2 control-label"><?= $this->lang_php['image'] ?></label>
                                    <div class="col-sm-10">
                                        <input type="file"  name="image" id="fileToUpload">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Facebook <i class="fa fa-facebook-official"></i></label>
                                    <div class="col-sm-10">
                                        <input class="form-control" name="facebook" type="text" value="<?= isset($_POST['facebook']) ? $_POST['facebook'] : '' ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Twitter <i class="fa fa-twitter-square"></i></label>
                                    <div class="col-sm-10">
                                        <input class="form-control" name="twitter" type="text" value="<?= isset($_POST['twitter']) ? $_POST['twitter'] : '' ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Linked In <i class="fa fa-linkedin-square"></i></label>
                                    <div class="col-sm-10">
                                        <input class="form-control" name="linkedin" type="text" value="<?= isset($_POST['linkedin']) ? $_POST['linkedin'] : '' ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Skype <i class="fa fa-skype"></i></label>
                                    <div class="col-sm-10">
                                        <input class="form-control" name="skype" type="text" value="<?= isset($_POST['skype']) ? $_POST['skype'] : '' ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Email <i class="fa fa-envelope"></i></label>
                                    <div class="col-sm-10">
                                        <input class="form-control" name="email" type="text" value="<?= isset($_POST['email']) ? $_POST['email'] : '' ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><?= $this->lang_php['language'] ?></label>
                                    <div class="col-sm-10">
                                        <select name="lang" class="form-control" <?= $this->user_lang == null ? 'disabled' : '' ?>>
                                            <?php foreach ($abbreviations as $abbreviature) { ?>
                                                <option <?= $abbreviature == $this->user_lang ? 'selected' : '' ?> value="<?= $abbreviature ?>"><?= $abbreviature ?></option>
                                            <?php } ?>
                                        </select>
                                        <div class="checkbox">
                                            <label><input type="checkbox" value="" id="default_lang" <?= $this->user_lang == null ? 'checked=""' : '' ?>><?= $this->lang_php['use_site_default'] ?></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-10 col-sm-offset-2">
                                        <div class="checkbox">
                                            <label><input type="checkbox" <?= isset($_POST['email_notif']) && $_POST['email_notif'] == 1 ? 'checked' : '' ?> name="email_notif" value="1"><?= $this->lang_php['receive_email_on_notif'] ?></label>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="update_user" value="<?= $this->user_id ?>">
                                <input type="submit" class="btn btn-info pull-right" name="update" onclick="return validateForm()" value="<?= $this->lang_php['update_info'] ?>">
                            </form>
                            <div class="clearfix"></div>
                            <script type="text/javascript" src="<?= base_url('assets/js/zxcvbn.js') ?>"></script>
                            <script type="text/javascript" src="<?= base_url('assets/js/zxcvbn_bootstrap3.js') ?>"></script>
                            <script type="text/javascript" src="<?= base_url('assets/js/pGenerator.jquery.js') ?>"></script>
                            <script>
                            var profileValidator = {
                                fullname_regex: <?= $GLOBALS['CONFIG']['FULLNAMEREGEX'] ?>,
                                email_regex: <?= $GLOBALS['CONFIG']['EMAILREGEX'] ?>
                            };
                            </script>
                            <script src="<?= base_url('assets/js/profileValidator.js') ?>"></script>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php
        } elseif ($mine === false) {
            $usr_preview = $this->getUsers(0, 0, null, $usr);
            ?>
            <div class="row">
                <div class="col-xs-6 col-xs-offset-3">
                    <div class="profile-sidebar">
                        <div class="profile-userpic">
                            <img src="<?= base_url(returnImageUrl($usr_preview[0]['image'])) ?>" class="img-responsive" alt="">
                        </div>
                        <div class="profile-usertitle">
                            <div class="profile-usertitle-name">
                                <?= $usr_preview[0]['fullname'] ?>
                            </div>
                            <div class="profile-usertitle-job">
                                <?= $usr_preview[0]['profession_name'] ?>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
            <?php
        } elseif (isset($_GET['sub-page']) && $mine == true) {

            require 'templates/list_users.php';

            $search = isset($_GET['find-user']) ? $_GET['find-user'] : null;
            $num_results = $this->getCountProfiles($search);

            $num_pages = ceil($num_results / RESULT_LIMIT_PROFILES);
            $pg = isset($_GET['pg']) ? intval($_GET['pg']) : 0;
            $pg = min($pg, $num_pages);
            $pg = max($pg, 0);
            $from = RESULT_LIMIT_PROFILES * ($pg);

            $profiles = $this->getUsers($from, RESULT_LIMIT_PROFILES, $search);

            if (isset($_GET['find-user']) && $_GET['find-user'] != '') {
                $query_string = '&find-user=' . $_GET['find-user'];
            } else {
                $query_string = '';
            }
            $arr_lang = array(
                'registered' => $this->lang_php['registered'],
                'last_login' => $this->lang_php['last_login'],
                'last_active' => $this->lang_php['last_active'],
                'preview' => $this->lang_php['preview']
            );
            ?> 
            <div class="row">
                <div class="col-lg-8">
                    <div class="panel panel-default">
                        <div class="panel-body p-t-0">
                            <form method="GET" action="">
                                <input type="hidden" name="sub-page" value="list-users">
                                <div class="input-group">
                                    <input type="text" id="example-input1-group2" value="<?= isset($_GET['find-user']) ? urldecode($_GET['find-user']) : '' ?>" name="find-user" class="form-control" placeholder="<?= $this->lang_php['search'] ?>">
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn btn-effect-ripple btn-primary"><i class="fa fa-search"></i></button>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="profiles-boxes">
                <div class="row">
                    <?= loop_users($profiles, null, $arr_lang); ?>
                </div>
            </div>
            <?php
            $f_u = '';
            if (isset($_GET['find-user'])) {
                $f_u = '&find-user=' . $_GET['find-user'];
            }
            echo paging('profile', '&sub-page=list-users' . $f_u, $num_results, $pg, RESULT_LIMIT_PROFILES);
            ?>
        <?php } ?>
    </div>
    <script>
        $('body').css("background-color", "#f1f1f1");
    </script>