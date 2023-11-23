<?php

class Check{

    public $token_exp_seconds = 3*24*60*60;

    public function __construct(){
        include_once("db.php");
        $this->db = new DB();
    }

    public function login($user_id, $password){
        try{
            $sql = "SELECT password FROM users WHERE id = :id";
            $sth = $this->db->pdo->prepare($sql);
            $sth->bindParam(':id', $user_id);
            $sth->execute();
        }catch(PDOException $e){
            echo $e;
            return ["ok" => false, "error" => "db_error"];
        }
        
        $data = $sth->fetch(PDO::FETCH_ASSOC);
        if(!$data) return ["ok" => false, "error" => "not_user"];
        if(!password_verify($password, $data["password"]))  return ["ok" => false, "error" => "invalid_password"];
        $token = $this->create_token($user_id);
        if($token === false) return ["ok" => false, "error" => "cannot_create_token"];
        return ["ok" => true, "token"=>$token];
    }

    private function create_token($user_id){
        $token = substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', 120)), 0, 120);
        $now = time();
        $create_date = date('Y-m-d H:i:s', $now);
        $expiration_date = date('Y-m-d H:i:s', $now + $this->token_exp_seconds);
        try{
            $sql = "INSERT INTO sessions (user_id, token, create_date, expiration_date) VALUE (:user_id, :token, :create_date, :expiration_date)";
            $sth = $this->db->pdo->prepare($sql);
            $sth->bindParam(':user_id', $user_id);
            $sth->bindParam(':token', $token);
            $sth->bindParam(':create_date', $create_date);
            $sth->bindParam(':expiration_date', $expiration_date);
            $sth->execute();
        }catch(PDOException $e){
            echo $e;
            return false;
        }
        return $token;
    }

    public function access($page_id){
        $access_page = $this->access_page($page_id);
        if($access_page<0){
            $sql = "SELECT url FROM pages WHERE id = :page_id";
            $sth = $this->db->pdo->prepare($sql);
            $sth->bindParam(':page_id', $page_id);
            $sth->execute();
            $data = $sth->fetch(PDO::FETCH_ASSOC);
            $url_param = $data ? "?page=".$data["url"]."&c=".(-$access_page) : "";
            header('Location: https://app.minagu.work/login/'.$url_param);
            exit;
        }
    }

    public function fetch_user_from_cookie(){
        if(!empty($_COOKIE) && !empty($_COOKIE["token"])){
            $token = $_COOKIE["token"];
            return $this->fetch_user_from_token($token);
        }
        return false;
    }

    private function access_page($page_id){
        $user = $this->fetch_user_from_cookie();
        if($this->access_scope($user, $page_id)){
            return 1;
        }else{
            return ($user) ? -403 : -401;
        }
    }

    private function fetch_user_from_token($token){
        $now = date('Y-m-d H:i:s');
        try{
            $sql = "SELECT * FROM sessions WHERE token = :token and expiration_date > :expiration_date";
            $sth = $this->db->pdo->prepare($sql);
            $sth->bindParam(':token', $token);
            $sth->bindParam(':expiration_date', $now);
            $sth->execute();
        }catch(PDOException $e){
            echo $e;
            return false;
        }
        $data = $sth->fetch(PDO::FETCH_ASSOC);
        if(!$data) return false;
        return $data["user_id"];
    }

    private function access_scope($user_id, $page_id){
        $access_bool = true;
        try{
            $sql = "SELECT id FROM pages WHERE id = :page_id and anyone_access = :anyone_access";
            $sth = $this->db->pdo->prepare($sql);
            $sth->bindParam(':page_id', $page_id);
            $sth->bindParam(':anyone_access', $access_bool);
            $sth->execute();
        }catch(PDOException $e){
            echo $e;
            return false;
        }
        if(count($sth->fetchAll(PDO::FETCH_ASSOC))>0){ return true;}
        if($user_id===false) return false;
        $access_bool = true;
        try{
            $sql = "SELECT * FROM scopes WHERE user_id = :user_id and page_id = :page_id and access = :access";
            $sth = $this->db->pdo->prepare($sql);
            $sth->bindParam(':user_id', $user_id);
            $sth->bindParam(':page_id', $page_id);
            $sth->bindParam(':access', $access_bool);
            $sth->execute();
        }catch(PDOException $e){
            echo $e;
            return false;
        }
        return boolval(count($sth->fetchAll(PDO::FETCH_ASSOC))>0);
    }

}