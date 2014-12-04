<?php
if(isset($_POST['parametro1']))
{
    $valor = $_POST['parametro1'];

    $productos = GetProducts($valor);
    $str = count($productos)>1 ? "productos" : "producto";
    
    $formproducthtml = MostrarProduct($str,$productos);
    echo $formproducthtml;
    function GetProducts($valor)
    {
        return ORM::for_table('producto')->
        where_like('nombre_producto','%'+$valor+'%')->find_many();
    }
}

function MostrarProduct($str,$productos)
{
    
  if($productos)
  {
       $optionproducts = function($productos)
       {
          $data="";
          foreach($productos as $producto)
          {
              $data.="<option value='{$producto['id']}'>{$producto['nombre']}</option>";
          }
          return $data;
       };
       
       $descatalogado = function($productos)
       {
         $booleano = ($productos['descatalogado']===1) ? true : false;
         return $booleano;
       };
     $cadena = <<<EOD
    <form class='form-horizontal' method='POST' role='form' action={{ urlFor('lineorder_create',{'id':{\$productos['id']}}) }}>
    <h2>Listado de {$str}</h2>
    <div class='form-group'>
        <label class='col-md-4 col-xs-4 control-label' for='selectproductname'>Nombre producto:</label>
        <div class='col-md-5 col-xs-5'>
            <select name='selectproductname' class='form-control'>
                {$optionproducts}
            </select>
        </div>
    </div>
    <div class='form-group'>
       <label class='col-md-4 col-xs-4 control-label' for='descripciontext'>Descripcion:</label>
       <div class='col-md-5 col-xs-5'>
                <textarea cols='3' rows='4'>{$productos['descripcion']}</textarea>
       </div>
    </div>
    <div class='form-group'>
       <label class='col-md-4 col-xs-4 control-label' for='descripciontext'>Descripcion:</label>
       <div class='col-md-5 col-xs-5'>
                <textarea cols='3' rows='4' name='descripciontext' id='descripciontext'>{$productos['descripcion']}</textarea>
       </div>
    </div>
    <div class='form-group'>
       <label class='col-md-4 col-xs-4 control-label' for='inputprecio'>Precio:</label>
       <div class='col-md-5 col-xs-5'>
                <input type='text' name='inputprecio' id='inputprecio' value='{$productos}' /> <span> &euro</span>
       </div>
    </div>
    <div class='form-group'>
       <label class='col-md-4 col-xs-4 control-label' for='descatalogado'>Descatalogado:</label>
       <div class='col-md-5 col-xs-5'>
                <input type='checkbox' name='descatalogado' checked='{$descatalogado}' />
       </div>
    </div>
    <div class='form-group'>
       <label class='col-md-4 col-xs-4 control-label' for='inputcantidad'>Cantidad:</label>
       <div class='col-md-5 col-xs-5'>
                <input type='text' name='inputcantidad' id='inputcantidad'/><span>En iventario {$productos['cantidad_stock'] } productos </span>
       </div>
    </div>
    <div class="form-group text-center">
        <div class="col-md-8">
            <button type="submit" id="add-cart" name="add-cart" class="btn-default btn-success">Añadir al carrito</button>
            <a id='view-cart' name='view-cart'>Ver carrito</a>
        </div>
    </div>
    
    </form>
    
EOD;
  }
  return $cadena;
}

 