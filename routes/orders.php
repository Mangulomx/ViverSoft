<?php
$app->get("/orders/search", function() use($app)
{
    $gamas = listadoGamas();
    $app->render("order_search.html.twig", array('gamas' => $gamas));
})->name('create-order');


//Obtengo el listado de diferentes gamas que hay en el almacen.
function listadoGamas()
{
    return ORM::for_table('gama')->distinct()->find_many();
}


