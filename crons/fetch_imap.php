<?php

/*
 * This cron checks for new emails and convert them to tickets
 * Can be set to be called in one minute
 * Example of valid gmail inbox: {imap.gmail.com:993/imap/ssl}INBOX, $hostname must be something like this!
 * 
 * @author: Kiril Kirkov
 * GitHub: https://github.com/kirilkirkov
 */

ini_set('error_reporting', E_ALL);
define('APPLICATION_LOADED', true);
define('DEBUG_MODE', false);

require_once '../inc/db.php';
require_once '../inc/config.php';
require_once '../inc/helpers.php';
require_once '../classes/class.imap.php';
require_once '../classes/class.mysql.php';

$db = new Mysql();
$imap = new Imap();

$accounts_ = $db->query("SELECT id FROM accounts WHERE active = 1");
$accounts = array();
while ($row = $accounts_->fetch_assoc()) {
    $accounts[] = $row;
}

if (!empty($accounts)) {
    foreach ($accounts as $account) {
        $acc_id = $account['id'];
        $projects = $db->query("SELECT projects.id as project_id, projects.name as project_name, projects.timestamp, sync as sync_id, hostname, protocol, port, _ssl, self_signed, folder, username, password FROM projects INNER JOIN sync_connections ON projects.sync = sync_connections.id WHERE projects.sync > 0 AND projects.for_account=" . $acc_id);
        $arr_projects = array();
        while ($row = $projects->fetch_assoc()) {
            $arr_projects[] = $row;
        }

        if (!empty($arr_projects)) {
            foreach ($arr_projects as $project) { //Loop synced projects
                $ssl = '';
                $self_signed = '';
                if ($project['_ssl'] == 1) {
                    $ssl = '/ssl';
                }
                if ($project['self_signed'] == 1) {
                    $self_signed = '/novalidate-cert';
                }
                $hostname = '{' . $project['hostname'] . ':' . $project['port'] . '/' . $project['protocol'] . $ssl . $self_signed . '}' . $project['folder'];
                $connection_result = $imap->connect($hostname, $project['username'], $project['password']);
                if ($connection_result !== true) {
                    writeLog('Time: ' . date("Y.m.d H.m.s", time()) . "\nDomain:" . $_SERVER['HTTP_HOST'] . "\nError connect to IMAP from crons/emailsFetch.php:\n" . $connection_result . "\n\n", '../logs/errors.log');
                    continue; //if error with connetion loop next project
                }

                $messages = $imap->getMessages('text');
                //echo '<pre>';
                //print_r($messages);
                //echo '<pre>';
                if (!empty($messages)) {
                    $tickets = array();
                    $replays = array();
                    $maxid = 0;
                    foreach ($messages as $message) {
                        if (is_int($message['references'])) { //if no reference - is new email
                        } else {
                            $replays[$message['uid']] = array(
                                'comment' => $db->escape($message['message']), //description of ticket
                                'message_uid' => $message['uid'], //uniqe message id
                                'message_id' => $message['references'],
                                'time' => $message['date'],
                                'send' => 0
                            );
                        }
                        $attachments = serialize($message['attachments']);
                        if ($maxid == 0) {
                            $max_res = $db->query("SELECT MAX(ticket_id) as id FROM tickets WHERE project = " . $project['project_id'] . " AND for_account=" . $acc_id);
                            $maxid = $max_res->fetch_object()->id + 1;
                        } else {
                            $maxid++;
                        }
                        $tickets[$message['uid']] = array(
                            'for_account' => $acc_id,
                            'ticket_id' => $maxid,
                            'project' => $project['project_id'],
                            'type' => 3, //support type of ticket
                            'subject' => isset($message['subject']) ? $message['subject'] : '',
                            'status' => 1, //new ticket
                            'priority' => 3, //priority is high
                            'description' => $db->escape($message['message']), //description of ticket
                            'message_uid' => $message['uid'], //uniqe message id
                            'message_id' => $message['message_id'],
                            'message_from_email' => $message['from']['address'],
                            'message_from_name' => $message['from']['name'],
                            'message_attachments' => isset($attachments) ? $attachments : '',
                            'timecreated' => $message['date']
                        );
                    }
                    $tickets_original = $tickets;
                    $reply_keys = array_keys($replays);
                    foreach ($reply_keys as $r_key) {
                        unset($tickets[$r_key]);
                    }

                    if (!empty($tickets)) {
                        foreach ($tickets as $ticket) { //set new tickets there are no in database with this UID and PROJECT ID
                            $check = $db->query("SELECT id FROM tickets WHERE message_uid = " . $ticket['message_uid'] . " AND project = " . $ticket['project'] . " AND for_account=" . $acc_id);
                            if ($check->num_rows == 0) {
                                $columns = implode(',', array_keys($ticket));
                                $values = implode("','", $ticket);
                                $result = $db->query("INSERT INTO tickets ($columns) VALUES ('$values')");
                            }
                        }
                    }
                    if (!empty($replays)) {
                        foreach ($replays as $reply) { //set new emails like comments there are have reply message_id and are no allready in database
                            preg_match("/<(.*)>/U", $reply['message_id'], $out);
                            if (isset($out[1])) {
                                $check_first = $db->query("SELECT id FROM tickets WHERE message_id LIKE '%" . $out[1] . "%' AND for_account=" . $acc_id . " LIMIT 1");
                                if ($check_first->num_rows > 0) {
                                    unset($reply['message_id']);
                                    $tid = $check_first->fetch_row();
                                    $reply['ticket_id'] = $tid[0];
                                    $check_comment = $db->query("SELECT id FROM comments WHERE ticket_id = " . $reply['ticket_id'] . " AND message_uid = " . $reply['message_uid'] . " AND for_account=" . $acc_id);
                                    if ($check_comment->num_rows == 0) {
                                        $columns = implode(',', array_keys($reply));
                                        $values = implode("','", $reply);
                                        $result = $db->query("INSERT INTO comments ($columns) VALUES ('$values')");
                                    }
                                } else { //not categorized? Make it new ticket!
                                    $set_reply_ticket = $tickets_original[$reply['message_uid']];
                                    $check = $db->query("SELECT id FROM tickets WHERE message_uid = " . $set_reply_ticket['message_uid'] . " AND project = " . $set_reply_ticket['project'] . " AND for_account=" . $acc_id);
                                    if ($check->num_rows == 0) {
                                        $columns = implode(',', array_keys($set_reply_ticket));
                                        $values = implode("','", $set_reply_ticket);
                                        $result = $db->query("INSERT INTO tickets ($columns) VALUES ('$values')");
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
?>