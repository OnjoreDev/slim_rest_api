<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\ProductRepository;


class ProductIndex{

      //create a constructor to inject the dependency between the container and this class
      public function __construct(private ProductRepository $repository){}
     
      public function __invoke(Request $request, Response $response):Response{
      //we use dependency injection here via the constructor
      $data = $this->repository->getAll();
         //convert the array into json object format
         $body = json_encode($data);

         $response->getBody()->write($body);
         return $response;
      }

}