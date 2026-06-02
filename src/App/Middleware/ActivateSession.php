<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class ActivateSession{

      public function __invoke(Request $request, RequestHandler $handler):Response{
           //chack status of the session befores tarting it
           if(session_status() !== PHP_SESSION_ACTIVE){
           //start the session
           session_start();
           }
          
          //handle the incoming request and assing it to response variable 
          $response = $handler->handle($request);

          return $response;

      }
}