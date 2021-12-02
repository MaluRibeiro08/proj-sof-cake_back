<?php

  class Conexao {
    private $_dbHostname = 'localhost';
    private $_dbUsername = 'root';
    private $_dbPassword = 'bcd127';
    private $_dbDatabase = 'db_softcake';
    private $_conn;

    public function __construct() {
      try {
        $this->_conn = new PDO("mysql:dbname=$this->_dbDatabase;host=$this->_dbHostname;", $this->_dbUsername, $this->_dbPassword);

        $this->_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
      } catch (PDOException $error) {
        echo 'Connection error: ' . $error->getMessage();
      }
    }

    public function getConnection() {
      return $this->_conn;
    }
  }

?>