<?php

function loop_users($profiles, $edit = false, $arr_langs = null) {
    foreach ($profiles as $profile) {
        $social = unserialize($profile['social']);
        ?>
        <div class="col-md-6 list-users-templ">
            <div class="panel">
                <div class="panel-body p-t-10">
                    <div class="media-main">
                        <a class="pull-left" href="<?= base_url('profile/' . $profile['username']) ?>">
                            <img class="thumb-lg img-circle bx-s" src="<?= base_url(returnImageUrl($profile['image'])) ?>" alt="">
                        </a>
                        <div class="pull-right btn-group-sm">
                            <?php if ($edit === true) { ?>
                                <a href="javascript:void(0);" onclick="editUser(<?= $profile['id'] ?>)" class="btn btn-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <a href="?delete_user=<?= $profile['id'] ?>" class="btn btn-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Delete" onClick="return confirm('Are you sure want to delete this user permanently?')">
                                    <i class="fa fa-close"></i>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="info">
                            <h4><?= $profile['fullname'] ?></h4>
                            <p class="text-muted"><?= $profile['profession_name'] ?></p>
                        </div>
                        <div class="clearfix"></div>
                        <ul class="pull-left u-info">
                            <li><b><?= $arr_langs['registered'] ?>:</b> <?= $profile['registered'] !== null && $profile['registered'] != 0 ? date(SETTINGS_PROFILES_DATETYPE, $profile['registered']) : 'none' ?></li>
                            <li><b><?= $arr_langs['last_login'] ?>:</b> <?= $profile['last_login'] !== null && $profile['last_login'] != 0 ? date(SETTINGS_PROFILES_DATETYPE, $profile['last_login']) : 'none' ?></li>
                            <li><b><?= $arr_langs['last_active'] ?>:</b> <?= $profile['last_active'] !== null && $profile['last_active'] != 0 ? date(SETTINGS_PROFILES_DATETYPE, $profile['last_active']) : 'none' ?></li>
                        </ul>
                        <a href="<?= base_url('profile/' . $profile['username']) ?>" class="btn btn-default pull-right"><?= $arr_langs['preview'] ?></a>
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <ul class="social-links list-inline p-b-10">
                        <li>
                            <a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href="<?= $social['facebook'] ?>" data-original-title="Facebook" target="_blank"><i class="fa fa-facebook"></i></a>
                        </li>
                        <li>
                            <a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href="<?= $social['twitter'] ?>" data-original-title="Twitter" target="_blank"><i class="fa fa-twitter"></i></a>
                        </li>
                        <li>
                            <a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href="<?= $social['linkedin'] ?>" data-original-title="LinkedIn" target="_blank"><i class="fa fa-linkedin"></i></a>
                        </li>
                        <li>
                            <a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href="skype:<?= $social['skype'] ?>?add" data-original-title="Skype" target="_blank"><i class="fa fa-skype"></i></a>
                        </li>
                        <li>
                            <a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href="mailto:<?= $profile['email'] ?>" data-original-title="Message"><i class="fa fa-envelope-o"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <?php
    }
}
?>