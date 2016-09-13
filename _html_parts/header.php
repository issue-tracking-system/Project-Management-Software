<?php
if(!defined('APPLICATION_LOADED') || !APPLICATION_LOADED) {
    die('No direct script access.');
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="<?= $description ?>">
        <?= $tags_inner ?>
        <title><?= $title ?></title>
        <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
        <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
        <link rel="stylesheet" href="<?= base_url('assets/css/font-awesome.min.css') ?>">
        <link rel="stylesheet" href="<?= base_url('assets/bootstrap-select-1.9.4/css/bootstrap-select.min.css') ?>">
        <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
        <script src="<?= base_url($this->lang_js) ?>"></script>
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body<?= $to_body ?>>
        <?php if(DEBUG_MODE === true) { ?>
            <div class="text-center bg-danger"><?= $this->lang_php['working_on'] ?> <b><?= $this->lang_php['dev_mode'] ?></b>! <a tabindex="0" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?= $this->lang_php['what_diference'] ?>" data-content="<?= $this->lang_php['dev_mode_info'] ?>"><?= $this->lang_php['more_info'] ?></a></div>
        <?php } ?>
        <div id="wrapper">