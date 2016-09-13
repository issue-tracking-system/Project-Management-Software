<?php

$result = $this->getTicketNotifications($_POST['project_id'], $_POST['user_id'], $_POST['from'], $_POST['to']);
if(!empty($result['notifs'])) {
    $html = '';
    foreach ($result['notifs'] as $res) {
        $html .= '<div class="notif-design"><a href="' . base_url('profile/' . $res['username']) . '">' . $res['fullname'] . '</a> <span>' . $res['event'] . '</span> <a href="' . base_url('tickets/' . $res['proj_name'] . '/view/' . $res['abbr'] . '-' . $res['ticket_id']) . '">' . $res['subject'] . '</a></div>';
    }
    $arr = array('html' => $html, 'num' => $result['num']);
} else {
    $arr = array('html' => $this->lang_php['no_notifs'].'!', 'num' => 0);
}
echo json_encode($arr);
?>