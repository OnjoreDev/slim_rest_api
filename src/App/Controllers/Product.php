<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\ProductRepository;
use Valitron\Validator;

class Product{

public function __construct(private ProductRepository $repository,private Validator $validator){
        $this->validator->mapFieldsRules([
           'name'=>['required'],
           'size'=>['required','integer',['min',1]],
        ]);
}


//function to show a specific record
public function show(Request $req, Response $resp, string $id):Response{
         $product = $req->getAttribute('product');

         //convert the array to a json object
         $body = json_encode($product);

         //write the response
         $resp->getBody()->write($body);

         return $resp;
 }

 //function to create a specific record
 public function create(Request $request, Response $response):Response{
      $body =  $request->getParsedBody();
 
      //validate the input
      $this->validator = $this->validator->withData($body);

      if(!$this->validator->validate()){
        $response->getBody()->write(json_encode($this->validator->errors()));

        //status code for unprocessable entity
        return $response->withStatus(422);
      }

      //refer to the repository(model) to create the data
      $id = $this->repository->create($body);

      //$body = json_encode($body);
      //we get the message and last inserted id
      $body = json_encode([
        'message'=>'Product created',
        'id'=> $id
      ]);

      $response->getBody()->write($body);

      return $response->withStatus(201);
 }

 //function to update a specific record
 public function update(Request $request, Response $response,string $id):Response{
      $body =  $request->getParsedBody();
 
      //validate the input
      $this->validator = $this->validator->withData($body);

      if(!$this->validator->validate()){
        $response->getBody()->write(json_encode($this->validator->errors()));

        //status code for unprocessable entity
        return $response->withStatus(422);
      }

      //refer to the repository(model) to create the data
      $rows = $this->repository->update((int) $id,$body);

      //$body = json_encode($body);
      //we get the message and last inserted id
      $body = json_encode([
        'message'=>'Product updated',
        'id'=> $rows
      ]);

      $response->getBody()->write($body);
       
      //since the default is 200 we omit the withStatus method call
      return $response;
 }
  //delete controller function'
  public function delete(Request $request, Response $response,string $id):Response{
         //number of rows affected
        $rows = $this->repository->delete($id);
        
        //return the message and numer of rows in jon format
        $body = json_encode([
                'message'=>'Product deleted',
                'rows' =>$rows

        ]);

        $response->getBody()->write($body);

        return $response;

  }
 
}