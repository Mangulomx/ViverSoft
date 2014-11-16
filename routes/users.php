<?php

$app->get('/users', function() use ($app)
{
    $user = listadoUsuarios();
    $is_admin = EsAdministrador();
    $app->render('users.html.twig',array('users' => $user, 'is_admin' => $is_admin));
})->name('userList');

#Borrar usuarios
$app->post('/delete', function() use($app)
{ 
       if(isset($_POST['eliminar']))
       {
           $user = ORM::for_table('usuario')->where('idusuario',$_POST['eliminar'])->find_one();
            if($user)
            {
                $user->delete();
                $app->redirect($app->router()->urlFor('userList'));
            }
       }
})->name("userDelete");

#Alta de usuarios
$app->map('/altausers',function() use($app)
{
    $error = array();
    if(isset($_POST['create-user']))
    {
        $username = $app->request()->post('inputuser');
        $password = $app->request()->post('inputpassword');
        $email = $app->request()->post('inputemail');
        $is_admin = (int)$app->request()->post('es_admin',0);
        
        #valido si hay errores
        
        if(empty($username))
        {
            $error[] = "El nombre de usuario no puede estar vacio";
        }
        else
        {
            $user = ORM::for_table('usuario')->
            where('username',$username)->
            find_one();
            if($user)
            {
                $error[] = "El nombre de usuario ya esta en uso";
            }
        }
        if(strlen($password)<6)
        {
            $error[] = "La contraseña debe tener un mínimo de 7 caracteres";
        }
        
        #Si no hay errores procedemos a crear el usuario
        if(count($error)==0)
        {
            $user = ORM::for_table('usuario')->create();
            $user->username = $username;
            $user->contrasenia = password_hash($password,PASSWORD_DEFAULT);
            $user->email = $email;
            $user->admin = $is_admin;
            $user->save();
            $app->flash('success','Usuario creado correctamente');
        }
        else 
        {
            $app->flash('error',$error);
        }
        $app->redirect('/altausers');
    }
    $app->render("altausers.html.twig");
})->VIA('GET','POST')->name('altausers');

#Mostrar usuario segun su identificador
$app->get('/users/:id',function($id) use($app)
{
    $user = getUser($id);
    $app->render('edituser.html.twig',array('user'=>$user));
    
})->name('edituser');

#Actualizar usuario

$app->post('/users/:id',function($id) use($app)
{
    $error = array();
    if(isset($_POST['update']))
    {
        $username = $app->request()->post('inputuser'); 
        $email = $app->request()->post('inputemail');
        $password = $app->request()->post('inputpassword');
        $esadmin = $app->request()->post('es_admin',0);
        
        //Validación de los datos
        if(empty($username))
        {
            $error[]="El usuario no puede estar vacio";
        }
        if(!empty($password)&&strlen($password)<6)
        {
            $error[] = "La contraseña no puede tener menos de 6 caracteres";
        }
        
        //Si no hay errores procedo a crear el usuario
        if(count($error)==0)
        {
            $user = ORM::for_table('usuario')->create();
            $user->username = $username;
            if(!empty($password))
            {
                $user->contrasenia = $password;
            }
            $user->email = $email;
            $user->admin = $esadmin;
            $user->save();
            $app->redirect($app->urlFor('userList'));
        }
        else
        {
            $app->flash('error',$error);
            $app->redirect($app->urlFor('edituser'));
        }
    }
    
})->name('updateuser');

function EsAdministrador()
{
    $user = ORM::for_table('usuario')->
    select('usuario.idusuario')->
    where('idusuario',$_SESSION['user_id'])->
    find_one();
    $authorized = false;
    if($user!==false)
    {
        $authorized = ($user->idusuario == 1);
    }     
    return $authorized;
}
function listadoUsuarios()
{
    return ORM::for_table('usuario')->
    select('usuario.*')->
    find_many();
}
//Obtengo los usuarios segun su identificador
function getUser($id_user)
{
    return ORM::for_table('usuario')->
    select('usuario.*')->
    where('idusuario',$id_user)->
    find_one();
}


