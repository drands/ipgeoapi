<?php
namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\GeoIPSearch;
use App\Token;
use App\Upload;
use App\Util;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->add(new \Tuupola\Middleware\HttpBasicAuthentication([
    'path' => '/admin',
    'realm' => 'Protected',
    "users" => Auth::getUsers(),
]));


//  ROUTES
//
$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello");
    return $response;
});

$app->get('/ip', function (Request $request, Response $response, $args) {
    $params = $request->getQueryParams();
    $token = $params['token'] ?? null;
    $ip = $params['ip'] ?? null;

    if (!$token || !$ip) {
        $response->getBody()->write("Missing parameters");
        return $response;
    }
    if (!Token::validate($token)) {
        $response->getBody()->write("Invalid token");
        return $response;
    }

    $geoIPSearch = new GeoIPSearch();
    $record = $geoIPSearch->search($ip);
    $json = $geoIPSearch->parse($record);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/me', function (Request $request, Response $response, $args) {
    $params = $request->getQueryParams();
    $token = $params['token'] ?? null;
    $ip = Util::getIP();

    if (!$token) {
        $response->getBody()->write("Missing parameters");
        return $response;
    }
    if (!Token::validate($token)) {
        $response->getBody()->write("Invalid token");
        return $response;
    }

    $geoIPSearch = new GeoIPSearch();
    $record = $geoIPSearch->search($ip);
    $json = $geoIPSearch->parse($record);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type', 'application/json');
});


//ADMIN ROUTES
//
$app->get('/admin', function (Request $request, Response $response, $args) {
    $html = file_get_contents(__DIR__ . '/../src/views/admin/index.html');
    $response->getBody()->write($html);
    return $response;
});

$app->post('/admin/upload', function (Request $request, Response $response, $args) {
    $uploadedFiles = $request->getUploadedFiles();
    $uploadedFile = $uploadedFiles['file'];

    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
        $filename = Upload::moveUploadedFile($uploadedFile);
        $response->getBody()->write(json_encode(['status' => 'ok']));
    } else {
        $response->getBody()->write(json_encode(['status' => 'error']));
    }

    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();