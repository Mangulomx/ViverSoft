<?php
/*Ruta para añadir productos al carrito*/
$app->post('/lineorder/addCart/:id',function($id) use($app)
{
   
       $str='';
       //compruebo si el array de sesion del carrito era creado si no es creado procedemos a crearlo
       if(!isset($_SESSION['cart-items']))
       {
           $_SESSION['cart-items'] = array();
       }
    
       $cantidad = $app->request()->post('inputcantidad');
       $nombre = $app->request()->post('inputnombre');
       if(array_key_exists($id, $_SESSION['cart-items']))
       {
           $str='exist';
       }
       else
       {
           $str='added';
           $_SESSION['cart-items'][$id] = $cantidad; 
           
       }
      
       $app->redirect("/lineorder/products/$str/$id/$nombre");
       
});


/*Ruta para añadir productos al carrito, segun la accion elegida*/
$app->get('/lineorder/products(/:action(/:id(/:nombre)))', function($action='',$id='',$nombre='') use($app)
{
    //Compruebo si existen datos en la variable de session carrito
     //Compruebo si existen datos en la variable de session
    $info = array();
    $dataproduct = array();
    if(!isset($_SESSION['cart-items']))
    {
        $app->flash('error','para ver listado de carritos tienes que seleccionar productos primeramente');
        $app->redirect($app->urlFor('create-order'));
    }
    else
    {
    if(count($_SESSION['cart-items'])> 0)
    {
        $template = ($action == 'view' || $action ==='removed') ? 'cartlist.html.twig' : 'showCart.html.twig';
        if($action == 'added')
        {
            $info[] = "producto {$nombre} fue añadido a tu carrito";
        }
        if($action == 'exist')
        {
            $info[] = "producto {$nombre} ya existe en su carrito";
        }
        if($action == 'removed')
        {
            $info[] = "producto con id {$id} se ha eliminado de su carrito ";
        }
        if(count($info)>0)
        {
            $app->flash('info',$info);
        }
        $ids="";
        $str="";
        foreach($_SESSION['cart-items'] as $id => $value)
        {
            //var_dump("id".$id);
            $ids.=$id.',';
            $str.=$value.'*';
        }
        //Elimino el caracter del final especificado
        $ids = rtrim($ids,',');
        $str = rtrim($str,'*');
      
        
        $encontrado = strrpos($ids, ",");
        $query = ORM::for_table('producto');
        if($encontrado)
        {
            $ids = explode(',',$ids);
            //Separa la cadena str donde tengo los precios por su caracter delimitador y obtengo un array
            $cantidad = explode('*',$str);
            $query = $query->where_in('id',$ids);
        }
        else
        {
            $cantidad = array();
            $cantidad[0] = $str;
            $query = $query->where_in('id',array($ids));
        }
      
        $productos = $query->order_by_asc('nombre_producto')->find_array();
        $dataproduct = array('productos'=> $productos, 'cantidad'=> $cantidad, 'action' => $action);
    }
    else
    {
        $template = 'cartlist.html.twig';
        $productos = array();
        $productos = $_SESSION['cart-items'];
        $dataproduct = array('productos'=> $productos, 'action' => $action);
    }
        //var_dump(array($dataproduct,$_SESSION['cart-items']));die();
         $app->render($template,$dataproduct); 
    }
})->name('cartList');
//ruta para borrar linea de pedido
$app->post('/lineorder/delete', function() use($app)
{
    if(isset($_POST['delete-productCart']))
    {
        $id = $app->request()->post('delete-productCart');
        $nombre = $app->request()->post('inputnombre');
        //Borro el articulo que le he pasado
        //Con unset lo que hago es destruir la sesion
        unset($_SESSION['cart-items'][$id]);
        $app->redirect("/lineorder/products/removed/$id/$nombre");
    }
})->name('deleteLine');

$app->map('/lineorder/insert/:cliente', function($cliente) use($app)
{
    if(!isset($_SESSION['cart-items']))
    {
         $app->redirect($app->urlFor('create-order'));
         $app->flash('error','No tienes carritos');
    }
    //Si existe la variable de sesion
    else
    {
        if(count($_SESSION['cart-items'])>0)
        {
            ORM::get_db()->beginTransaction();
            try
            {
                $idpedido = ORM::for_table('pedido')->max('id');
                foreach($_SESSION['cart-items'] as $id => $cantidad)
                {
                    $producto = ORM::for_table('producto')->select_many('id','precioVenta','precioProveedor','cantidad_stock')->where('id',$id)->find_one();
                    $preciounidad = ($cliente==='proveedor') ? $producto->precioProveedor : $producto->precioVenta;
                    $lineorder_insert = ORM::for_table('lineapedido')->create();
                    $lineorder_insert->pedido_idpedido = $idpedido;
                    $lineorder_insert->producto_idproducto = $producto->id;
                    $lineorder_insert->cantidad = $cantidad;
                    $lineorder_insert->preciounidad = $preciounidad;
                    $lineorder_insert->save();
                    //Actualizo cantidad en la table productos
                    $cantidad_stock =$producto->cantidad_stock;
                    $cantidad_stock+=$cantidad;
                    $update_amount = ORM::for_table('producto')->find_one($id);
                    $update_amount->set('cantidad_stock',$cantidad_stock);
                    $update_amount->save();
                    unset($_SESSION['cart-items'][$id]);
                }
                ORM::get_db()->commit();
                $app->flash('success',"pedido {$idpedido} creado correctamente");
                $app->redirect($app->urlFor('orderList'));
            }
            catch(PDOException $e)
            {
                 ORM::get_db()->rollBack();
                 $app->flash('error','Ha ocurrido un error en la base de datos no fue insertado ninguna linea de pedido'.$e->getMessage());
                 $app->redirect('/orders/insert');
            }
        }
    }
})->via('GET','POST');

