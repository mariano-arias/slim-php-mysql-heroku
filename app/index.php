<?php //composer require firebase/php-jwt

//php -S localhost:666 -t public
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';
require_once './db/AccesoDatos.php';
require_once './middlewares/LoggerMW.php';
require_once './middlewares/AuthJWT.php';
require_once './middlewares/ValidacionMW.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';

date_default_timezone_set('America/Argentina/Buenos_Aires');

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Set base path
$app->setBasePath('/app');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes

$app->get('[/]', function (Request $request, Response $response) {    
  $response->getBody()->write("Slim Framework 4 PHP <br> Mariano Arias - Programacion 3 <br> UTN FRA - 2°C 2021");
    return $response;
});

$app->group('/login', function (RouteCollectorProxy $group){
  $group->post('[/]', \UsuarioController::class . ':VerificarLogin');
})->add(LoggerMW::class . ':LogOperacion');

$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('/file', \UsuarioController::class . ':SaveCSV');
  $group->post('/borrar', \UsuarioController::class . ':BorrarUno')->Add(ValidacionMW::class . ':ValidarSocio');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->post('[/]', \UsuarioController::class . ':CargarUno')->Add(ValidacionMW::class . ':ValidarSocio');
    $group->put('[/]', \UsuarioController::class . ':ModificarUno')->Add(ValidacionMW::class . ':ValidarSocio');
   // $group->delete('/{usuarioId}', \UsuarioController::class . ':BorrarUno')->Add(ValidacionMW::class . ':ValidarSocio');

  })->add(ValidacionMW::class . ':ValidarToken');

$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->post('/file', \ProductoController::class . ':GetCSV');
    $group->get('/file', \ProductoController::class . ':SaveCSV');
    $group->post('[/]', \ProductoController::class . ':CargarUno')->Add(ValidacionMW::class . ':ValidarSocio');
    $group->get('[/]', \ProductoController::class . ':TraerTodos');
    $group->get('/{idProducto}', \ProductoController::class . ':TraerUno');
    $group->put('[/]', \ProductoController::class . ':ModificarUno')->Add(ValidacionMW::class . ':ValidarSocio');
  })->add(ValidacionMW::class . ':ValidarToken');

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->post('[/]', \MesaController::class . ':CargarUno')->Add(ValidacionMW::class . ':ValidarSocio');
    $group->get('[/]', \MesaController::class . ':TraerTodos');
    $group->get('/{mesa}', \MesaController::class . ':TraerUno');
    $group->put('[/]', \MesaController::class . ':ModificarUno');
  })->add(ValidacionMW::class . ':ValidarToken');

$app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \PedidoController::class . ':TraerTodos');
    $group->get('/{pedido}', \PedidoController::class . ':TraerUno');
    $group->post('[/]', \PedidoController::class . ':CargarUno')
      ->Add(ValidacionMW::class . ':ValidarMozo')
      ->Add(ValidacionMW::class . ':ValidarMesa')
      ->Add(ValidacionMW::class . ':ValidarPhoto');
    $group->put('[/]', \PedidoController::class . ':ModificarUno')->Add(ValidacionMW::class . ':ValidarSector');
  })->add(ValidacionMW::class . ':ValidarToken');


$app->addBodyParsingMiddleware();

$app->run();
