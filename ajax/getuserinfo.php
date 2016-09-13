<?php

if (isset($_POST['uid'])) {
    $value = (int) $_POST['uid'];
} elseif (isset($_POST['uname'])) {
    $value = (string) $_POST['uname'];
}
$result = $this->getUsers(0, 0, null, $value);
if (empty($result)) {
    echo 1;
} else {
    $result[0]['social'] = unserialize($result[0]['social']);
    echo json_encode($result[0]);
}
?>