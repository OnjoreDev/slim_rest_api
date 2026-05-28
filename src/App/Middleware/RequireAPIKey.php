<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Factory\ResponseFactory ;

class RequireAPIKey{
     
     public function __construct(private ResponseFactory $factory){

     }
    
     public function __invoke(Request $request,RequestHandler $handler):Response{
          //check for presence of api key from the request url
          //returns an array of key => value pairs from the query string
          $params = $request->getQueryParams();

          //check if there is a key named api-key
          //if( !array_key_exists('api-key',$params)){
          //we now check the request header instead of the query string
          if(! $request->hasHeader('X-API-Key')){
             //exit('API Key is missing');
             //create a response using the factory injected
             $response =  $this->factory->createResponse();
             $response->getBody()->write(json_encode('api-key missing from request'));
             //return status code 400 for bad request
             return $response->withStatus(400);
          }
          //if($params['api-key'] !== 'abc123'){
         if($request->getHeaderLine('X-API-Key') !== 'abc123'){    
            //create a factory response object
            $response = $this->factory->createResponse();

            $response->getBody()->write(json_encode('Invalid API Key'));

            //status 401 for unauthorized access
            return $response->withStatus(401);
          }

          $response = $handler->handle($request);

          return $response;

     }

}