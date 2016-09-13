<?php

require 'templates/activitystream.php';

$result = $this->getWikiActivityStream($_POST['projectid'], $_POST['from'], $_POST['to']);
activityForeach($result, 1);
?>