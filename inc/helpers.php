<?php

if (!defined('APPLICATION_LOADED') || !APPLICATION_LOADED) {
    die('No direct script access.');
}

function redirect($location = '', $get_parameters = true) {
    if (DEBUG_MODE === false) {
        if ($location == '') {
            if ($get_parameters == false) {
                $_SERVER['REQUEST_URI'] = preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']);
            }
            $location = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }
        if (!headers_sent()) {
            header('Location: ' . $location . '');
            exit;
        } else {
            echo '<script>document.location.href = "' . $location . '"</script>';
            exit;
        }
    }
}

function response($code) {
    if ($code == 404) {
        include '_html_parts/error_codes/404.php';
    }
    if ($code == 403) {
        include '_html_parts/error_codes/403.php';
    }
    http_response_code($code);
    exit;
}

function return_pass($password) {
    if (preg_match('/^[a-f0-9]{32}$/', $password)) //this is because from pmticket administration i pass md5 password from directly db
        return $password;
    else
        return md5($password);
}

function send_notification_emails($emails) {
    mail($emails, EMAIL_SUBJECT, EMAIL_MESSAGE);
}

function base_url($add = null, $atRoot = FALSE, $atCore = FALSE) {
    if (isset($_SERVER['HTTP_HOST'])) {
        $http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
        $hostname = $_SERVER['HTTP_HOST'];
        $dir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

        $core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))), NULL, PREG_SPLIT_NO_EMPTY);
        $core = $core[0];

        $tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
        $end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
        $base_url = sprintf($tmplt, $http, $hostname, $end);
    } else
        $base_url = 'http://localhost/';

    return $base_url . $add;
}

function url_segment($n = null) {
    $dir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    if ($dir != '/') {
        $current_link = str_replace($dir, '', $_SERVER["REQUEST_URI"]);
    } else {
        $current_link = ltrim($_SERVER["REQUEST_URI"], '/');
    }
    if (strpos($current_link, '?')) {
        $get = parse_url($current_link, PHP_URL_QUERY);
        $get = explode('&', $get);
        foreach ($get as $g) {
            $g = explode('=', $g);
            $_GET[$g[0]] = $g[1];
        }
        $current_link = preg_replace('/\?.*/', '', $current_link);
    }
    $link_array = explode('/', $current_link);
    if ($n == null) {
        return $current_link;
    } elseif (is_numeric($n) && isset($link_array[$n])) {
        return $link_array[$n];
    } else {
        return false;
    }
}

function word_limiter($word, $limit_to, $start_from = 0) {
    if (mb_strlen($word) <= 100) {
        return $word;
    }
    $cut = mb_substr($word, $start_from, $limit_to);
    $add_dots = $cut . '...';
    return $add_dots;
}

function secondsToTime($seconds, $want_array = false) {
    if ($seconds < 0) {
        $minus = '- ';
    }
    else {
        $minus = '';
    }
    $dtF = new DateTime("@0");
    $dtT = new DateTime("@$seconds");
    if ($want_array === false) {
        return $dtF->diff($dtT)->format($minus . ESTIMATED_TIME_TYPE);
    } else {
        $_POST['estimated_days'] = $dtF->diff($dtT)->format('%a');
        $_POST['estimated_hours'] = $dtF->diff($dtT)->format('%h');
        $_POST['estimated_minutes'] = $dtF->diff($dtT)->format('%i');
        $_POST['estimated_seconds'] = $dtF->diff($dtT)->format('%s');
    }
}

function validTicketAbbr($ticketAbbr) {
    if (preg_match('/^[a-zA-Z]{3}-[0-9]+$/', $ticketAbbr)) {
        return true;
    } else {
        return false;
    }
}

function ticketAbbrParse($ticketAbbr) {
    $arr = array();
    $editable = explode('-', $ticketAbbr);
    $arr['abbr'] = $editable[0];
    $arr['id'] = $editable[1];
    return $arr;
}

function nameDay($timestamp) {
    if ((time() - $timestamp) <= 24 * 60 * 60)
        return 'Today';
    else if ((time() - $timestamp) > 24 * 60 * 60 && (time() - $timestamp) <= 48 * 60 * 60)
        return 'Yesterday';
    else {
        return date('l', $timestamp);
    }
}

function returnImageUrl($image) {
    if ($image != null) {
        return $GLOBALS['CONFIG']['IMAGESUSERSUPLOADDIR'] . $image;
    } else {
        return 'assets/imgs/' . $GLOBALS['CONFIG']['DEFAULTUSERIMAGE'];
    }
}

