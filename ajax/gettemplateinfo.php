<?php

$t_id = (int) $_POST['tid'];
$result = $this->getTemplateInfo($t_id);
if(empty($result)) {
    echo 1;
} else {
    echo json_encode($result);
}
?>