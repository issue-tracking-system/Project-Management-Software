<?php
if (!defined('APPLICATION_LOADED') || !APPLICATION_LOADED) {
    die('No direct script access.');
}
$this->title = $_SERVER['HTTP_HOST'] . ' - ' . $this->lang_php['title_select_project'];
$this->description = 'This is your home page';

$projects = $this->getProjects();
?>
<link href='https://fonts.googleapis.com/css?family=Indie+Flower' rel='stylesheet' type='text/css'>
<div id="content">
    <div id="home" class="container-fluid">
        <h1><?= $this->lang_php['welcome_to'] ?> <?= $_SERVER['HTTP_HOST'] ?></h1>
        <div class="nav-top-content">
            <?php if (in_array($GLOBALS['CONFIG']['PERMISSIONS']['CREATE_PROJECT'], $this->permissions) || in_array($GLOBALS['CONFIG']['PERMISSIONS']['SETTINGS_PAGE'], $this->permissions)) { ?>
                <a href="javascript:void(0);" data-toggle="modal" data-target="#createModal" data-tooltip="tooltip" data-placement="bottom" title="<?= $this->lang_php['create_new_project'] ?>"><i class="fa fa-plus-square"></i></a>
            <?php } ?>
            <a href="<?= base_url('profile') ?>" data-tooltip="tooltip" data-placement="bottom" title="<?= $this->lang_php['view_profile'] ?>"><i class="fa fa-user"></i></a>
            <?php if (in_array($GLOBALS['CONFIG']['PERMISSIONS']['SETTINGS_PAGE'], $this->permissions)) { ?>
                <a href="<?= base_url('settings') ?>" data-tooltip="tooltip" data-placement="bottom" title="<?= $this->lang_php['open_settings'] ?>"><i class="fa fa-cog"></i></a>
            <?php } ?>
            <a href=" <?= base_url('documentation.html') ?>" target="_blank" data-tooltip="tooltip" data-placement="bottom" title="<?= $this->lang_php['read_docs'] ?>"><i class="fa fa-info-circle"></i></a>
            <a href="<?= base_url('logout') ?>" data-tooltip="tooltip" data-placement="bottom" title="<?= $this->lang_php['logout'] ?>"><i class="fa fa-sign-out"></i></a>
        </div>
        <?php if (!empty($projects)) { ?>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <div class="form-group project-selecter">
                        <select class="selectpicker form-control show-tick show-menu-arrow" id="sel-proj">
                            <?php foreach ($projects as $project) {
                                ?>
                                <option value="<?= $project['name'] ?>"><?= $project['name'] ?></option>
                            <?php }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row going">
                <div class="col-sm-6 text-right bg-choice right">
                    <a id="gotowiki" href="#">
                        <i class="fa fa-angle-left pull-left"></i> 
                        <?= $this->lang_php['go_to_wiki'] ?>
                    </a>
                </div>
                <div class="col-sm-6 text-left bg-choice left">
                    <a id="gototickets" href="#">
                        <?= $this->lang_php['go_to_tickets'] ?> 
                        <i class="fa fa-angle-right pull-right"></i>
                    </a>
                </div>
            </div>
        <?php } else { ?>
            <div class="col-sm-6 col-sm-offset-3">
                <div class="alert alert-danger"><?= $this->lang_php['dont_have_projects'] ?></div>
            </div>
        <?php } ?>
    </div>

    <!-- Modal for Project creation -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?= $this->lang_php['create_project'] ?></h4>
                </div>
                <div class="modal-body">
                    <form role="form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" value="0" name="update">
                        <div class="form-group">
                            <label for="edit-proj"><?= $this->lang_php['edit_projects'] ?>: <a href="javascript:void(0);" data-toggle="popover" data-trigger="hover" title="<?= $this->lang_php['edit_projects'] ?>" data-content="<?= $this->lang_php['edit_proj_help'] ?>"><span class="glyphicon glyphicon-question-sign"></span></a></label>
                            <select class="selectpicker form-control show-tick show-menu-arrow" id="edit-proj">
                                <option value=""></option>
                                <?php foreach ($projects as $project) {
                                    ?>
                                    <option value="<?= $project['id'] ?>" data-abbr="<?= $project['abbr'] ?>"><?= $project['name'] ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="title"><?= $this->lang_php['name'] ?>:</label>
                            <input type="text" class="form-control" name="title" placeholder="<?= $this->lang_php['name_regex'] ?>" value="" id="name">
                        </div>
                        <div class="form-group">
                            <label for="abbr"><?= $this->lang_php['abbrevation'] ?></label>
                            <input type="text" class="form-control" name="abbr" placeholder="<?= $this->lang_php['abbrevation_regex'] ?>" maxlength="3" value="" id="abbr">
                        </div>
                        <div class="checkbox check-sync">
                            <label><input type="checkbox" name="sync" value=""><?= $this->lang_php['sync_email_account'] ?></label>
                        </div>
                        <div id="sync">
                            <div class="form-group">
                                <label for="hostname">IMAP/POP3 <?= $this->lang_php['hostname'] ?>:</label>
                                <input type="text" class="form-control" name="hostname" placeholder="<?= $this->lang_php['example'] ?>: imap.gmail.com" value="" id="hostname">
                            </div>
                            <div class="form-group">
                                <label for="smtp_hostname">SMTP <?= $this->lang_php['hostname'] ?>:</label>
                                <input type="text" class="form-control" name="smtp_hostname" placeholder="<?= $this->lang_php['example'] ?>: smtp.gmail.com" value="" id="smtp_hostname">
                            </div>
                            <div class="form-group">
                                <label for="protocol"><?= $this->lang_php['protocol'] ?>:</label>
                                <select class="selectpicker form-control" onchange="setPort('imap')" id="protocol">
                                    <option value="imap">imap</option>
                                    <option value="pop3">pop3</option>
                                    <option value="nntp">nntp</option>
                                </select>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" onclick="setPort('imap')" name="_ssl" value="1"><?= $this->lang_php['ssl'] ?>(imap/pop3)</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" onclick="setPort('ssl')" name="smtp_ssl" value="1"><?= $this->lang_php['ssl'] ?>(smtp)</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" name="self_signed_cert" value="1"><?= $this->lang_php['self_signed_cert'] ?></label>
                            </div>
                            <div class="form-group">
                                <label for="port">IMAP/POP3 <?= $this->lang_php['port'] ?>:</label>
                                <input type="text" class="form-control" name="port" placeholder="<?= $this->lang_php['example'] ?>: 993" value="143" id="port">
                            </div>
                            <div class="form-group">
                                <label for="smtp_port">SMTP <?= $this->lang_php['port'] ?>:</label>
                                <input type="text" class="form-control" name="smtp_port" placeholder="<?= $this->lang_php['example'] ?>: 587" value="587" id="smtp_port">
                            </div>
                            <div class="form-group">
                                <label for="folder"><?= $this->lang_php['folder'] ?>:</label>
                                <input type="text" class="form-control" name="folder" placeholder="<?= $this->lang_php['example'] ?>: INBOX" value="" id="folder">
                            </div>
                            <div class="form-group">
                                <label for="uname_h"><?= $this->lang_php['username'] ?>:</label>
                                <input type="text" class="form-control" name="username" placeholder="<?= $this->lang_php['example'] ?>: kiril@gmail.com" value="" id="uname_h">
                            </div>
                            <div class="form-group">
                                <label for="pass_h"><?= $this->lang_php['pass'] ?>:</label>
                                <input type="text" class="form-control" name="password" placeholder="<?= $this->lang_php['example'] ?>: <?= $this->lang_php['pass'] ?>" value="" id="pass_h">
                            </div>
                            <a href="javascript:void(0);" id="check_conn" class="btn btn-info"><?= $this->lang_php['check_conn'] ?> <i class="fa fa-refresh" aria-hidden="true"></i></a>
                            <div id="check-errors" class="alert alert-danger"></div>
                            <div id="err_info" class="alert alert-danger"></div>
                        </div>
                    </form>
                    <div id="result"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= $this->lang_php['close'] ?></button>
                    <button type="button" onclick="setProject('add_project')" class="btn btn-primary"><?= $this->lang_php['create'] ?></button>
                </div>
            </div>
        </div>
    </div>
    <script src="<?= base_url('assets/js/project_add.js') ?>"></script>
    <script src="<?= base_url('assets/js/function.setPort.js') ?>"></script>
    <script>
    var urlsHome = {
        url_wiki: '<?= base_url('wiki/') ?>',
        url_tickets: '<?= base_url('tickets/') ?>',
        sync_url: '<?= base_url('sync_conn_check') ?>'
    };
    </script>
    <script src="<?= base_url('assets/js/home.js') ?>"></script>
</div>