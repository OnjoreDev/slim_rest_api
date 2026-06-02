<?php

declare(strict_types=1);

use App\Controllers\ProductIndex;
use App\Controllers\Product;
use App\Middleware\GetProduct;
use Slim\Routing\RouteCollectorProxy;
use App\Middleware\RequireAPIKey;
use App\Controllers\Home;
use App\Controllers\SignUp;
use App\Controllers\Login;
use App\Middleware\AddJsonResponseHeader;
use App\Middleware\ActivateSession;
use App\Controllers\Profile;
use App\Middleware\RequireLogin;

//create a route group for session bound routes
$app->group('',function(RouteCollectorProxy $group){
//create a route for home
//since it has an invoke we dont have to specify the method name
        $group->get('/',Home::class);
        $group->get('/signup',[SignUp::class,'new']);
        //post route for signup
        $group->post('/signup',[SignUp::class,'create']);
        //route for sign up success
        $group->get('/signup/success',[SignUp::class,'success']);



        //get route for login
        $group->get('/login',[Login::class,'new']);
        //add post route for login
        $group->post('/login',[Login::class,'create']);
     
     
        //route to log out the user
        $group->get('/logout',[Login::class,'destroy']);
        //profile view
        $group->get('/profile',[Profile::class,'show'])->add(RequireLogin::class);
})->add(ActivateSession::class);


//create a route group
$app->group('/api',function(RouteCollectorProxy $group){
//create our first route to get all products
$group->get('/products',ProductIndex::class);

//create a product
$group->post('/products',[Product::class ,'create']);

//now create group for routes that utilise the same middleware
$group->group('',function(RouteCollectorProxy $group){
    //get a single
    $group->get('/products/{id:[0-9]+}',Product::class.':show');

    //update a product
    $group->patch('/products/{id:[0-9]+}',[Product::class,'update']);

    //delete a product
    $group->delete('/products/{id:[0-9]+}',[Product::class,'delete']);
})->add(GetProduct::class);

})->add(RequireAPIKey::class)
->add(AddJsonResponseHeader::class);
