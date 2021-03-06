<?php

use Iter\LogsRepository;
use Iter\Validator;

// autoload composer
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Middleware\MethodOverrideMiddleware;
use DI\Container;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

// Logs repository on the json file
$repo = new LogsRepository(__DIR__ . '/../db/logs.txt');

// Create Container
$container = new Container();
AppFactory::setContainer($container);

// Set view in Container
$container->set('renderer', function () {
    return Twig::create(__DIR__ . '/../templates', []);
});

// Create App
$app = AppFactory::create();
$app->add(MethodOverrideMiddleware::class);

// Add Twig-View Middleware
$app->add(TwigMiddleware::createFromContainer($app, 'renderer'));

//Add IP Middleware
$app->add(new \RKA\Middleware\IpAddress(true, []));

$app->addErrorMiddleware(true, true, true);

$router = $app->getRouteCollector()->getRouteParser();

$app->get('/', function ($request, $response) {
    return $response->write('<a href="/logs">All logs</a>');
});

$app->get('/logs', function ($request, $response) use ($repo, $router) {
    $page = $request->getQueryParam('page', 1);
    $logsOnPage = $request->getQueryParam('per', 10);

    if ($page < 1) {
        return $response->withRedirect('/logs', 302);
    }

    $logs = $repo->getLogsByPage($page, $logsOnPage);
    
    $params = [
        'logs' => $logs,
        'page' => $page,
        'per' => $logsOnPage,
    ];
    return $this->get('renderer')->render($response, 'base.twig', $params);
})->setName('logs');

$app->post('/logs', function ($request, $response) use ($repo, $router) {
    $logFormParams = $request->getParsedBody();
    $validator = new Validator();
    $errors = $validator->validate($logFormParams);

    if (count($errors) === 0) {
        $id = count($repo->all()) + 1;
        $ip = $request->getAttribute('ip_address');
        $date = date('d.m.Y H:i:s');

        $newLog = array_merge([$id, $ip, $date], $logFormParams);
        
        $repo->save($newLog);

        if (!$request->isXhr()) {
            return $response->withRedirect($router->urlFor('logs'));
        }
        $newLogJson = json_encode($newLog);
        return $response->write($newLogJson);
    }
    $params = [
        'head' => $headerContentType,
        'log' => $logFormParams,
        'errors' => $errors
    ];
    $response = $response->withStatus(422);
    if (!$request->isXhr()) {
        return $this->get('renderer')->render($response, 'base.twig', $params);
    }
    $jsonParams = json_encode($errors);
    return $this->get('renderer')->render($response, 'form.twig', $params);
});

$app->get('/api/all', function ($request, $response) use ($repo) {
    return $response->withJson(['logsCount' => $repo->getLogsCount()], 200);
});

$app->get('/api/logs/{id}', function ($request, $response, $args) use ($repo) {
    $page = $args['id'];
    $logs = $repo->getLogsByPage($page);
    return $response->withJson($logs, 200);
});
$app->run();
