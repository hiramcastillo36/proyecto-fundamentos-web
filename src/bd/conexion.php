<?php

class Conexion {
    private $host;
    private $user;
    private $password;
    private $database;
    private $charset;
    private $port;

    public function __construct() {
        $this->host = 'mysql';
        $this->user =  'user';
        $this->password = 'password';
        $this->database = 'myapp';
        $this->charset = 'utf8';
        $this->port = '3306';
    }

    public function conectar() {
        $com = "mysql:host=".$this->host.";dbname=".$this->database.";charset=".$this->charset;
        $conexion = new PDO($com, $this->user, $this->password);

        return $conexion;
    }
}
?>
