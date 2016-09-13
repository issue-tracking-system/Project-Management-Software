<?php

if (!defined('APPLICATION_LOADED') || !APPLICATION_LOADED) {
    die('No direct script access.');
}

/** PLANS **
 * user = 0.30$
 * project = 0.60$
 */
$PLAN = array();
$PLAN['BASIC'] = array(
    'custom_domain' => false,
    'projects' => 1,
    'users' => 10
);
$PLAN['STANDARD'] = array(
    'custom_domain' => true,
    'projects' => 5,
    'users' => 30
);
$PLAN['PREMIUM'] = array(
    'custom_domain' => true,
    'projects' => 'unlimited',
    'users' => 'unlimited'
);
$PLAN['CUSTOM'] = array(
    'custom_domain' => null,
    'projects' => null,
    'users' => null
);
