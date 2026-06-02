<?php

use App\Database;
use Slim\Views\PhpRenderer;


//the key is the database class
// the value is an anonymous function that returns the object
return [
    Database::class => function(){
        return new Database(host:$_ENV['DB_HOST']?? '127.0.0.1',
                            name:$_ENV['DB_NAME'] ??'api_db',
                            user:$_ENV['DB_USER'] ?? 'root',
                            password: $_ENV['DB_PASS']??''  
         );
    },

    //definitions that tells the slim where to find the viw
    PhpRenderer::class => function(){
         $renderer = new PhpRenderer(__DIR__ . '/../views');
         //configuration for checking where layout file is
         $renderer->setlayout('layout.php');
         
         return $renderer;
    }


    ];


