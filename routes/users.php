<?php

$app->get('/users', function() use ($app)
{
    $user = listadoUsuarios();
    $is_admin = EsAdministrador();
    $app->render('users.html.twig',array('users' => $user, 'is_admin' => $is_admin));
})->name('userList');

$app->post('/DeleteUsers', function() use($app)
{
    if (isset($_POST['eliminar']))
    {
      
        $usuarios = $app->request()->post('rusuario');
        foreach($usuarios as $valor)
        {
            $query = \ORM::for_table('usuario')->find_one($valor);
            if($query)
            {
                $query->delete();
            }
        }
        
        $app->redirect($app->urlFor('userList'));
    }
})->name('userDelete');

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


