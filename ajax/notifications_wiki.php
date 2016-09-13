<?php

$result = $this->getWikiNotifications($_POST['project_id'], $_POST['user_id'], $_POST['from'], $_POST['to']);
if(!empty($result['notifs'])) {
    $html = '';
    foreach ($result['notifs'] as $res) {
        $html .= '<div class="notif-design"><a href="' . base_url('profile/' . $res['username']) . '">' . $res['fullname'] . '</a> <span>' . $res['event'] . '</span> <a href="' . base_url('wiki/' . $res['proj_name'] . '/display/' . $res['key_space'] . '?viewPageId=' . $res['page_id']) . '">' . $res['title'] . '</a></div>';
        ?>

        <?php

    }
    $arr = array('html' => $html, 'num' => $result['num']);
} else {
    $arr = array('html' => $this->lang_php['no_notifs'].'!', 'num' => 0);
}
echo json_encode($arr);
?>