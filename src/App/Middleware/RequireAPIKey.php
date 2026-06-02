<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Factory\ResponseFactory ;
use App\Repositories\UserRepository;

class RequireAPIKey{
     
     public function __construct(private ResponseFactory $factory, private UserRepository $repository){

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
          
          $api_key = $request->getHeaderLine('X-API-Key');

          //generate api_key_hash
          $api_key_hash = hash_hmac('sha256',$api_key,$_ENV['HASH_SECRET_KEY']);

          //obtain api_key value from the database
          //look for api_key_hash in the database
          $user = $this->repository->find('api_key_hash',$api_key_hash);
           //if user is not found
          if($user === false){
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