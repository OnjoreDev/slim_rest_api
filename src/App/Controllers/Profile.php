<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;
use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;

class Profile{
      //inject dependency for PhpRenderer class
      public function __construct(private PhpRenderer $view){

      }

      public function show(Request $request, Response $response):Response{
             //show a specific view
             $user = $request->getAttribute('user');
             
             $encryption_key = Key::loadFromAsciiSafeString($_ENV['ENCRYPTION_KEY']);

             //decrypt thr api key brfore displaying it
             $api_key = Crypto::decrypt($user['api_key'],$encryption_key);

             //pass the decrypted value to the view
             return $this->view->render($response,'profile.php',[
                'api_key' => $api_key,

             ]);

      }

}