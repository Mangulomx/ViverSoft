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

$app->map('/', function() use($app){
    if(isset($_POST['entrar']))
    {
        $username = $app->request->post('user');
        $password = $app->request->post('password');
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
            $app->redirect($app->urlFor('frontpage'));
        }
    }
    $app->render('login.html.twig',array(
        'title' => 'login',
        'url' => $app->urlFor('login')
        
        ));
})->name('login')->via('GET','POST');

#Se va a la portada principal de mi aplicación
$app->get('/portada', function() use($app)
{
    if(!isset($_SESSION['user_id']))
    {
        $app->flash('error','Usuario no autorizado');
        $app->redirect('/');
    }
    $app->render('frontpage.html.twig');
    
})->name('frontpage');
$app->run();