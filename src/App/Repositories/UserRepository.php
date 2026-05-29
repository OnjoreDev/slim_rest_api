<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;

use PDO;

class UserRepository{

     public function __construct(private Database $db){
          
     }

     //function to be use to create the record
     public function create(array $data):void{
        $sql = 'INSERT INTO  users (name,email,password_hash,api_key,api_key_hash) VALUES (:name,:email,:password_hash,:api_key,:api_key_hash)';
       
        //establish the connection
        $pdo = $this->db->getConnection();

        //use the db class
        $stmt = $pdo->prepare($sql);

        //prevent sql injection
        //third parameter is not mandatory
        $stmt->bindValue(':name', $data["name"],PDO::PARAM_STR);
        $stmt->bindValue(':email',$data["email"],PDO::PARAM_STR);
        $stmt->bindValue(':password_hash',$data["password_hash"], PDO::PARAM_STR);
        $stmt->bindValue(':api_key',$data["api_key"],PDO::PARAM_STR);
        $stmt->bindValue(':api_key_hash',$data["api_key_hash"],PDO::PARAM_STR);

        //execute statement
        $stmt->execute();

    }

    public function find(string $column, $value):array|bool{
        $sql = "SELECT * FROM users WHERE $column = :value";

        //establish connection
        $pdo = $this->db->getConnection();

        //start prepared statement
        $stmt = $pdo->prepare($sql);
      
        $stmt->bindValue(':value',$value);

        $stmt->execute();
        //returns the array of the matching record or boolean false
        return $stmt->fetch();
    }

}
