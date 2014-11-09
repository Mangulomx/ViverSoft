<?php
#Muestro listado de empleados
$app->get("/empleado", function() use($app)
{
    $empleados = listadoEmpleados();
    $app->render("employee.html.twig", array('empleados' => $empleados));
})->name('employeeList');


#Alta de empleados
$app->map("/AltaEmpl", function() use($app)
{
    $error = array();
    if(isset($_POST['create-employee']))
    {
        $nif = $app->request->post('inputnif');
        $nombre = $app->request->post('inputempleado');
        $apellido1 = $app->request->post('inputapellido1');
        $apellido2 = $app->request->post('inputapellido2');
        $email = $app->request->post('inputemail');
        $puesto = $app->request->post('inputpuesto');
        $telefono = $app->request->post('inputphone');
        $usuario = existeUsuario($nif);
        //Valido los posibles errores
        if(empty($nombre))
        {
            $error[] = 'El nombre del empleado es obligatorio.';
        }
        if(empty($apellido1))
        {
            $error[] = 'El primer apellido es obligatorio.';
        }
        if(empty($apellido2))
        {
            $error[] = 'El segundo apellido es obligatorio.';
        }
        if(empty($puesto))
        {
            $error[] = 'El puesto es obligatorio.';
        }
        if(count($error)==0)
        {
            $AltaEmpleado = ORM::for_table('empleado')->create();
            $AltaEmpleado->nif = $nif;
            $AltaEmpleado->nombre = $nombre;
            $AltaEmpleado->apellido1 = $apellido1;
            $AltaEmpleado->apellido2 = $apellido2;
            $AltaEmpleado->puesto = $puesto;
            $AltaEmpleado->email = $email;
            $AltaEmpleado->telefono = $telefono;
            if($usuario)
            {
                $AltaEmpleado->usuario_idusuario = $usuario;
            }
            $AltaEmpleado->save();
            $app->redirect($app->urlFor('employeeList'));
        }
        else
        {
            $app->flash('error',$error);
            $app->redirect($app->urlFor('AltaEmpleado'));
        }
    }
    $app->render("createEmployee.html.twig");
})->name('AltaEmpleado')->VIA('GET','POST');

function existeUsuario($nif)
{
    return ORM::for_table('empleado')->
    table_alias('emp')->
    select('emp.usuario_idusuario')->
    Where('emp.nif',$nif)->
    find_one();  
}
function listadoEmpleados()
{
    return ORM::for_table('empleado')->
    table_alias('emp')->
    select('emp.*')->
    order_by_asc('emp.idempleado')->find_many();
}