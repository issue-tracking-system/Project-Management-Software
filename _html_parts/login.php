<?php
if (!defined('APPLICATION_LOADED') || !APPLICATION_LOADED) {
    die('No direct script access.');
}
$this->title = $_SERVER['HTTP_HOST'] . ' - ' . COMPANY_NAME;
$this->description = 'Account login form, sign in to continue to the tickets';

if (isset($_POST['signin'])) {
    $result = $this->login($_POST['username'], $_POST['password']);
    if ($result !== false) {
        if (isset($_POST['remember']) && $_POST['remember'] !== null) {
            $this->setLogged(true, $result);
        } else {
            $this->setLogged(false, $result);
        }
        redirect('home');
    } else {
        $this->set_alert($this->lang_php['invalid_usr_and_pass'] . '!', 'danger', true);
    }
}

$login_image = $this->getLoginImage();
url_segment();
if (!isset($_GET['reset_code'])) {
    ?>
    <div class="container" id="login">   
        <?= $this->get_alert() ?>
        <div class="row top-20">
            <div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong class="pull-left"><?= $this->lang_php['sign_to_continue'] ?></strong>
                        <a href="http://pmticket.com" class="pull-right">
                            <img src="<?= base_url('assets/imgs/login_logo.png') ?>" alt="pmTicket.com">
                        </a>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <form role="form" action="" method="POST">
                            <fieldset>
                                <div class="row">
                                    <div class="center-block">
                                        <img class="profile-img" src="<?= $login_image != null ? base_url($GLOBALS['CONFIG']['IMAGELOGINUPLOADDIR'] . $login_image) : base_url('assets/imgs/login-triangle.png') ?>" alt="Login">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-10  col-md-offset-1 ">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="glyphicon glyphicon-user"></i>
                                                </span> 
                                                <input class="form-control" placeholder="<?= $this->lang_php['username_or_email'] ?>" name="username" value="<?= isset($_POST['username']) ? $_POST['username'] : '' ?>" type="text" autofocus>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="glyphicon glyphicon-lock"></i>
                                                </span>
                                                <input class="form-control" placeholder="<?= $this->lang_php['pass'] ?>" name="password" type="password" value="<?= isset($_POST['password']) ? $_POST['password'] : '' ?>">
                                            </div>
                                        </div>
                                        <div class="checkbox">
                                            <label><input type="checkbox" value="remember-me" name="remember"><?= $this->lang_php['remember_me'] ?></label>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-lg btn-primary btn-block blue-gradient" name="signin" value="<?= $this->lang_php['sign_in'] ?>">
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                    <div class="panel-footer ">
                        <?= $this->lang_php['forgot_pass'] ?> <a href="javascript:void(0);" data-toggle="modal" data-target="#passReset"><?= $this->lang_php['click_here'] ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Password Reset Modal -->
    <div class="modal fade" id="passReset" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?= $this->lang_php['pass_reset'] ?></h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" id="email_info" style="display:none;"></div>
                    <form class="form-horizontal" action="" method="POST" id="resetForm" role="form" style="margin-top:15px;">
                        <div class="form-group">
                            <label class="col-sm-2 control-label" style="text-align: left;">Email:</label>
                            <div class="col-sm-10">
                                <input class="form-control" type="text" value="" name="email">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= $this->lang_php['close'] ?></button>
                    <button type="button" class="btn btn-primary" onclick="validEmailCheck()"><?= $this->lang_php['reset'] ?></button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function validEmailCheck() {
            var reg = <?= $GLOBALS['CONFIG']['EMAILREGEX'] ?>;
            var email = $('[name="email"]').val();
            if (!reg.test(jQuery.trim(email))) {
                $("#email_info").empty().append(lang.invalid_email).show();
            } else {
                $.ajax({
                    type: "POST",
                    async: false,
                    url: "<?= base_url('resetemail') ?>",
                    data: {email: email}
                }).done(function (data) {
                    $("#email_info").empty().append(data).show();
                });
            }
        }
        $('#passReset').on('hidden.bs.modal', function (e) {
            $("#email_info").hide();
        })
    </script>

    <?php
} else {
    $result = $this->resetPassFromCode($_GET['reset_code']);
    if ($result === false) {
        redirect('login');
    } else {
        ?>
        <p class="text-center"><?= $this->lang_php['ready_reset_pass'] ?>: <b><?= $result ?></b></p>
        <?php
    }
}
?>
