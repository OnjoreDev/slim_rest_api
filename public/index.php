<?php

declare(strict_types=1);

//imports
use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use Slim\Handlers\Strategies\RequestResponseArgs;
use App\Middleware\AddJsonResponseHeader;
use App\Controllers\ProductIndex;
use App\Controllers\Product;
use App\Middleware\GetProduct;
use Slim\Routing\RouteCollectorProxy;

//create a constant for the firectory DIR part
define('APP_ROOT',dirname(__DIR__));
require APP_ROOT . '/vendor/autoload.php';

//create an object of the container class
$builder = new ContainerBuilder;
//create a container and use addDefinitions function to pass the path to the config file then call the buld method
$container = $builder->addDefinitions(APP_ROOT .'/config/definitions.php')->build();


//set the container-it configures app with dependency injection container
AppFactory::setContainer($container);

//create an  instance of the app 
$app = AppFactory::create();

//collect the routes
$collector = $app->getRouteCollector();

$collector->setDefaultInvocationStrategy(new RequestResponseArgs);
//middleware to parse the request data into json
$app->addBodyParsingMiddleware();


//add middleware to check for exceptions because we do not add status codes directly in slim
$error_middleware = $app->addErrorMiddleware(true,true,true);

//since we want to display the error messages in json format and not html 
$error_handler = $error_middleware->getDefaultErrorHandler();

//now set the type 
$error_handler->forceContentType('application/json');

//use the AddJsonResponseHeader middleware class to all the routes
$app->add(new AddJsonResponseHeader);

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

});


$app->run();
