<?php

$app->post('/lineapedido/insertar/:id',function($id) use($app)
{
    $error = array();
    if(isset($_POST['add-cart']))
    {
        $preciounidad = $app->request()->post('inputprecio');
        $cantidad = $app->request()->post('inputcantidad');
        $productos = ORM::for_table('producto')->where('id',$id)->find_one();
        if(!$productos)
        {
            $error[] = "No existe productos con este {$productos['id']}";
        }
        if(count($error)==0)
        {
            $lineapedido = ORM::for_tabe('lineapedido')->create();
            $lineapedido->cantidad = $cantidad;
        }
    }
})->name('lineorder_create');
