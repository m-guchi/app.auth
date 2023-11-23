<?php

require "vendor/autoload.php";

use PDO;
use PDOException;
use \Symfony\Component\Yaml\Yaml;


class DB
{
    public $pdo;
    private $env;

    public function __construct()
    {
        $input = file_get_contents(__DIR__."/../.env/db_key.yaml");
        $this->env = Yaml::parse($input);
        $this->pdo = $this->pdo();
    }
    
    public function pdo()
    {
        try{
            $driver_option = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $dns = "mysql:dbname=app_auth;host={$this->env["host"]};charset=utf8mb4";
            $pdo = new PDO($dns ,$this->env["user"],$this->env["pass"],$driver_option);
        }catch(PDOException $error){
            header("Content-Type: application/json; charset=utf-8", true, 500);
            echo json_encode(["error" => ["type" => "server_error","message"=>$error->getMessage()]]);
            die();
        }
        return $pdo;
    }
}