function returnSpaceImageUrl($image) {
    if ($image != null) {
        return $GLOBALS['CONFIG']['IMAGESSPACESUPLOADDIR'] . $image;
    } else {
        return 'assets/imgs/' . $GLOBALS['CONFIG']['DEFAULTSPACEIMAGE'];
    }
}

function paging($action = '', $query_string = '', $count = 0, $page = 0, $per_page = 20) {
    if ($page < 0 || $page >= ceil($count / $per_page))
        $page = 0;

    $paging = '';
    $pz = $page - 1;
    $pp = $page + 1;
    $asa = $page;
    $number_pages = floor(($count - 1) / $per_page) + 1;
    if ($count > $per_page) {
        $paging .= '<nav><ul class="pagination">';

        if ($pp >= 2) {
            $paging .= '<li><a aria-label="Първа" href="' . base_url($action) . '?pg=0' . $query_string . '"><span aria-hidden="true">&laquo;&laquo;</span></a></li>';
            $paging .= '<li><a aria-label="Предишна" href="' . base_url($action) . '?pg=' . $pz . $query_string . '"><span aria-hidden="true">&laquo;</span></a></li>';
        }

        if ($number_pages > 10 and ( $page < 5 or $page == 5)) {
            $bbn = 1;
            $bbkr = 10;
        } elseif ($number_pages > 10 and $page > 5 and $page < $number_pages - 5) {
            $bbn = $page - 4;
            $bbkr = $page + 5;
        } elseif ($number_pages > 10 and ( $page == 5) and $page < $number_pages - 5) {
            $bbn = 1;
            $bbkr = $page + 5;
        } elseif ($number_pages > 10 and $page > 5 and ( $page > $number_pages - 5 or $page == $number_pages - 5)) {
            $bbn = $number_pages - 9;
            $bbkr = $number_pages;
        } elseif ($number_pages <= 10) {
            $bbn = 1;
            $bbkr = $number_pages;
        }

        if ($page > 2 and $bbn > 2) {
            $paging .= '<li><a href="' . base_url($action) . '?pg=0' . $query_string . '" >1</a></li>';
        }

        for ($i = $bbn; $i <= $bbkr; $i++) {
            if ($asa == $i - 1) {
                $paging .= '<li class="active"><a href="#">' . $i . '</a></li>';
            } else {
                $paging .= '<li><a href="' . base_url($action) . '?pg=' . ($i - 1) . $query_string . '">' . $i . '</a></li>';
            }
        }

        if ($page < $number_pages and $bbkr < $number_pages - 1) {
            $paging .= '<li><a>...</a></li>';
        }
        if ($page < $number_pages and $bbkr < $number_pages) {
            $paging .= '<li><a href="' . baseurl($action) . '?pg=' . ($number_pages - 1) . $query_string . '" >' . $number_pages . '</a></li>';
        }
        if ($asa != ($number_pages - 1)) {
            $paging .= '<li><a aria-label="Следваща" href="' . base_url($action) . '?pg=' . $pp . $query_string . '"><span aria-hidden="true">&raquo;</span></a></li>';
            $paging .= '<li><a aria-label="Последна" href="' . base_url($action) . '?pg=' . ($number_pages - 1) . $query_string . '"><span aria-hidden="true">&raquo;&raquo;</span></a></li>';
        }

        $paging .= '</ul></nav>';
    }

    return $paging;
}

function getQueryStr($get, $unset) {
    if (is_array($unset)) {
        foreach ($unset as $un) {
            unset($get[$un]);
        }
    } else {
        unset($get[$unset]);
    }
    $queryString = '';
    foreach ($get as $key => $value) {
        if ($queryString == '')
            $queryString.='?';
        $queryString .= $key . '=' . $value . '&';
    }
    return rtrim($queryString, "&");
}

function getFolderLanguages() {
    $abbreviations = array();
    $abbrs = array();
    $dir_files = scandir('lang/');
    unset($dir_files[0], $dir_files[1]);
    foreach ($dir_files as $file) {
        preg_match("/^lang_([A-Z]{2,5})\.(php|js|PHP|JS)$/", $file, $abb);
        if (!empty($abb)) {
            $abbrs[$abb[1]][] = $abb[2];
        }
    }
    foreach ($abbrs as $ab_key => $ab) {
        if (array_search("php", $ab) !== false && array_search("js", $ab) !== false) {
            $abbreviations[] = $ab_key;
        }
    }
    return $abbreviations;
}

function writeLog($text, $logfile = 'logs/errors.log') {
    $file = fopen($logfile, "a");
    if ($file !== false) {
        fwrite($file, $text . "\n");
        fclose($file);
    }
}

?>