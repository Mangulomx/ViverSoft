<?php

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