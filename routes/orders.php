<?php
$app->get("/orders/search", function() use($app)
{
    $app->render("provider_search.html.twig");
})->name('create-order');

//ruta que va a la busqueda de productos le paso el id del proveedor
$app->get("/orders/products/:id", function($id) use($app)
{
    $proveedores = ORM::for_table('proveedor')->find_one($id);
    $_SESSION['idproveedor'] = $id; //Guardo en una variable de session el id de proveedor 
    $gamas = ORM::for_table('gama')->distinct()->find_many();
    $app->render("product_search.html.twig",array('proveedores' => $proveedores, 'gamas' => $gamas));
})->name('searchProduct');

$app->get("/orders/provider/:namesearch", function($namesearch) use($app)
{
    $proveedores = ORM::for_table('proveedor')->where_like('nombreproveedor',"%{$namesearch}%")->order_by_asc('nieproveedor')->find_many();
    $app->render("providerindex.html.twig", array('proveedores' => $proveedores));
});





