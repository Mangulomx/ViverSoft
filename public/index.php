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

$twig = $view->getInstance();

$authorized = false;
if(isset($_SESSION['user_id']))
{
    $user = ORM::for_table('usuario')->
    select('usuario.*')->
    where('id',$_SESSION['user_id'])->
    find_one();
    if($user!==false)
    {
        $authorized = ($user->admin == 1);
    }
   
    $twig->addGlobal('authorized',$authorized);
 }
$users = ORM::for_table('usuario')->find_many();
$twig->addGlobal('users',$users);
require('../routes/session.php');
#Se va a la portada principal de mi aplicación
require('../routes/frontpage.php');
#Gestion de usuarios de la aplicación
require('../routes/users.php');
#Gestion de empleados de la aplicación
require('../routes/employees.php');
#Gestión de categorias o gamas de la aplicación
require('../routes/gama.php');
$app->run();