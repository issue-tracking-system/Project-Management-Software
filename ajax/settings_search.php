<?php
$result = $this->getSettingsSearchResults($_POST['find']);
if(!empty($result)) {
    foreach ($result as $res) {
        if($res['what'] == 1) {
            ?>
            <a href="<?= base_url('wiki/' . $res['name'] . '/display/' . $res['key_space'] . '?viewPageId=' . $res['page_id']) ?>" class="list-group-item select-suggestion"><i class="fa fa-wikipedia-w"></i> <?= $res['title'] ?></a>
            <?php
        } else {
            ?>
            <a href="<?= base_url('tickets/' . $res['name'] . '/view/' . $res['abbr'] . '-' . $res['ticket_id']) ?>" class="list-group-item select-suggestion"><i class="fa fa-ticket"></i> <?= $res['title'] ?></a>
            <?php
        }
    }
} else {
    echo 0;
}
?>