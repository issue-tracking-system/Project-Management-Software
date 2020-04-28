<?php

if (!defined('APPLICATION_LOADED') || !APPLICATION_LOADED) {
    die('No direct script access.');
}

class Mysql {

    public $conn = null;
    public $connections = array();
    public $queries = array();

    public function __construct() {
        $ms = microtime(true);
        if(!class_exists('mysqli')) {
            throw new Exception("No MYSQLi extension");
        }
        $this->conn = new mysqli(HOST, USER, PASS, DATABASE);
        if ($this->conn->connect_error) {
            throw new Exception("MySQL connection failed: " . $this->conn->connect_error);
            exit;
        } else {
            $this->conn->set_charset("utf8");
            $res = "Connected to MySQL";
        }
        $ms = microtime(true) - $ms;
        if (DEBUG_MODE === true) {
            $this->connections[] = array(
                'result' => $res,
                'time' => number_format($ms, 5, '.', ''));
        }
    }

    public function escape($value) {
        return trim(mysqli_real_escape_string($this->conn, $value));
    }

    public function query($query) {
        $ms = microtime(true);
        $result = $this->conn->query($query);
        $ms = microtime(true) - $ms;
        if (DEBUG_MODE === true) {
            $this->queries[] = array(
                'query' => $query,
                'time' => number_format($ms, 5, '.', ''));
        }
        return $result;
    }

}
