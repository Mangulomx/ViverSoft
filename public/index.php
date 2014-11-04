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

$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);

$app->get('/', function() use($app){
    if(isset($_POST['entrar']))
    {
        $password = $app->request->post('user');
        $username = $app->request->post('password');
        $user = ORM::for_table('usuario')->where('username',$username)->find_one();
        $isValid = false;
        if($user)
        {
            #Compruebo que las contraseñas coincidan
            if(password_verify($password,$user->contrasenia))
            {
                $isValid = true;
            }
        }
        if(!$isValid)
        {
            $app->flash('error','Usuario y/o contraseña incorrecta');
            $app->redirect($app->urlFor('login'));
        }
        else
        {
            $_SESSION['user_id'] = $user->idusuario;
        }
    }
    $app->render('login.html.twig',array('title' => 'login'));
})->name('login');
$app->run();