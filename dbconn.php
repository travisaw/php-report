<?php

class dbconn
{
    private $config;

    private $CONN_DB_TYPE;
    private $CONN_DB_HOST;
    private $CONN_DB_NAME;
    private $CONN_DB_USER;
    private $CONN_DB_PASS;
    private $options;
    private $con;

    public $errorCount = 0;
    public $queryCount = 0;
    public $errorInfo;
    public $isError = 0;
    public $errorMessage = '';

    public $dbname;

    function __construct() {

        // Load Config File
        // $scriptPath = realpath(dirname(__FILE__));
        // $this->config = json_decode(file_get_contents("$scriptPath/config.json"));
        $this->config = json_decode(file_get_contents("config.json"));

        // Get Database Parameters
        $this->CONN_DB_TYPE = $this->config->SqlDbType;
        $this->CONN_DB_HOST = $this->config->SqlDbHost;
        $this->CONN_DB_NAME = $this->config->SqlDbName;
        $this->CONN_DB_USER = $this->config->SqlDbUser;
        $this->CONN_DB_PASS = $this->config->SqlDbPass;

        if ($this->config->SqlDbLogLevel == 1) {
            $this->options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);
        }
        elseif ($this->config->SqlDbLogLevel == 2) {
            $this->options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        }
        else {
            $this->options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT);
        }

        // Create connection
        $this->con = new PDO($this->CONN_DB_TYPE . ':host=' . $this->CONN_DB_HOST . ';dbname=' . $this->CONN_DB_NAME, $this->CONN_DB_USER, $this->CONN_DB_PASS, $this->options);
        $this->con->exec("set names utf8");

        // Set Local Variables
        $this->dbname = $this->CONN_DB_NAME;
    }

    function execQuery($sql, $isReturn = 0) {

        // Reset error properties
        $this->isError = 0;
        $this->errorMessage = '';

        $results = 0;
        $query = $this->con->prepare($sql);
        $query->execute();

        $this->errorInfo = $query->errorInfo();

        if ($query->errorCode() !== "00000") {
            $this->errorCount++;
            $this->isError = 1;
            $this->errorMessage = $this->errorInfo[2];
            $this->dbLogMessage();
        }
        $this->queryCount++;

        if ($isReturn > 0) {
            $results = $query->fetchAll();
        }
        return $results;
    }

    function beginTrans() {
        $query = $this->con->prepare("BEGIN;");
        $query->execute();
    }

    function commitTrans() {
        $query = $this->con->prepare("COMMIT;");
        $query->execute();
    }

    private function dbLogMessage() {
        if ($this->config->ScreenLogLevel >= 1) {
            echo "SQL ERROR! SQL State " . $this->errorInfo[0] .". Error Number: " . $this->errorInfo[1] . ". " . $this->errorInfo[2] . PHP_EOL;
        }
    }

}

?>