<?php
require '../vendor/autoload.php';
require_once '../config.php';
session_start();
$app = new \Slim\Slim(
        array(
        'view' => new \Slim\Views\Twig(),
        'templates.path' => '../templates'
        )
);
$view = $app->view();
$view->parserOptions = array(
  'debug' => true,
  'cache' => '../cache'
);
//Hook para añadir lo que la plantilla necesita globalmente
$app->hook('slim.before', function () use ($app) {
   $app->view()->appendData(array(
       'login' => isset($_SESSION['user_id'])
   )); 
});
$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);

require('../routes/session.php');
#Se va a la portada principal de mi aplicación
require('../routes/frontpage.php');
#Gestion de usuarios de la aplicación
require('../routes/users.php');
#Gestion de empleados de la aplicación
require('../routes/employees.php');
$app->run();