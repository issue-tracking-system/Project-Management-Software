<?php
if(!defined('APPLICATION_LOADED') || !APPLICATION_LOADED) {
    die('No direct script access.');
}

if(isset($_POST['updatepage'])) {
    $space = $_POST['space'];
    unset($_POST['space']);
    $inserted_id = $this->updateWikiPage($_POST);
    if($inserted_id == false) {
        $this->set_alert($this->lang_php['page_update_error'], 'danger');
    } else {
        $this->set_alert($this->lang_php['page_was_updated'], 'success');
    }
    if(!isset($_POST['firstedit']) && $inserted_id != false) {
        $this->setWikiLog($this->user_id, $this->project_id, $this->lang_php['update_page'], url_segment(3), $space, $inserted_id);
    }
    if(url_segment(3)) {
        redirect(base_url('wiki/' . $project_name . '/display/' . $space . '?viewPageId=' . url_segment(3)));
    } else {
        redirect(base_url('wiki/' . $project_name . '/display/' . $space . '?viewPageId=' . $_POST['page_id']));
    }
}

if(isset($_POST['firstedit']) && !url_segment(3)) {
    $want_id = $this->addWikiPage($_POST);
    $get_template = $this->getWikiPageTemplate($_POST['page_template']);
    $this->setWikiLog($this->user_id, $this->project_id, $this->lang_php['create_page'], $want_id, $_POST['key_sp']);
} elseif(url_segment(3) && is_numeric(url_segment(3))) {
    $want_id = (int) url_segment(3);
} else {
    redirect(base_url('wiki/' . $project_name));
    exit;
}

$the_page = $this->getWikiPageEdit($want_id);
if(!in_array($GLOBALS['CONFIG']['PERMISSIONS']['WIKI']['EDIT_OTHER_PAGES'], $this->permissions) && $the_page['created_from'] != $this->user_id) {
    redirect(base_url('wiki/' . $project_name));
}

if($the_page === null) {
    redirect(base_url('wiki/' . $project_name));
    exit;
}

$this->title = $this->project_name . ' -' . $this->lang_php['title_edit_page'] . ' - ' . $the_page['title'];
?>
<script src="<?= base_url('assets/js/ckeditor/ckeditor.js') ?>"></script>
<div id="edit-wiki-page">
    <form action="" method="POST">
        <input type="hidden" name="updatepage" value="<?= isset($_POST['firstedit']) ? '0' : '1' ?>">
        <input type="hidden" name="page_id" value="<?= $want_id ?>">
        <input type="hidden" name="space" value="<?= $the_page['key_space'] ?>">
        <div class="form-group">
            <input type="text" class="form-control" name="title" value="<?= $the_page['title'] ?>">
        </div>
        <div class="form-group">
            <textarea name="content_edit_p" class="form-control"><?= isset($_POST['firstedit']) ? $get_template['content'] : $the_page['content'] ?></textarea>
            <script>
                CKEDITOR.replace('content_edit_p');
            </script>
        </div>
        <div class="form-group">
            <input type="submit" value="<?= $this->lang_php['update'] ?>" class="btn btn-primary">
            <a class="btn btn-danger" href="<?= base_url('wiki/' . $project_name . '/display/' . $the_page['key_space'] . '?viewPageId=' . $want_id) ?>"><?= $this->lang_php['close'] ?></a>
        </div>
    </form>
</div>