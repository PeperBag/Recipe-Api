<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PATCH");
header("Access-Control-Allow-Max-Age: 3600");
header("Access-Control-Allow-Header: Content-Type, Access-Control-Allow-Origin, X-Auth-User");

if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Auth-User");
    header("HTTP/1.1 200 OK");
    die();
  }



date_default_timezone_set("Asia/Manila");

define("SERVER", "localhost");
define("DB", "recipeapi");
define("USER", "root");
define("PWORD", "");
define("TOKEN_KEY","C8A17F3E4C221AC1151F52B53DC44");
define("SECRET_KEY","Your_secret_key");

class Connection
{
    protected $connectionString = "mysql:host=" . SERVER . ";dbname=" . DB . ";charset=utf8";
    protected $options = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES => false,
    ];

    public function connect()
    {
        return new \PDO($this->connectionString, USER, PWORD, $this->options);
    }
}
