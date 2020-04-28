<?php

if (!defined('APPLICATION_LOADED') || !APPLICATION_LOADED) {
    die('No direct script access.');
}

/**
 * Subdomain checker and validation
 * @author Kiril Kirkov
 */
define('BASEPATH', '');
require_once 'inc/plans.php';

class Subdomain {

    private $domain;
    private $custom_plan_id;
    private $db;

    public function __construct() {
        $subDomain = explode('.', $_SERVER['HTTP_HOST']);
        if (count($subDomain) < 3) {
            header('Location: http://'.$_SERVER['HTTP_HOST']); //Check that we have subdomain in url
        } else {
            $this->domain = $subDomain[0];
            $this->checkFirm();
        }
    }

    private function checkFirm() {
        require_once 'class.mysql.php';
        $this->db = new Mysql();
        $domain = $this->db->escape($this->domain);
        $result = $this->db->query("SELECT id, company, domain FROM accounts WHERE domain = '" . $domain . "' AND active = 1 LIMIT 1");
        if ($result->num_rows > 0) {
            $obj = $result->fetch_object();
            $for_account = $obj->id;
            $result_plans = $this->db->query("SELECT plan, id FROM accounts_plans WHERE for_account = '" . $for_account . "' AND to_date >= " . time() . ' LIMIT 1');
            $obj_plan = $result_plans->fetch_object();
            define('MY_PLAN', $obj_plan->plan);
            if ($obj_plan->plan == 'CUSTOM') { //Set parameters for custom plan
                $this->custom_plan_id = $obj_plan->id;
                $this->setCustomPlan();
            }
            define('COMPANY_NAME', $obj->company);
            define('ACCOUNT_ID', $obj->id);
            define('ACCOUNT_DOMAIN', $obj->domain);
        } else {
            header('Location: http://'.$_SERVER['HTTP_HOST']);
        }
        unset($this->db);
    }

    private function setCustomPlan() {
        $custom_id = $this->custom_plan_id;
        $cust_plan = $this->db->query("SELECT * FROM custom_accounts_plans WHERE for_id = " . $custom_id);
        $cust_plan = $cust_plan->fetch_object();
        $GLOBALS['PLAN']['CUSTOM'] = array(
            'custom_domain' => $cust_plan->custom_domain,
            'projects' => $cust_plan->projects,
            'users' => $cust_plan->users
        );
    }

}

$goCheck = new Subdomain();
unset($goCheck);
