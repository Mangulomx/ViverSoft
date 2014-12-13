<?php
/*

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
*/

$app->get('/lineorder/addCart/:id',function($id) use($app)
{
    
   if(isset($_GET['add-cart'])) 
   {
       $str='';
       //compruebo si el array de sesion del carrito era creado si no es creado procedemos a crearlo
       if(!isset($_SESSION['cart-items']))
       {
           $_SESSION['cart-items'] = array();
       }
       $cantidad = $app->request()->get('inputcantidad');
       $nombre = $app->request()->get('inputnombre');
       if(array_key_exists($id, $_SESSION['cart-items']))
       {
           $str='/exist/';
       }
       else
       {
           $str='/added/';
           $_SESSION['cart-items'][$id] = $cantidad; 
       }
       $app->redirect("/lineorder/products/{$str}/{$id}/{$nombre}");
       
   }
});

$app->get('/lineorder/products/:action/:id/:nombre', function($action,$id,$nombre) use($app)
{
    //Compruebo si existen datos en 
    if(count($_SESSION['cart-items'])> 0)
    {
        if($action == 'added')
        {
            $app->flash('info',"producto {$nombre} fue añadido a tu carrito");
        }
        if($action == 'exist')
        {
            $app->flash('info',"producto {$nombre} ya existe en su carrito");
        }
        $ids="";
        $str="";
        foreach($_SESSION['cart-items'] as $id => $value)
        {
            $ids.=$id.',';
            $str.=$value.'*';
        }
        //Elimino el caracter del final especificado
        $ids = rtrim($ids,',');
        $str = rtrim($str,'*');
        
        //Separa la cadena str donde tengo los precios por su caracter delimitador y obtengo un array
        $cantidad = explode('*',$str);
        $productos = ORM::for_table('producto')->where_in($ids)->order_by_asc('nombre_producto')->find_many();
        $app->render('showCart.html.twig',array('productos'=>$productos, 'cantidad'=>$cantidad));
        
    }
});