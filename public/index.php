<?php

declare(strict_types=1);

//imports
use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use Slim\Handlers\Strategies\RequestResponseArgs;
use Dotenv\Dotenv;

//create a constant for the firectory DIR part
define('APP_ROOT',dirname(__DIR__));
require APP_ROOT . '/vendor/autoload.php';

// 2. Load the .env file right here
$dotenv = Dotenv::createImmutable(APP_ROOT); // Uses your APP_ROOT constant to find the file
$dotenv->load();

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
$app->addErrorMiddleware(true,true,true);

//routes for product
require APP_ROOT . '/config/routes.php';

$app->run();
