<?php
class Authenticator{
    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    private function CheckPassword($existingHash, $inputPassword){
        
        $hash = crypt ($inputPassword, $existingHash);
        return $hash === $existingHash;
    }

    private function encryptPassword($password){
        $hashFormat = "$2y$10$";
        $saltLength = 22;
        $salt = $this -> makeSalt ($saltLength);
        return crypt($password, $hashFormat . $salt);
    }

    private function makeSalt($length){
        $urs = md5(uniqid(mt_rand(), true));
        $b64 = base64_encode($urs);
        $md64 = str_replace("+",".", base64_encode($b64));
        return substr(  $md64, 0, $length);
    }

public function login($body){
    $username = $body -> username;
    $password = $body -> password;
   
    $code = 0;
    $payload = "";
    $remarks = "";
    $message = "";
   
    try{
       $sqlStr = "SELECT recipeid, username, password, token FROM accounts_tbl WHERE username = ?";
       $stmt = $this->pdo->prepare($sqlStr);
       $stmt->execute([$username]);

       if ($stmt->rowCount() > 0){
        $result = $stmt->fetchALL()[0];
        if($this -> CheckPassword($password, $result['password'])){
            $code = 200;
            $remarks = "Successful";
            $message = "Login successful";
            $payload = array("recipeid" => $result['recipeid'], "username" => $result['username'], "token" => $result['token']);
    
        }

    else{
        $code = 400;
        $payload = null;
        $remarks = "Failed to login";
        $message = "Username does not exist";
    }
}
    else{
        $code = 400;
        $payload = null;
        $remarks = "Failed to login";
        $message = "Username does not exist";
    }

    }
        catch (\PDOException $e) { 
            $errmsg = $e->getMessage();
            $code = 400;
        }  
        return array("code" => $code, "payload" => $payload, "remarks"=> $remarks,"message"=> $message);
}
    public function makeAccounts($body){
        $values = [];
        $errmsg = "";
        $code = 0;

        $body -> passsword = $this -> encryptPassword($body);

    foreach($body as $value){
        array_push($values, $value);
    }

    try{
        $sqlString = "INSERT INTO accounts_tbl (recipeid, username. password) VALUES (?,?,?)";
        $sql = $this->pdo->prepare($sqlString);
        $sql ->execute($values);
        $code = 200;
        $data = null;

        return array("code" => $code, "data" => $data);
    }
        catch (\PDOException $e) { 
            $errmsg = $e->getMessage();
            $code = 400;
        }     
        
        return array("errmsg" => $code,"code"=> $code);
    }

}
?>