<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Factory\ResponseFactory;
use App\Repositories\UserRepository;

class RequireLogin{
    
    public function __construct(private ResponseFactory $factory, private UserRepository $repository){}

    public function __invoke(Request $request, RequestHandler $handler):Response
    {
        //check if user f is set in the session
        if(isset($_SESSION['user_id'])){

              //find the user by id
             $user = $this->repository->find('id',$_SESSION['user_id']);

             //if user is found handle the request
             if($user){
                //find logged in user id in the request 
               $request = $request->withAttribute('user',$user);

               return  $handler->handle($request);
             }
        }

        //if the session does not contain the user_id
        $response = $this->factory->createResponse();

        //write an unauthorized header in the response body
        $response->getBody()->write('Unauthorized');
        
        //return response status code 401 invalid credentials
        return $response->withStatus(401);

    }

}
