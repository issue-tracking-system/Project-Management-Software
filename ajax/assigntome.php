<?php

$result = $this->assignToMe($_POST['ticket_id'], $_POST['assigntome']);
if ($result !== false) {
    echo 1;
} else {
    echo 0;
    //log error
}
?>