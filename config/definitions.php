<?php

use App\Database;

//the key is the database class
// the value is an anonymous function that returns the object
return [
    Database::class => function(){
        return new Database(host:'127.0.0.1',
                            name:'api_db',
                            user:'root',
                            password:''  
         );
    }
];

