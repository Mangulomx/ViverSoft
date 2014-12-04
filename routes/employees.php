<?php
#Muestro listado de empleados
$app->get("/empleado", function() use($app, $authorized)
{
    $empleados = array();
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
        $puesto = (int)$app->request->post('selectpuesto');
        $telefono = $app->request->post('inputphone');
        $usuario = (int)$app->request->post('selectuser');
        //Valido los posibles errores
        if(empty($nif))
        {
            $error[] = 'El nif no puede estar vacio';
        }
        else 
        {
            $existe_nif = ORM::for_table('empleado')->where('nieempleado',$nif)->find_one();
            if($existe_nif)
            {
                $error[] = 'El nif para ese empleado ya existe';
            }
        }
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
        if(!filter_var($email,FILTER_VALIDATE_EMAIL))
        {
            $error[]="El email es incorrecto.";
        }
        if($puesto =='-1')
        {
            $error[] = 'Debes seleccionar un puesto.';
        }
        else
        {
            $es_jefe = ORM::for_table('empleado')->where('puesto',0)->find_one();
            if($es_jefe)
            {
                if($puesto===0)
                {
                    $error[] = "Ya existe un jefe, no puedes crea otro";
                }
            }
        }
        if($usuario!='-1')
        {
            $usuariotmp = ORM::for_table('empleado')->where('usuario_idusuario',$usuario)->find_one();
            if($usuariotmp)
            {
                $error[] = 'Ya existe un empleado con dicho usuario';
            }
        }
        if(count($error)==0)
        {
            $AltaEmpleado = ORM::for_table('empleado')->create();
            $AltaEmpleado->nieempleado =$nif;
            $AltaEmpleado->nombre = $nombre;
            $AltaEmpleado->apellido1 = $apellido1;
            $AltaEmpleado->apellido2 = $apellido2;
            $AltaEmpleado->email = $email;
            $AltaEmpleado->telefono = $telefono;
            if($puesto!=='-1')
            {
                $AltaEmpleado->puesto = $puesto;
            }
            if($usuario!='-1')
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


#Muestro los empleados según su identificador
$app->get('/editEmp/:id', function($id) use($app,$users)
{
    $empleado = getEmpleado($id);
    $app->render('editEmployee.html.twig', array('empleado' => $empleado,
    'usuarios' => $users));
})->name('EditEmployee');

#Actualizar empleado según su identificador
$app->post('/editEmp/:id', function($id) use($app)
{
    $error = array();
    $expr_tlfno = "/^[9|6|7][0-9]{8}$/";
    if(isset($_POST['update']))
    {
        $nie = $app->request()->post('inputnie');
        $nombre = $app->request()->post('inputname');
        $apellido1 = $app->request()->post('inputsurname');
        $apellido2 = $app->request()->post('inputsurname1');
        $email = $app->request()->post('inputemail');
        $puesto = (int)$app->request()->post('selectpuesto');
        $telefono = $app->request()->post('inputphone');
        $usuario =(int)$app->request()->post('selectusuario');
        #Valido si hay errores
        if(empty($nie))
        {
            $error[] = "El nie no puede estar vacio";
        }
        
        else
        {
            $empleado = ORM::for_table('empleado')->
            where_not_equal('id',$id)->
            where('nieempleado',$nie)->find_one();
            if($empleado)
            {
                $error[] = "Ya existe un empleado con este nif";
            }
        }
        
        if(empty($nombre))
        {
            $error[] = "El nombre del empleado no puede estar vacio";
        }
        if(empty($apellido1))
        {
            $error[] = "El apellido 1 no puede estar vacio";
        }
        if(empty($apellido2))
        {
            $error[] = "El apellido 2 no puede estar vacio";
        }
        if(!empty($email))
        {
            if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                
                $error[] = "El email no es valido";
            }
        }
        if(!empty($telefono))
        {
            if(!preg_match($expr_tlfno, $telefono))
            {
                $error[] = "El teléfono no es correcto";
            }
        }
        if($puesto===-1)
        {
            $error[] = "Debes seleccionar un puesto";
        }
        else
        {
            $es_jefe = ORM::for_table('empleado')->where('puesto',0)->
            where_not_equal('id',$id)->
            find_one();
            if($es_jefe)
            {
                if($puesto===0)
                {
                    $error[] = "Ya existe un jefe, no puedes crea otro";
                }
            }
        }
        #Si no hay errores procedo a crear el empleado
      
        if(count($error)==0)
        {
           
            $empleado = ORM::for_table('empleado')->find_one($id);
            $empleado->nieempleado = $nie;
            $empleado->nombre = $nombre;
            $empleado->apellido1 = $apellido1;
            $empleado->apellido2 = $apellido2;
            $empleado->email = $email;
            $empleado->puesto = $puesto;   
            if(!empty($telefono))
            {
                $empleado->telefono = $telefono;
            }
            if($usuario!==-1)
            {
                $empleado->usuario_idusuario = $usuario;
            }  
            $empleado->save();
            $app->redirect($app->urlFor('employeeList'));
        }
        else
        {
            $app->flash('error',$error);            
            $app->redirect($app->urlFor('updateEmployee',array('id' => $id)));
        }
    }
    
})->name('updateEmployee');
  

#Borro el empleado
$app->post("/delete", function() use($app)
{
    if(isset($_POST['borrar']))
    {
        $empleado = ORM::for_table('empleado')->find_one($_POST['borrar']);
        if($empleado)
        {
            $empleado->delete();
            $app->redirect($app->urlFor('employeeList'));
        }
    }
})->name('employeeDelete');

function listadoEmpleados()
{
   //Hacer un left join con la tabla de usuario
    return ORM::for_table('empleado')->
    table_alias('emp')->
    select('emp.*')->
    select('emp.id','idempleado')->
    select('u.*')->
    left_outer_join('usuario',array('emp.usuario_idusuario', '=', 'u.id'),'u')->find_array();
}

function getEmpleado($identificador)
{
    //Hago un left join con la table de usuario y obtengo el identificador
    return ORM::for_table('empleado')->
    table_alias('emp')->
    select('emp.*')->
    select('emp.id','idempleado')->
    select('emp.email','emailemp')->
    select('u.*')->
    where('emp.id',$identificador)->
    left_outer_join('usuario',array('emp.usuario_idusuario', '=', 'u.id'),'u')->order_by_asc('emp.nieempleado')->find_one();
}

