<?php

function getOrdersProduct($valueproduct, $valuegama)
{
    $strHTML='';
    $errores = array();
     if($valuegama == '-1')
     {
        $errores[] = "Tiene que se seleccion una categoria";
     }
     if(!empty($valueproduct)&&!preg_match("/^\D{3,}$/",$valueproduct))
     {
        $errores[] = "El campo producto tiene que tener al menos 3 digitos alfanumericos";
     }
     if(count($errores)==0)
     {
        $productos = GetProducts($valueproduct,$valuegama);
        if(!$productos)
        {
            $errores[] = "No se encuentran productos con el valor de busqueda {$valueproduct}";
        }
        else
        {
            $str = count($productos)>1 ? "Numeros de productos econtrados: " : "Numero de producto encontrado: ";
            $strHTML = ShowProduct($str,$productos);
        }
      }
      else
      {
            foreach($errores as $error)
            {
                $strHTML.= "<div class='alert alert-info'>{$error}</div>";
            }
      }
      return $strHTML;
    
}
function getOrdersProduct1($identificador)
{
    $strHTML='';
    $errores = array();
    if($identificador ==='-1')
    {
        $errores[] = "Tienes que seleccinar un producto";
    }
    if(count($errores)==0)
    {
        $productos = GetProducts1($identificador);
        if(!$productos)
        {
            $errores[]="No hay productos con este identificador {$identificador}";
        }
        else 
        {
            $strHTML = $productos;
        }
    }
    else 
    {
        foreach($errores as $error)
        {
            $strHTML.="<div class='alert alert-info'>{$error}</div>";
        }
    }
    
    return $strHTML;
    
}
function GetProducts1($identificador)
{
    return ORM::for_table('producto')->
    where('id',$identificador)->find_one()->as_array();
}
function GetProducts($valor,$valor1)
{
    return ORM::for_table('producto')->
    select('producto.id','idproducto')->
    select('producto.*')->
    select('gama.*')->
    inner_join('gama',array('producto.gama_id','=','gama.id'))->
    where('gama.id',$valor1)->
    where_like('nombre_producto',"%{$valor}%")->find_many();
}

function ShowProduct($str,$productos)
{
  $cadena=""; 
  if($productos)
  {
       $optionproducts = function($productos)
       {
          $data="";
          foreach($productos as $producto)
          {
              $data.="<option value='{$producto['idproducto']}'>{$producto['nombre_producto']}</option>";
          }
          return $data;
       };
       
     $num = count($productos);
     $cadena = <<<EOD
    <form class='form-horizontal' method='POST' role='form' action={{ urlFor('lineorder_create',{'id':{\$productos['id']}}) }}>
    <p class='style-blue'>{$str} {$num} </p>
    <div class='form-group'>
        <label class='col-md-4 col-xs-4 control-label' for='selectproductname'>Nombre producto:</label>
        <div class='col-md-5 col-xs-5'>
            <select name='selectproductname' id='selectproductname' class='form-control'>
                <option value='-1' selected>Seleccione un producto</option>
                {$optionproducts($productos)}
            </select>
        </div>
    </div>
    </form>
EOD;
  }
  return $cadena;
}