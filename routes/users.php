<?php

$app->get('/users', function() use ($app, $authorized, $users)
{
    $app->render('users.html.twig',array('users' => $users, 'is_admin' => $authorized));
})->name('userList');

#Borrar usuarios
$app->post('/delete', function() use($app)
{
    if(isset($_POST['eliminar']))
    {
        $usuarios = $app->request()->post('rusuario');
        foreach($usuarios as $valor)
        {
            if($_SESSION['user_id'] !== $valor)
            {
                $query = \ORM::for_table('usuario')->find_one($valor);
                if($query)
                {
                    $query->delete();
                }
            }
            else
            {
                $app->flash('error',"No puedes borrar el id ".$_SESSION['user_id']." con que te has logueado");
            }
        }
        $app->redirect($app->urlFor('userList'));
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


//Obtengo los usuarios segun su identificador
function getUser($id_user)
{
    return ORM::for_table('usuario')->
    select('usuario.*')->
    where('id',$id_user)->
    find_one();
}


