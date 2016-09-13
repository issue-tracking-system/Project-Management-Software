<?php

require 'classes/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer();

if (!$mail instanceof PHPMailer) {
    writeLog(date('H:i:s / d.m.Y') . ' - Cant instance PHPMailer, maybe library is not loaded!! Contact developers!');
    exit;
}

if ($emailConfig->IsSMTP) {
    $mail->isSMTP();
}
$mail->Host = $_POST['smtp_hostname'];
$mail->Port = $_POST['smtp_port'];
$mail->SMTPAuth = true;

if ($_POST['smtp_ssl'] == 1) {
    $mail->SMTPSecure = 'ssl';
}

if (DEBUG_MODE === true) {
    $mail->SMTPDebug = 3;
}

$mail->Username = $_POST['username'];
$mail->Password = $_POST['password'];
$mail->From = $_POST['username'];
$mail->FromName = 'Kiril Kirkov';

if ($mail->smtpConnect()) {
    $mail->smtpClose();
    $smtp_errors = false;
} else {
    $smtp_errors = true;
    $smtp_err_info = $mail->ErrorInfo;
}

$ssl = '';
$self_signed = '';
if ($_POST['_ssl'] == 1) {
    $ssl = '/ssl';
}
if ($_POST['self_signed_cert'] == 1) {
    $self_signed = '/novalidate-cert';
}
$hostname = '{' . $_POST['hostname'] . ':' . $_POST['port'] . '/' . $_POST['protocol'] . $ssl . $self_signed . '}' . $_POST['folder'];

$connection = imap_open($hostname, $_POST['username'], $_POST['password']) or die('Cannot connect to Mail: ' . imap_last_error());
$imap_errors = imap_errors(); //true or false

if ($imap_errors === false && $smtp_errors === false) {
    echo '1';
} elseif ($imap_errors !== false) {
    echo $connection;
} elseif ($smtp_errors === true) {
    echo $smtp_err_info;
}
?>