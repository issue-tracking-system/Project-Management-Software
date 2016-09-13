<?php
$result = $this->getTicketsSearch($_POST['find'], $_POST['my_ticket']);
if (!empty($result)) {
    foreach ($result as $res) {
        ?>
        <div class="list-group-item select-suggestion" data-conticket="<?= $res['id'] ?>"><?= $res['subject'] . ' / ' . $res['abbr'] . '-' . $res['ticket_id'] ?></div>
        <?php
    }
} else {
    echo 0;
}
?>