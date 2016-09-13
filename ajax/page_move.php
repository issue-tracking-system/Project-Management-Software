<?php

if($_POST['page_id'] == 0 || $_POST['space'] <= 0) {
    echo 0;
    exit;
}
$result = $this->movePage($_POST['parent'], $_POST['space'], $_POST['page_id']);
if(!empty($result)) {
    echo 1;
} else {
    echo 0;
}
?>