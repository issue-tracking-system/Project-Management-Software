<?php

if (!defined('APPLICATION_LOADED') || !APPLICATION_LOADED) {
    die('No direct script access.');
}
//DO NOT CHANGE ANYTHING IN THIS FILE!!!
mb_internal_encoding("UTF-8");

$CONFIG = array();

$CONFIG['PERMISSIONS'] = array(
    'CREATE_PROJECT' => 1,
    'SETTINGS_PAGE' => 2,
    'TICKETS' => array(
        'ADD_EDIT_MINE_TICKETS' => 3, //ADDED BY ME OR ASSIGNED TO ME
        'ADD_EDIT_ALL_TICKETS' => 4 //ALL OTHER
    ),
    'WIKI' => array(
        'ADD_NEW_SPACES' => 5,
        'ADD_NEW_PAGES' => 6,
        'EDIT_OTHER_PAGES' => 7, //EDIT PAGES THAT ARE NOT MINE
        'DELETE_PAGES' => 8,
        'MOVE_PAGES' => 9
    )
);

$CONFIG['DEFAULT_USER_TYPES'] = array(//MUST BE SORTED! <
    'Admin' => '1,2,3,4,5,6,7,8,9',
    'Super User' => '3,4,5,6,7,8,9',
    'User' => '3, 6',
    'Watcher' => ''
);

/*
 * ACCOUNT_DOMAIN constant is defined in class.subdomain.php
 * If is not enabled subdomain support, it is defined in index.php
 */
$CONFIG['USERNAMEREGEX'] = '/^[a-zа-яA-ZА-Я0-9]+$/'; //REGISTRATION REGEX
$CONFIG['FULLNAMEREGEX'] = '/^[a-zа-яA-ZА-Я\s]+$/'; //REGISTRATION REGEX
$CONFIG['EMAILREGEX'] = '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/'; //REGISTRATION REGEX
$CONFIG['IMAGESUSERSUPLOADDIR'] = 'attachments/' . ACCOUNT_DOMAIN . '/users_profile/'; //PROFILE IMAGES
$CONFIG['IMAGELOGINUPLOADDIR'] = 'attachments/' . ACCOUNT_DOMAIN . '/login_image/'; //PROFILE IMAGES 
$CONFIG['ATTACHMENTS_DIR'] = 'attachments/' . ACCOUNT_DOMAIN . '/imap/'; //USABLE IN class.imap.php
$CONFIG['DEFAULTUSERIMAGE'] = 'default/male.jpg'; //DEFAULT USER IMAGE 
$CONFIG['IMAGESSPACESUPLOADDIR'] = 'attachments/' . ACCOUNT_DOMAIN . '/space_logos/'; //SPACE IMAGES
$CONFIG['DEFAULTSPACEIMAGE'] = 'default/space-logo.png'; //DEFAULT SPACE IMAGE 
$CONFIG['SPACEKEYREGEX'] = '/^[a-zA-Zа-яА-Я0-9_]+$/'; //WIKI SPACE KEY REGEX
$CONFIG['WIKI_PAGES_NAMES'] = '/^[a-zA-Zа-яА-Я0-9_-]+$/'; //WIKI SPACE KEY REGEX

/*
 * must have languages for keys, tickets are saved with key values to be used for languages
 * values are inverse type of keys
 */
$CONFIG['ISSUE_LINKS'] = array(
    'relates_to' => 'relates_to',
    'is_duplicated_by' => 'duplicates',
    'duplicates' => 'is_duplicated_by',
    'is_blocked_by' => 'blocks',
    'blocks' => 'is_blocked_by'
);
?>