<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;
use Valitron\Validator;
use App\Repositories\UserRepository;

class SignUp{

    //dependency injection
    public function __construct(private PhpRenderer $view, private Validator $validator, private UserRepository $repository){
       //create the validation rules
       $this->validator->mapFieldsRules([
          'name'=> ['required'],
          'email'=> ['required','email'],
          'password' => ['required',['lengthMin',6]],
          'password_confirmation'=>['required',['equals','password']]
       ]);
    }
    public function new(Request $request, Response $response):Response{
           return $this->view->render($response,'signup.php');
    }

    //how to respond to the post request
    public function create(Request $request, Response $response):Response{
        //get data from the form
        $data = $request->getParsedBody();

        //we call the validator to validate the message
        $this->validator = $this->validator->withData($data);

        if(! $this->validator->validate()){
        //test if the errors are displaying     
        //print_r($this->validator->errors());
        
        //now let us render the response properly
          return $this->view->render($response,'signup.php',[
            //variable that will display errors on sign up page
            'errors'=>$this->validator->errors(),
            //keeps the data that was initially provided by the user
            'data' =>$data
          ]);
        }
        //hash the password
        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);

        //generate an api key from random bytes
        $api_key =  bin2hex(random_bytes(16));
       
        //set the value of apikey
        //$data['api_key'] = $api_key;
        $data['api_key'] = '';
       
        //provide a value for api_key_hash
        //$data['api_key_hash'] = '';
        $data['api_key_hash'] = hash_hmac('sha256',$api_key,$_SERVER['HASH_SECRET_KEY']);
        //print_r($data);
        //lets add the data to the database table
        $this->repository->create($data);

        $response->getBody()->write("Here is your API key: $api_key");
       return $response;

    }
}