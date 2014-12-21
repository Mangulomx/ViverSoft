<?php
    include_once 'functions.php';
    $create_file = false; #variable para comprobar que el fichero de configuración existe o no
    $create_db = false;
    $create_tables = false;
    $create_admin = false;
    $path = "config.php";
    if(isset($_POST['instalar']))
    {
        $server = test_input($_POST['servidor']); //Nombre del servidor
        $db_name = test_input($_POST['dbname']); 
        $user = test_input($_POST['user']);
        $user_pass = test_input($_POST['password0']);
         #Crear el archivo config.php si no existe, en caso de que no exista
        if(!file_exists("../../".$path))
        {
           $create_file = create_config($path,$server,$db_name);
        }
        #Creacion de la base de datos
        $database_created = create_db($server,$db_name);
        #Creacion de las tablas de la base de datos
        if($database_created) //Si se ha creado correctamente la base de datos
        {
            $tables_created = create_tables($server,$db_name); //procedo a crear las tablas
        }
        #Creacion del usuario administrador
        if($tables_created) //Si he creado las tablas correctamente
        {
            $user_created = create_AdminUser($server,$db_name); //creo el usuario administrador
        }
       
    } 
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link href="../libs/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
        <title>Instalador de la aplicación</title>
    </head>
    <body>
       
        <div class="panel panel-primary">
            <div class="panel panel-heading">
                <h3 class="panel-title">Instalador</h3>
            </div>           
        <form action="instalador.php" method="post">
            <div class="panel-body">
            <?php
                if($create_file)
                {
                    echo "<div class='alert alert-warning'>El fichero config.php ya existe</div>";
                }
            
            ?>
                <div class="form-group">
                    <label for="servidor">Servidor</label>
                    <input type="text" name="servidor" id="servidor" class="form-control" placeholder="servidor" />
                </div>
                <div class="form-group">
                     <label for="dbname">Base de datos</label>
                     <input type="text" name="dbname" id="dbname" class="form-control" placeholder="Nombre de la base de datos" />
                </div>
                <div class="form-group">
                    <label for="user">Usuario</label>
                    <input type="text" name="user" id="user" class="form-control" placeholder="Usuario bd" />
                </div>
                <div class="form-group">
                    <label for="password0">Contraseña</label>
                    <input type="password" name="password0" id="password0" class="form-control" placeholder="Contraseña bd" />
                </div>
            </div>
            <div class="panel-body">
                <h3>Usuario administrador</h3>
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="nombre usuario" />
                </div>
                <div class="form-group">
                    <label for="password1">Contraseña</label>
                    <input type="password" name="password1" id="password1" class="form-control" placeholder="contraseña" />
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="email" />
                </div>
            </div>
            <div class="panel-footer">
                <button type="submit" name="instalar" class="btn btn-primary">Instalar</button>
                <button type="reset" name="Limpiar" class="btn btn-primary">Limpiar</button>
            </div>
        </form>
        </div>
        <?php
            if(isset($_POST['instalar']))
            {
                if($database_created)
                {
                    echo "<div class='alert alert-success'>Base de datos, creada con exito</div>";
                }
                if($tables_created)
                {
                    echo "<div class='alert alert-success'>Tablas creadas con exito</div>";
                }
                if($user_created)
                {
                    echo "<div class='alert alert-success'>Usuario creado con exito</div>";
                }
                
            }
        ?>
    </body>
</html>