<?php

$s_id = (int) $_POST['sid'];
$result = $this->getSpaces($s_id, 0, 0, 0, null);
if(empty($result)) {
    echo 1;
} else {
    echo json_encode($result[0]);
}
?>