<?php
if(!defined('APPLICATION_LOADED') || !APPLICATION_LOADED) {
    die('No direct script access.');
}

$this->title = $this->project_name . ' - ' . $this->lang_php['title_wiki_home'];

$stream = $this->getWikiActivityStream($this->project_id);
require 'templates/activitystream.php';
?>
<div id="wiki-home">
    <h1><?= $this->lang_php['home'] ?></h1>
    <div class="row">
        <div class="col-sm-4 list-category">
            <?php
            if(!empty($spaces)) {
                ?>
                <h3 class="title"><?= $this->lang_php['project_spaces'] ?></h3>
                <div class="list-group">
                    <?php
                    foreach ($spaces as $space) {
                        ?>
                        <a href="<?= base_url('wiki/' . $project_name . '/display/' . $space['key_space']) ?>" class="list-group-item"><div class="truncate pull-left"><?= $space['name'] ?></div><span class="badge"><?= $space['num_pages'] ?> pages</span></a>

                        <?php
                    }
                    ?>
                </div>
                <?php
            } else {
                ?>
                <?= $this->lang_php['no_spaces_create_first'] ?>
            <?php } ?>
        </div>
        <div class="col-sm-8">
            <div class="panel panel-info panel-activity">
                <div class="panel-heading"><?= $this->lang_php['activity'] ?></div>
                <div class="panel-body">
                    <?php
                    if(!empty($stream)) {
                        activityForeach($stream, 1);
                        ?>
                        <div id="w-ajax"></div>
                        <a href="javascript:void(0)" onClick="showMore(<?= $this->project_id ?>)" class="bordered text-center show-more"><?= $this->lang_php['show_more'] ?> <i class="fa fa-chevron-circle-down"></i></a>
                            <?php
                        } else {
                            ?>
                            <?= $this->lang_php['no_activity'] ?>
                        <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/js/wikiHome.js') ?>"></script>