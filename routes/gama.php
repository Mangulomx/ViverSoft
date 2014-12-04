<?php

$app->get("/gama", function() use($app)
{        
    $gamas = mostrarGama();
    $app->render('gama.html.twig',array('gamas' => $gamas));
})->name('gamaList');


function mostrarGama()
{
    ORM::for_table('gama')->find_many();
}


