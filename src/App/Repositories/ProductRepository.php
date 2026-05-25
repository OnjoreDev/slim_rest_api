<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;

use PDO;

class ProductRepository{

    public function __construct(private Database $database){}

   //function to get all products
   public function getAll():array{
        //call the get connection class from database
        $pdo = $this->database->getConnection();
         //create the query
         $stmt = $pdo->query("SELECT * FROM products");
         //execute query by fetching records in an associative array
         return  $stmt->fetchAll(PDO::FETCH_ASSOC);
   }

   //method to get a single product
   public function getById(int $id):array|bool{
       //use an aias 
       $sql = "SELECT * FROM products WHERE id = :id";
       //connect to db
       $pdo = $this->database->getConnection();
       //use prepared statement
       $stmt = $pdo->prepare($sql);
       //bind the paraeter to its value
       $stmt->bindValue(':id',$id,PDO::PARAM_INT);
       //execute the statement
       $stmt->execute();
       //return result
       return $stmt->fetch(PDO::FETCH_ASSOC);
   }

   //method to create a product
   public function create(array $data):string{
     $sql =  "INSERT INTO products (name,description,size) VALUES(:name,:description,:size)";

     $pdo = $this->database->getConnection();

     $stmt = $pdo->prepare($sql);

     $stmt->bindValue(':name',$data['name'],PDO::PARAM_STR);

     if(empty($data['description'])){
        $stmt->bindValue(':description',null,PDO::PARAM_NULL);
     }else{
        $stmt->bindValue(':description',$data['description'],PDO::PARAM_STR);
     }

     $stmt->bindValue(':size',$data['size'],PDO::PARAM_INT);

     $stmt->execute();

     //return the id of the last inserted element
     return $pdo->lastInsertId();

   }

   //method to update a product
   public function update(int $id,array $data):int{
     $sql =  "UPDATE products SET name=:name, size=:size, description=:description WHERE id=:id";

     $pdo = $this->database->getConnection();

     $stmt = $pdo->prepare($sql);

     $stmt->bindValue(':name',$data['name'],PDO::PARAM_STR);

     if(empty($data['description'])){
        $stmt->bindValue(':description',null,PDO::PARAM_NULL);
     }else{
        $stmt->bindValue(':description',$data['description'],PDO::PARAM_STR);
     }

     $stmt->bindValue(':size',$data['size'],PDO::PARAM_INT);
     
     //hold the id
     $stmt->bindValue(':id',$id,PDO::PARAM_INT);

     $stmt->execute();

     //return the id of the last inserted element
     return $stmt->rowCount();

   }


   //delete a product
   public function delete(string $id):int{
    $sql = "DELETE FROM products where id = :id";

    $pdo = $this->database->getConnection();

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':id',$id,PDO::PARAM_INT);

    $stmt->execute();

    //return the number of rows affected
    return $stmt->rowCount();
   }

}