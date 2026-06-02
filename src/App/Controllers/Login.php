<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\UserRepository;
use Slim\Views\PhpRenderer;

class Login{
      public function __construct(private UserRepository $repository, private PhpRenderer $view){}

      //function to view the login page
      public function new(Request $request,Response $response):Response{

          return $this->view->render($response,'login.php');
      }

      //function to log in user
      public function create(Request $request, Response $response):Response{
        //get the data from the form
        $data = $request->getParsedBody();

        //find user based on provided email address
        $user = $this->repository->find('email',$data['email']);
        
        //verify the password such that if an error occurs it is returned
        //password verify checks if the plain-text matches the database password-hash
        if($user && password_verify($data['password'],$user['password_hash'])){
           //use session to enable login
           //assign the session user_id variable a value of the logged in user's id 
           $_SESSION['user_id'] = $user['id'];

            //if login is successful redirect back to homepage
            return $response->withHeader('Location','/')->withStatus(302);
        }
        //if login fails
        return $this->view->render($response,'login.php',[
            'data' => $data ,//data to repopulate fields
            'error' => 'Invalid login'
        ]);

      }

      //function to logout user
      public function destroy(Request $request, Response $response):Response{
        //destroy the session
         session_destroy();
         
         //redirect user back to home page
         return $response->withHeader('Location','/')->withStatus(302);
      }

}