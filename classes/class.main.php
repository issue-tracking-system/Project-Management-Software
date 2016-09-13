<?php

if (!defined('APPLICATION_LOADED') || !APPLICATION_LOADED) {
    die('No direct script access.');
}
/**
 * This class render pages 
 * Here are included header, footer, alerts, etc.. of pages
 * 
 * @author Kiril Kirkov
 */
require_once 'inc/db.php';
require_once 'inc/config.php';
require_once 'inc/settings.php';
require_once 'inc/helpers.php';
require_once 'classes/class.database.php';

class Main extends Database {

    private $alert;
    private $day = 30; //Login cookie expire time (in days)
    private $page;
    private $title;
    private $description;
    private $lang_js;
    private $lang_php;
    public $default_lang_abbr;
    public $user_lang = NULL;
    public $url;

    public function __construct() {
        parent::__construct();
    }

    public function run($page, $is_ajax = false) {
        $this->loadLanguage();
        if ($is_ajax === true) {
            $arr = explode('/', url_segment());
            $ajax_page = end($arr);
            if (file_exists('ajax/' . strtolower($ajax_page) . '.php')) {
                include 'ajax/' . strtolower($ajax_page) . '.php';
            } else {
                response(404);
            }
        } else {
            $this->page = $page;
            $this->loginCheck();
            ob_start();
            $this->render($page);
            $page_content = ob_get_clean();
            $this->_header($this->title, $this->description);
            echo $page_content;
            $this->_footer();
        }
    }

    private function loadLanguage() {
        $abbr = $this->getDefaultLanguage();
        $this->default_lang_abbr = $abbr;
        if (isset($_SESSION['logged']) || isset($_COOKIE['logged'])) {
            $user_id = $_SESSION['logged'] ? $_SESSION['logged']['id'] : unserialize($_COOKIE['logged'])['id'];
            $lang_abbr = $this->getUserLanguage($user_id);
        }
        if (isset($lang_abbr) && $lang_abbr !== null) {
            $abbr = $lang_abbr;
            $this->user_lang = $lang_abbr;
        }
        if (file_exists('lang/lang_' . $abbr . '.php') && file_exists('lang/lang_' . $abbr . '.js')) {
            $this->setLanguageArr($abbr);
        } else if (file_exists('lang/lang_EN.php') && file_exists('lang/lang_EN.js')) {
            $this->setLanguageArr('EN');
        } else {
            throw new Exception("Cant find language file! " . 'lang/lang_' . $abbr . '.php OR ' . 'lang/lang_' . $abbr . '.js');
        }
    }

    private function setLanguageArr($abbr) {
        require_once 'lang/lang_' . $abbr . '.php';
        $this->lang_php = $LANG;
        $this->lang_js = 'lang/lang_' . $abbr . '.js';
        unset($LANG);
    }

    private function _header($title, $description, $tags = null, $to_body = null) {
        if (is_array($tags)) {
            $tags_inner = implode("\n", $tags);
        } elseif (!is_array($tags) && $tags !== null) {
            $tags_inner = $tags;
        } else {
            $tags_inner = '';
        }
        include '_html_parts/header.php';
    }

    private function _footer() {
        include '_html_parts/footer.php';
    }

    public function render($page) {
        $page = strtolower($page);
        if (file_exists('_html_parts/' . $page . '.php')) {
            $this->page = $page;
            include '_html_parts/' . $page . '.php';
        } else {
            response(404);
        }
    }

    public function get_alert() {
        if (isset($_SESSION['alert'])) {
            $this->alert = $_SESSION['alert'];
            unset($_SESSION['alert']);
        }
        $alert = $this->alert;
        unset($this->alert);
        return $alert;
    }

    public function set_alert($msg, $type, $login_alert = false) {
        if ($login_alert === true) {
            $login_alert = 'login-alert';
        } else {
            $login_alert = '';
        }
        $_SESSION['alert'] = '<div class="alert alert-' . $type . ' ' . $login_alert . ' top-20">' . $msg . '</div>';
    }

    public function setLogged($cookie, $info_array) {
        $this->setLastLogin($info_array['id']);
        if ($cookie === true) {
            $seconds = 60 * 60 * 24 * $this->day;
            setcookie("logged", serialize($info_array), time() + $seconds);
        }
        $_SESSION['logged'] = $info_array;
    }

