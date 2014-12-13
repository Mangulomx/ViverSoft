<?php
require_once '../../vendor/autoload.php';
require_once '../../config.php';
require_once '/functions/function_orders.php';
$opcion = $_REQUEST['opcion'];
$orderHTML='';
switch($opcion)
{
    case '1':
    
        if(isset($_POST['parametro1'])&&isset($_POST['parametro2']))
        {
            $orderHTML = getOrdersProduct($_POST['parametro1'],$_POST['parametro2']);
            echo trim($orderHTML);
            
        }
        break;
    
    case '2':
    
        if(isset($_GET['idProducto']))
        {
            $producto = getOrdersProduct1($_GET['idProducto']);
            //Compruebo si el tipo es string, ya que me devolvera una cadena con los errores del formulario
            if(gettype($producto)==="string")
            {
                echo $producto;
            }

            {
               $orderHTML = ltrim($producto['id']).'#'.$producto['nombre_producto'].'*'.$producto['precioVenta'].'*'.$producto['descripcion'].'*'.$producto['descatalogado'].'@'.$producto['cantidad_stock'];
               echo $orderHTML = trim(preg_replace('/\s+/',' ', $orderHTML));
            }
        }
}