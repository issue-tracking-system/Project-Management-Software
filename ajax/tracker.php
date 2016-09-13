<?php

$result = $this->startTracking($_POST['status'], $_POST['ticket_id'], $_POST['user_id'], $_POST['project_id']);
if ($result !== false) {
    
} else {
    echo 0;
    //log error
}
?>