    public function loginCheck() {
        if (isset($_SESSION['logged'])) {
            $this->user_id = $_SESSION['logged']['id'];
            $this->user_name = $_SESSION['logged']['username'];
            $usr_info = $this->getUsers(0, 0, null, (int) $this->user_id);
            if (empty($usr_info)) {
                $this->logOut();
                exit;
            } else {
                $this->setLastActive($_SESSION['logged']['id']);
            }
        } elseif (isset($_COOKIE['logged'])) {
            $arr = unserialize($_COOKIE['logged']);
            $this->user_id = $arr['id'];
            $this->user_name = $arr['username'];
            $usr_info = $this->getUsers(0, 0, null, (int) $this->user_id);
            if (empty($usr_info)) {
                $this->logOut();
                exit;
            } else {
                $this->setLastActive($arr['id']);
            }
        }

        if (isset($usr_info) && $usr_info !== null) {
            $this->dash_filter = unserialize($usr_info[0]['dash_filter']);
            $this->social = unserialize($usr_info[0]['social']);
            $this->username = $usr_info[0]['username'];
            $this->fullname = $usr_info[0]['fullname'];
            $this->image = $usr_info[0]['image'];
            $this->permissions = explode(",", $usr_info[0]['privileges']);
            $this->profession = $usr_info[0]['profession_name'];
            $this->email = $usr_info[0]['email'];
            $this->email_notif = $usr_info[0]['email_notif'];
        }

        if ((isset($_SESSION['logged']) || isset($_COOKIE['logged'])) && $this->page == 'login') {
            redirect(base_url('home'));
        } elseif ((!isset($_SESSION['logged']) && !isset($_COOKIE['logged'])) && $this->page != 'login') {
            redirect(base_url('login'));
        }
    }

    public function ticketView($ticketAbbr) {
        $ticketAbbr = ticketAbbrParse($ticketAbbr);
        $result = $this->getTickets($this->project_abbr, $ticketAbbr['id']);
        if ($result === null || validTicketAbbr(url_segment(3)) == false) {
            return false;
        } else {
            return $result;
        }
    }

    public function uploadImage($where) {
        $image = null;
        if ($_FILES["image"]["name"] != null) {
            $target_file = $where . basename($_FILES["image"]["name"]);
            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check !== false && $imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
                if (file_exists($target_file)) {
                    $_FILES["image"]["name"] = time() . $_FILES["image"]["name"];
                    $target_file = $where . basename($_FILES["image"]["name"]);
                }
                $result_move = move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
                if ($result_move === false) {
                    writeLog('Cant move uploaded image for login form from admin panel. Function move_uploaded_file return false!');
                }
                if ($_FILES['image']['error'] == 0) {
                    $image = $_FILES["image"]["name"];
                } else {
                    $this->set_alert($this->lang_php['err_image_upload_status'] . ' - ' . $_FILES['image']['error'] . '!', 'danger');
                }
            } else {
                $this->set_alert($this->lang_php['not_image_upload'] . '!', 'danger');
            }
        }
        return $image;
    }

    public function removeLoginImage($image) {
        unlink($image);
        $this->dropLoginImage();
    }

    public function sendEmail($send_email) {
        require 'classes/PHPMailer/PHPMailerAutoload.php';
        $mail = new PHPMailer;
        if (!$mail instanceof PHPMailer) {
            writeLog(date('H:i:s / d.m.Y') . ' - Cant instance PHPMailer, maybe library is not loaded!! Contact developers!');
        }

        $mail->isSMTP();
        if (DEBUG_MODE === true) {
            $mail->SMTPDebug = 4;
        }
        $mail->Host = $send_email['hostname'];
        $mail->SMTPAuth = true;
        $mail->Username = $send_email['username'];
        $mail->Password = $send_email['password'];
        if ($send_email['ssl'] == 1) {
            $mail->SMTPSecure = 'ssl';
        }
        $mail->Port = $send_email['port'];
        $mail->setFrom($send_email['username'], $send_email['fromName']);
        $mail->addAddress($send_email['to_email'], $send_email['subject']);
        $myFile = $_FILES['attachments'];
        $fileCount = count($myFile["name"]);
        if ($fileCount > 0 && $myFile["name"][0] != "") {
            $filenames = array();
            for ($i = 0; $i < $fileCount; $i++) {
                $target_file = $GLOBALS['CONFIG']['ATTACHMENTS_DIR'] . basename($myFile["name"][$i]);
                if (file_exists($target_file)) {
                    $myFile["name"][$i] = time() . $myFile["name"][$i];
                    $target_file = $GLOBALS['CONFIG']['ATTACHMENTS_DIR'] . basename($myFile["name"][$i]);
                }
                move_uploaded_file(end($myFile["tmp_name"]), $target_file);
                $result_add_attachment = $mail->addAttachment($target_file);
                if ($result_add_attachment === true) {
                    $filenames[] = $myFile["name"][$i];
                } else {
                    writeLog(date('H:i:s / d.m.Y') . ' - Error add attachments: ' . $result_add_attachment);
                }
            }
            $this->serialized_send_files = serialize($filenames);
        }
        $mail->isHTML($send_email['is_html']);

        $mail->Subject = $send_email['subject'];
        $mail->Body = $send_email['body'] == '' ? ' ' : $send_email['body'];

        if (!$mail->send()) {
            writeLog(date('H:i:s / d.m.Y') . ' - Mailer Error (Test in debug mode for advanced info): ' . $mail->ErrorInfo);
            return false;
        } else {
            $this->lastMessageId = $mail->getLastMessageID();
            return true;
        }
    }

    public function logOut() {
        unset($_SESSION['logged']);
        setcookie("logged", "", time() - 3600);
        redirect(base_url('login'));
        exit;
    }

}
