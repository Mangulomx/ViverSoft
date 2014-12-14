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
}