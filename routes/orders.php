<?php
$app->get("/orders/search", function() use($app)
{
    $app->render("provider_search.html.twig");
})->name('create-order');

//ruta que va a la busqueda de productos le paso el id del proveedor
$app->get("/orders/products/:id", function($id) use($app)
{
    $proveedores = ORM::for_table('proveedor')->find_one($id);
    $_SESSION['idproveedor'] = $id;
    $gamas = ORM::for_table('gama')->distinct()->find_many();
    $app->render("product_search.html.twig",array('proveedores' => $proveedores, 'gamas' => $gamas));
})->name('searchProduct');

$app->get('/orders/list(/:id(/:filtro))', function($id=0, $filtro='') use($app, $authorized)
{
    
    $pedidos = ListadoPedidos($filtro,$id);
    $app->render('orderindex.html.twig',array('pedidos' => $pedidos, 'is_admin' => $authorized));
})->name('orderList');

$app->get('/orders/products/list/:id', function($id) use($app)
{
    $productos = getOrdersProducts($id);
    if($id ==='-1')
    {
        $app->flash('error','Tienes que seleccionar un producto');
    }
    $app->render('showOrderProduct.html.twig',array('productos'=>$productos));
})->name('showOrderProduct');

$app->get("/orders/provider/:namesearch", function($namesearch) use($app)
{
    $proveedores = ORM::for_table('proveedor')->where_like('nombreproveedor',"%{$namesearch}%")->order_by_asc('nieproveedor')->find_array();
    $app->render("providerindex.html.twig", array('proveedores' => $proveedores));
});
//Ruta para insertar los pedidos
$app->get("/orders/insert", function() use($app)
{
    $empleado = ORM::for_table('empleado')->select('id')->where('usuario_idusuario',$_SESSION['user_id'])->find_one();
    $cliente = 'proveedor';
    if(!$empleado)
    {
        $app->flash('error','La cuenta de usuario con las que has iniciado sesion, tiene que estar asociado a un empleado registrado en la base de datos');
        $app->redirect('/lineorder/products/view');
    }
    try
    {
        $nums = range(1,12);
      
        do
        {
            $num_referencia = getNumReferencia($nums);
            $existe = ORM::for_table('pedido')->where('referencia',$num_referencia)->find_one();
        }while($existe);
        $insertOrder = ORM::for_table('pedido')->create();
        $insertOrder->referencia = $num_referencia;
        $insertOrder->fechapedido = date('Y/m/d');
        $insertOrder->estado = 0;
        $insertOrder->empleado_id = $empleado->id;
        $insertOrder->proveedor_id =  $_SESSION['idproveedor'];
        $insertOrder->save();
        $app->redirect("/lineorder/insert/$cliente");
    }
    catch(PDOException $e)
    {
        $app->flash('error','Ha ocurrido un error en la base de datos, no se ha insertado ningún pedido'.$e->getMessage());
        $app->redirect('/lineorder/products/view');
    }
});

//Borrar un pedido

$app->post("/orders/delete", function() use($app)
{
    if(isset($_POST['eliminar']))
    {
        $deleteorder = ORM::for_table('pedido')->find_one($_POST['eliminar']);
        if($deleteorder)
        {
            $deleteorder->delete();
            $app->flash('success','pedido eliminado correctamente');
            $app->redirect($app->urlFor('orderList'));
        }
    }
    
})->name('deleteOrder');

function getOrdersProducts($identificador)
{
    return ORM::for_table('producto')->where('id',$identificador)->find_one();
}

function ListadoPedidos($filtro,$id)
{
    $query = ORM::for_table('pedido')->
    table_alias('p')->
    select_many('p.id','p.referencia','p.fechapedido','p.estado')->
    select_expr('SUM(l.preciounidad * l.cantidad)','total')->
    inner_join('lineapedido',array('l.pedido_idpedido','=','p.id'),'l');
    /*
    switch($filtro)
    {
        case 'filtro_referencia':
        {
            $query = $query->where_like('p.referencia',"%{$id}%");
            break;
        }
        case 'filtro_estado':
        {
            $query = $query->where('p.estado',$id);
            break;
        }
        case 'fitro_fechas':
        {
            $query = $query->where('fechapedido' >= $id AND 'fechapedido' <= $id);
            break;
        }
        default:
            $filtro ="nada";
    }
    */
    $data = $query->group_by('p.id')->group_by('p.referencia')->group_by('p.fechapedido')->group_by('p.estado')->order_by_asc('p.id')->find_many();
    return $data;    
}
function getNumReferencia($nums)
{
    $str="";
    shuffle($nums); 
    $nums=array_slice($nums, 0, 4); 
    sort($nums);
    foreach ($nums as $value)
    {
        $str.=$value;
    }     
    return $str;
}
