<?php

if (isset($_POST['eid'])) {
    $value = (int) $_POST['eid'];
}
$result = $this->getSyncInfo($value);
if (empty($result)) {
    echo 1;
} else {
    echo json_encode($result);
}
?>