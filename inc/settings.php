<?php

if (!defined('APPLICATION_LOADED') || !APPLICATION_LOADED) {
    die('No direct script access.');
}
//Debug mode show every sql query on pages
define('DEBUG_MODE', FALSE);

//ALLOWED CHARS FOR PASS GEN AND PASS RESET
define('ALLOWED_CHARS_GEN', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');

//This is type of date that are in dashboard and ticket view
define('TICKETS_DATE_TYPE', 'H:i / d.m.Y');
define('TICKETS_DUEDATE_TYPE', 'd.m.Y');
define('TRACKTIME_CREATE_DATE_TYPE', 'd.m.Y');
define('ESTIMATED_TIME_TYPE', '%a days, %h hours and %i minutes');
define('ACTIVITY_DATE_TYPE', 'H:i / d.m.Y');

//settings page
define('MAX_NUM_NOTIF', 999); //max number notifications like integer on menu
define('MAX_NUM_IN_BRECKETS', 999); // same but in statistics
define('RESULT_LIMIT_SETTINGS_PROFILES', 20);
define('RESULT_LIMIT_SETTINGS_SPACES', 20);
define('SETTINGS_PROFILES_DATETYPE', 'H:i / d.m.Y');
define('PROJECTS_TIME_CREATED', 'H:i / d.m.Y');

define('SPACES_TIME_CREATED', 'H:i / d.m.Y');

//profile page
define('RESULT_LIMIT_PROFILES', 20);

//pages
define('PAGES_UPDATE_TYPE_DATE', 'M d, Y');

//email notifications
define('EMAIL_SUBJECT', 'Notification alert!');
define('EMAIL_MESSAGE', 'You have new notification from ticket system!');
?>
