<?php
require_once '../vendor/autoload.php';
require_once '../config.php';
$Puesto = array('Jefe','Empleado','Comercial');
$opcion = $_GET['opcion'];
$identificador = $_GET['id'];

switch($opcion)
{
    case '1':
    case '2':
    {
        $empleado = getEmpleado($identificador);
        $es_admin = ($empleado['admin']==1)?true:false;
        if($opcion ==='1')
        {
            $str = <<<EOD
            <fieldset class='col-lg-12 col-sm-12'>
            <legend>Datos empleado</legend>
            <div class='row'>
                <div class='col-md-4'>
                        <div class='form-group'>
                            <label for='nieempleado'>NIE</label>
                            <input type='text' class='form-control' id='nieempleado' name='nieempleado' value='{$empleado['nieempleado']}' />
                        </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-md-4'>
                        <div class='form-group'>
                            <label for='nombre'>NOMBRE</label>
                            <input type='text' class='form-control' id='nombre' name='nombre' value='{$empleado['nombre']}'/>
                        </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-md-4'>
                        <div class='form-group'>
                            <label for='apellido1'>APELLIDO 1</label>
                            <input type='text' class='form-control' id='apellido1' name='apellido1' value='{$empleado['apellido1']}' />
                        </div>
                </div>
                <div class='col-md-1'>
                    
                </div>
                <div class='col-md-4'>
                        <div class='form-group'>
                            <label for='apellido2'>APELLIDO 2</label>
                            <input type='text' class='form-control' id='apellido2' name='apellido2' value='{$empleado['apellido2']}'/>
                        </div>
                </div>
            </div>
              <div class='row'>
                    <div class='col-md-6'>
                        <div class='form-group'>
                            <label for='email'>EMAIL</label>
                            <input type='email' class='form-control' id='email' name='email' value='{$empleado['emailemp']}'/>
                        </div>
                    </div>
                    <div class='col-md-1'>
                    </div>
                    <div class='col-md-4'>
                        <div class='form-group'>
                            <label for='telefono'>TELEFONO</label>
                            <input type='text' class='form-control' id='telefono' name='telefono' value='{$empleado['telefono']}'/>
                        </div>
                    </div>
            </div>
            <div class='row'>
                <div class='col-md-4'>
                        <div class='form-group'>
                            <label for='nombre'>PUESTO</label>
                            <input type='text' class='form-control' id='puesto' name='puesto' value='{$Puesto[$empleado['puesto']]}'/>
                        </div>
                </div>
            </div>
            </fieldset>
EOD;
        }
        else 
        {
            $str = <<<EOD
        <fieldset class='col lg-12 col sm-12'>
        <legend>Datos usuario</legend>
        <div class='row'>
            <div class='col-lg-4 col-md-4 col-sm-4'>
               <div class='form-group'>
                  <label for='username'>Usuario</label>
                  <input type='text' class='form-control' id='username' name='username' value='{$empleado['username']}' />
               </div>
            </div>
            <div class='col-lg-1 col-md-1 col-sm-1'>
            </div>
            <div class='col-lg-4 col-md-4 col-sm-4'>
                  <label for='password'>Contraseña</label>
                  <input type='text' class='form-control' id='password' name='password' value='{$empleado['contrasenia']}' />
            </div>
        </div>
         <div class='row'>
            <div class='col-lg-6 col-md-6 col-sm-6'>
               <div class='form-group'>
                  <label for='username'>Email</label>
                  <input type='text' class='form-control' id='email' name='email' value='{$empleado['email']}' />
               </div>
            </div>
            <div class='col-lg-1 col-md-1 col-sm-1'>
            </div>
            <div class='col-lg-4 col-md-4 col-sm-4'>
                  <div class='form-group'>
                  <label for='administrator'>¿Es administrador?</label>
                  <input type='checkbox' class='form-control' id='administrator' name='administrator' checked='{$es_admin}' />
                  </div>
            </div>
        </div>
                            
        </fieldset>
            
EOD;
        }
         
        
        echo $str;
    }
}
//Obtengo los datos de los empleados según el nie del empleado que le he pasado
function getEmpleado($id)
{
    return ORM::for_table('empleado')->
        table_alias('emp')->
        select('emp.*')->
        select('emp.email','emailemp')->
        select('u.*')->
        where('emp.id',$id)->
        left_outer_join('usuario',array('emp.usuario_idusuario', '=', 'u.id'),'u')->order_by_asc('emp.id')->find_one();
}