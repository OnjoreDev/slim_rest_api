<?php

declare(strict_types=1);

use App\Controllers\ProductIndex;
use App\Controllers\Product;
use App\Middleware\GetProduct;
use Slim\Routing\RouteCollectorProxy;
use App\Middleware\RequireAPIKey;
use App\Controllers\Home;
use App\Controllers\SignUp;
use App\Middleware\AddJsonResponseHeader;


//create a route for home
//since it has an invoke we dont have to specify the method name
$app->get('/',Home::class);
$app->get('/signup',[SignUp::class,'new']);
//post route for signup
$app->post('/signup',[SignUp::class,'create']);

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
