<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Scopewell\Database;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

/**
 * Instantiate App
 *
 * In order for the factory to work you need to ensure you have installed
 * a supported PSR-7 implementation of your choice e.g.: Slim PSR-7 and a supported
 * ServerRequest creator (included with Slim PSR-7)
 */
$app = AppFactory::create();

$app->setBasePath('/api');

// Add Routing Middleware
$app->addRoutingMiddleware();

/**
 * Add Error Handling Middleware
 *
 * @param bool $displayErrorDetails -> Should be set to false in production
 * @param bool $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool $logErrorDetails -> Display error details in error log
 * which can be replaced by a callable of your choice.
 
 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Define app routes
$app->get('/hello/{name}', function (Request $request, Response $response, $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
});

$app->get('/geography', function (Request $request, Response $response, $args) {
    $geo = json_decode(file_get_contents("../data/geography.json"), true);
    foreach($geo as $name => &$stuff) {
        unset($stuff["connections"]);
    }
    $response->getBody()->write(json_encode($geo, JSON_PRETTY_PRINT));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/placements', function (Request $request, Response $response, $args) {
    $response->getBody()->write(file_get_contents("../data/placements.json"));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/state', function (Request $request, Response $response, $args) {
   $response->getBody()->write(file_get_contents("../data/state.json"));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/polygons', function (Request $request, Response $response, $args) {
    $response->getBody()->write(file_get_contents("../data/polygons.json"));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/load/{user}/{gameid}', function (Request $request, Response $response, $args) {
    //I really want to use extract here
    $user = $args["user"];
    $gameid = $args["gameid"];
    $placements = file_get_contents("../games/$user/$gameid/placements.json");
    $state = file_get_contents("../games/$user/$gameid/state.json");
    $json = ["success" => false, "reason" => "unknown"]; 
    if ($placements === false) {
        $json["reason"] = "Missing placements";
    } else if ($sate === false) {
         $json["reason"] = "Missing sate";
    } else {
        $json["success"] = true;
        $json["placements"] = json_decode($placements, true);
        $json["state"] = json_decode($state, true);
    }

    $response->getBody()->write(json_encode($json, JSON_PRETTY_PRINT));
    return $response->withHeader('Content-Type', 'application/json');
});


// Run app
$app->run();
