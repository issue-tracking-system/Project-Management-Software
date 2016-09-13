<?php
$result = $this->getSuggestions($_POST['find'], $_POST['proj_id'], $_POST['space']);
if(!empty($result)) {
    foreach ($result as $res) {
        ?>
        <a href="javascript:void(0);" class="list-group-item select-suggestion" onclick="addParent('<?= $res['title'] ?>', <?= $res['id'] ?>, <?= $_POST['space'] ?>)"><?= $res['title'] ?></a>
        <?php
    }
} else {
    echo 0;
}
?>