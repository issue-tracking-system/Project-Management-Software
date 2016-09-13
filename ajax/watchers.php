<?php

$result = $this->changeWatchers($_POST);
if ($result !== false) {
    echo 1;
} else {
    echo 0;
}
?>