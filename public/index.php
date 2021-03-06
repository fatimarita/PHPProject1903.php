<?php

use Appli\Controller\AboutController;
use Appli\Controller\ContactController;
use Appli\Controller\HomeController;
use Generic\App;
use Generic\Middlewares\TrailingSlashMiddleware;
use Generic\Renderer\TwigRenderer;
use Generic\Router\Router;
use Generic\Router\RouterMiddleware;
use GuzzleHttp\Psr7\ServerRequest;

$rootDir = dirname(__DIR__);

// Chargement de l'autoloader
require_once $rootDir .  '/vendor/autoload.php';

// Création du conteneur


$builder = new DI\ContainerBuilder();
$builder->addDefinitions($rootDir.'/config/config.php');
$container = $builder->build();

// Création de le requête
$request = ServerRequest::fromGlobals();


// Initialiser TWIG
$twig = new TwigRenderer($rootDir .  '/templates');

// Ajout des routes dans le routeur
$router = $container->get(Router::class);
$router->addRoute('/', $container->get(HomeController::class), 'homepage');
$router->addRoute('/contact', $container->get(ContactController::class), 'contact');



// Création de la réponse
$app = new App([
    $container->get(TrailingSlashMiddleware::class),
    $container->get(RouterMiddleware::class)
]);
$response = $app->handle($request);

// Renvoi de la réponse au navigateur
\Http\Response\send($response);
