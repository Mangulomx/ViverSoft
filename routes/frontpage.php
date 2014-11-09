<?php

$app->get('/portada', function() use($app)
{
    if(!isset($_SESSION['user_id']))
    {
        $app->flash('error','Usuario no autorizado');
        $app->redirect('/');
    }
    $app->render('frontpage.html.twig');
    
})->name('frontpage');
