<?php

require 'templates/activitystream.php';

$result = $this->getSettingsActivityLog($_POST['from'], $_POST['to']);
activityForeach($result, 2);
?>