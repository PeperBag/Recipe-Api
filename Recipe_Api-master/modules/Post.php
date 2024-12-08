<?php
class Post{
    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function postRecipes($body){
        $values = [];
        $errmsg = "";
        $code = 0;

    foreach($body as $values){
        array_push($values, $values);
    }

    try{
        $sqlString = "INSERT INTO recipe_tbl (name, description, category, cookingtime, servings) VALUES (?,?,?,?,?)";
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


    public function postIngredients($body){
        $values = [];
        $errmsg = "";
        $code = 0;

    foreach($body as $values){
        array_push($values, $values);
    }
     try{
        $sqlString = "INSERT INTO ingredients_tbl (name) VALUES (?)";
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