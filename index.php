<?php

/*
 * pmTicket.com
 * Created by Kiril Kirkov
 */
session_start();
define('APPLICATION_LOADED', true);

require_once 'inc/db.php';
require_once 'classes/class.main.php';
//require_once 'classes/class.subdomain.php';  ~REMOVE SUBDOMAIN SUPPORT

define('ACCOUNT_ID', '1'); //If using without multiaccounts

if (DEBUG_MODE === false) {
    error_reporting(E_ALL ^ E_NOTICE | E_WARNING);
}

try {
    $main = new Main();

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $main->run($_GET['page'], true);
    } else {
        if (!isset($_GET['page'])) {
            $_GET['page'] = 'login';
        }
        $main->run($_GET['page']);
    }
} catch (Exception $e) {
    if (DEBUG_MODE === true) {
        echo 'New Exception: ' . $e->getMessage();
    } else {
        writeLog('Time: ' . date("Y.m.d H.m.s", time()) . "\nDomain:" . $_SERVER['HTTP_HOST'] . "\nNew Exception:" . $e->getMessage() . "\n\n");
        include '_html_parts/exeption_page.php';
    }
}
?>
