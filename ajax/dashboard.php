<?php

$result = $this->changeTicketStatus($_POST['ticketid'], $_POST['tostatusid']);
if ($result !== false) {
    $st_id = $this->getStatuses($_POST['tostatusid']);
    $this->setTicketLog($_POST['userid'], $_POST['projectid'],  $this->lang_php['change_status_to'].' ' . $st_id[0]['name'], $_POST['ticketid']);
    echo 1;
} else {
    echo 0;
}
?>