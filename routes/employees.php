<?php
#Muestro listado de empleados
$app->get("/empleado", function() use($app, $authorized)
{
    $empleados = listadoEmpleados();  
    $app->render("employee.html.twig", array('empleados' => $empleados, 'is_admin' => $authorized ));
})->name('employeeList');


#Alta de empleados
$app->map("/AltaEmpl", function() use($app, $authorized,$users)
{
    $error = array();
    if(isset($_POST['create-employee']))
    {
        $nif = $app->request->post('inputnif');
        $nombre = $app->request->post('inputnombre');
        $apellido1 = $app->request->post('inputapellido1');
        $apellido2 = $app->request->post('inputapellido2');
        $email = $app->request->post('inputemail');
        $puesto = $app->request->post('inputpuesto');
        $telefono = $app->request->post('inputphone');
        $usuario = $app->request->post('selectuser');
        var_dump($usuario);die();
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
        if(!empty($usuario))
        {
            $usuariotmp = ORM::for_table('usuario')->where('idusuario',$usuario)->find_one();
            if($usuariotmp)
            {
                $error[] = 'Ya existe un usuario con ese identificador';
            }
        }
        if(count($error)==0)
        {
            $AltaEmpleado = ORM::for_table('empleado')->create();
            $AltaEmpleado->nieempleado = $nif;
            $AltaEmpleado->nombre = $nombre;
            $AltaEmpleado->apellido1 = $apellido1;
            $AltaEmpleado->apellido2 = $apellido2;
            $AltaEmpleado->puesto = $puesto;
            $AltaEmpleado->email = $email;
            $AltaEmpleado->telefono = $telefono;
            if(!empty($usuario))
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
    $app->render("createEmployee.html.twig", array('usuarios' => $users, 
        'is_admin' => $authorized,
        'users' =>$users));
})->name('AltaEmpleado')->VIA('GET','POST');

function listadoEmpleados()
{
   //Hacer un left join con la tabla de usuario
    return ORM::for_table('empleado')->
    table_alias('emp')->
    select('emp.*')->
    select_many('u.id','u.email')->
    left_outer_join('usuario',array('emp.usuario_idusuario', '=', 'u.id'),'u')->find_many();
}

