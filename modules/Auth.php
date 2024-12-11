<?php
class Authentication
{

    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function isAuthorized()
    {
        $headers = getallheaders();
        return $headers['Authorization'] === $this->getUserToken();
    }

    private function getUserToken()
    {
        $headers = getallheaders();
        $sqlString = "SELECT token FROM accounts_tbl WHERE username=?";
        $stmt = $this->pdo->prepare($sqlString);
        $stmt->execute([$headers['X-Auth-User']]);
        $res = $stmt->fetchAll()[0];
        return $res['token'];
    }

    private function generateHeader()
    {
        $header = [
            "alg" => "HS256",
            "typ" => "JWT",
            "app" => "recipe_api",
            "dev" => "Monkey Co"
        ];
        return base64_encode(json_encode($header));
    }

    private function generatePayload($owner_id, $username)
    {
        $payload = [
            "the owner_id" => $owner_id,
            "username" => $username,
            "by" => "Alberto and Herrera",
            "email" => "kianchasetrent@gmail.com",
            "date" => date_create(),
            "exp" => date("Y-m-d H:i:s")
        ];
    }
    private function generateToken($owner_id, $username)
    {
        $header = $this->generateHeader();
        $payload = $this->generatePayload($owner_id, $username);
        $signature = hash_hmac('sha256', $header . $payload, TOKEN_KEY);
        return "$header.$payload." . base64_encode($signature);
    }

    private function passchecker($existingHash, $inputPassword)
    {
        $hash = crypt($inputPassword, $existingHash);
        return $hash === $existingHash;
    }

    public function encriptor($password)
    {
        $hashFormat = "$2y$10$";
        $saltLength = 22;
        $salt = $this->generateSalt($saltLength);
        return crypt($password, $hashFormat . $salt);
    }

    public function generateSalt($length)
    {
        $urs = md5(uniqid(mt_rand(), true));
        $base64String = base64_encode($urs);
        $mb64String = str_replace("+", ".", $base64String);
        return substr($mb64String, 0, $length);
    }

    public function updateToken($token, $username)
    {
        $errmsg = "";
        $code = 0;

        try {
            $sqlString = "UPDATE accounts_tbl SET token = ? WHERE username = ?";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute([$token, $username]);
            $code = 200;
            $data = null;

            return array("code" => $code, "data" => $data);
        } catch (\PDOException $e) {
            $errmsg = "hehe";
            $code = 400;
        }

        return array("errmsg" => $code, "code" => $code);
    }

    public function login($body)
    {
        $username = $body->username;
        $password = $body->password;

        $code = 0;
        $payload = "";
        $remarks = "";
        $message = "";

        try {
            $sqlString = "SELECT owner_id,  username, password, token FROM accounts_tbl WHERE username=?";
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->execute([$username]);


            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetchAll()[0];
               
                if ($this->passchecker($password, $result['password'])) {
                    $code = 200;
                    $remarks = "success";
                    $message = "Logged in successfully";
                    $token = $this->generateToken($result['owner_id'], $result['username']);
                    $token_arr = explode(".", $token);
                    $this->updateToken($token_arr[2], $result['username']);
                    $payload = array("owner_id" => $result['owner_id'], "username" => $result['username'], "token" => $token_arr[2]);
                } else {
                    $code = 401;
                    $remarks = "failed";
                    $message = "Incorrect Password 1";
                    $payload = null;
                }
            } else {
                $code = 401;
                $remarks = "failed";
                $message = "User not found";
                $payload = null;
            }
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            $remarks = "failed";
            $code = 400;
            $message = "Database error. Please try again later.";
        }

        return array("payload" => $payload, "remarks" => $remarks, "message" => $message, "code" => $code);
    }


     public function addAcc($body)
 {
        $values = [];
         $errmsg = "";
        $code = 0;
   $body->password = $this->encriptor($body->password);

        foreach ($body as $value) {
             array_push($values, $value);
        }

        try {
            $sqlString = "INSERT INTO accounts_tbl (owner_id, username, password) VALUES (?,?,?)";
           $sql = $this->pdo->prepare($sqlString);
             $sql->execute($values);

            $code = 200;
            $data = null;
         $message = "Data successfully added";

         return array("data" => $data, "code" => $code, "message" => $message);
     } catch (\PDOException $e) {
            $errmsg = $e->getMessage();

          $code = 400;
       }

         return array("errmsg" => $errmsg, "code" => $code);
     }
}
