<?php
    function create_config($path,$server,$db_name)
    {
        global $user, $user_pass;
        $string_config = <<<EOD
        <?php
                
         \$CFG = array(
        'host'=> '$server',
        'database' => '$db_name',
        'user' => '$user',
        'password' => '$user_pass'
       );
       ORM::configure(array(
       'connection_string' => 'mysql:host='.\$CFG['host'].';dbname='.\$CFG['database'],
       'username' => \$CFG['user'],
       'password' => \$CFG['password'],
       'driver_options' => array(PDO::MYSQL_ATTR_INIT_COMMAND=>'set NAMES utf8')));
EOD;
        
        try
        {
            touch("../../$path"); //Creo la ruta en la raiz del fichero mkdir
            $fh = fopen("../../$path","w+") or die("Error al crear el fichero de configuracion"); //Abro el fichero en mode de escritura
            fwrite($fh,$string_config); //escribo en el fichero
            fclose($fh); //cierro el fichero
            chmod("../../".$path,'0777');#Cambiando permisos
            $create_file = true;
            
        } catch (PDOException $e)
        { 
          die('error al crear el archivo'.$path."".$e->getMessage());
        }
        return $create_file;
    }
    
    function create_db($server,$db_name)
    {
        global $user, $user_pass, $create_db;
        try
        {
            $dbh = new PDO('mysql:host='. $server .';charset=utf8',$user,$user_pass);
            $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
            $createDB = $dbh->query('CREATE DATABASE IF NOT EXISTS '.$db_name.' DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;');
            $createDB->execute();
            $create_db = true;
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $create_db;
    }
    
    function create_AdminUser($server,$db_name)
    {
        global $user, $user_pass, $create_admin;
        $username = test_input($_POST['username']);
        $password = password_hash(test_input($_POST['password1']),PASSWORD_DEFAULT); //encriptacion de la contraseña
        $email = test_input($_POST['email']);
        try
        {    
            $dbh = new PDO("mysql:host=".$server.";dbname=".$db_name.";charset=utf8",$user,$user_pass);
            $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
            $createAdminUser = $dbh->prepare("INSERT INTO usuario(username,contrasenia,email,admin) VALUES(:usuario,:contrasenia,:email,true)");
            $createAdminUser->bindValue(':usuario',$username,PDO::PARAM_STR);
            $createAdminUser->bindValue(':contrasenia',$password,PDO::PARAM_STR);
            $createAdminUser->bindValue(':email',$email,PDO::PARAM_STR);
            $createAdminUser->execute(); 
            $create_admin = true;
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>".$e->getMessage()."</div>";
        }
        return $create_admin;
    }
    
    function create_tables($server,$db_name)
    {
        global $user, $user_pass,$create_tables;
        try
        {
            $dbh = new PDO('mysql:host='. $server .";dbname=". $db_name .";charset=utf8",$user,$user_pass); //me conecto a la base de datos
            $dbh->beginTransaction(); 
            /*Creacion de las tablas de la base de datos*/
            
            #tabla usuario
            
            $dbh->exec("CREATE TABLE IF NOT EXISTS `usuario` (
           `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
           `username` varchar(45) NOT NULL,
           `contrasenia` varchar(255) NOT NULL,
           `email` varchar(45) NOT NULL,
           `admin` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
            UNIQUE KEY `variable_username_email_uk`(`username`, `email`),
            PRIMARY KEY (`id`)) 
            ENGINE = InnoDB DEFAULT CHARSET=utf8; ");
            
          
            
            #tabla gama
            
            $dbh->exec("CREATE TABLE IF NOT EXISTS `gama` (
           `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
           `nombre`varchar(50) NOT NULL,
           `descripcion` TEXT NULL,
           `imagen` BLOB NULL,
            PRIMARY KEY (`id`))
            ENGINE = InnoDB DEFAULT CHARSET=utf8;");
            
            #tabla proveedor
            
            $dbh->exec("CREATE TABLE IF NOT EXISTS `proveedor` (
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `nieproveedor` VARCHAR(10) NOT NULL,
            `nombreproveedor` VARCHAR(45) NOT NULL,
            `direccion` VARCHAR(150) NOT NULL,
            `telefono` VARCHAR(15) NOT NULL,
            PRIMARY KEY (`id`))
            ENGINE = InnoDB DEFAULT CHARSET=utf8;");
            
            #tabla producto
            
            $dbh->exec("CREATE TABLE IF NOT EXISTS `producto` (
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `nombre_producto` VARCHAR(70) NOT NULL,
            `nombre_latin` VARCHAR(70) NULL,
            `peso` DECIMAL(15,2) UNSIGNED NULL,
            `descatalogado` TINYINT(1) NOT NULL,
            `dimensiones` VARCHAR(15) NOT NULL,
            `descripcion` TEXT NULL,
            `cantidad_stock` SMALLINT UNSIGNED NOT NULL,
            `precionVenta` DECIMAL(15,2) UNSIGNED NOT NULL,
            `precioProveedor` DECIMAL(15,2) UNSIGNED NOT NULL,
            `gama_id` int(11) UNSIGNED NOT NULL,
            `proveedor_id` int(11) UNSIGNED NOT NULL,
            PRIMARY KEY (`id`),
            INDEX `fk_producto_gama_idfk_idx` (`gama_id` ASC),
            INDEX `fk_producto_proveedor_id_idx` (`proveedor_id` ASC),
            CONSTRAINT `fk_producto_gama_id_fk`
            FOREIGN KEY (`gama_id`)
            REFERENCES `gama` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            CONSTRAINT `fk_producto_proveedor_id_fk`
            FOREIGN KEY (`proveedor_id`)
            REFERENCES `proveedor` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE)
            ENGINE = InnoDB DEFAULT CHARSET=utf8;");
            
            #tabla cliente
            
            $dbh->exec("CREATE TABLE IF NOT EXISTS `cliente` (
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `niecliente` VARCHAR(10) NOT NULL,
            `nombrecliente` VARCHAR(45) NULL,
            `apellido` VARCHAR(50) NULL,
            `telefono` VARCHAR(45) NOT NULL,
            `fax` VARCHAR(15) NOT NULL,
            `lineadireccion1` VARCHAR(50) NOT NULL,
            `lineadireccion2` VARCHAR(50) NULL,
            `ciudad` VARCHAR(50) NOT NULL,
            `region` VARCHAR(50) NULL,
            `pais` VARCHAR(10) NULL,
            `codpostal` VARCHAR(10) NULL,
            PRIMARY KEY (`id`))
            ENGINE = InnoDB DEFAULT CHARSET=utf8;");
            
            #tabla compra
            
            $dbh->exec("CREATE TABLE IF NOT EXISTS `compra` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `idtransaccion` VARCHAR(50) NOT NULL,
            `fechacompra` DATE NOT NULL,
            `ingreso` DECIMAL(15,2) UNSIGNED NOT NULL,
            `cliente_id` int(11) UNSIGNED NOT NULL,
            PRIMARY KEY (`id`),
            INDEX `compra_cliente_id_fk_idx` (`cliente_id` ASC),
            CONSTRAINT `fk_compra_cliente_id_fk`
            FOREIGN KEY (`cliente_id`)
            REFERENCES `cliente` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE)
            ENGINE = InnoDB DEFAULT CHARSET=utf8;");
            
            #tabla lineacompra
            
            $dbh->exec("CREATE TABLE IF NOT EXISTS `lineacompra` (
            `compra_idcompra` INT(11) UNSIGNED NOT NULL,
            `compra_idtransaccion` VARCHAR(50) NOT NULL,
            `producto_idproducto` INT(11) UNSIGNED NOT NULL,
            `cantidad` INT UNSIGNED NOT NULL,
            `preciounidad` DECIMAL(15,2) UNSIGNED NOT NULL,
            PRIMARY KEY(compra_idcompra, producto_idproducto),
            INDEX `lineacompra_compra_idcompra_fk_idx` (`compra_idcompra` ASC),
            INDEX `lineacompra_producto_idproducto_fk_idx` (`producto_idproducto` ASC),
            CONSTRAINT `lineacompra_compra_fk`
            FOREIGN KEY (`compra_idcompra`)
            REFERENCES `compra` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            CONSTRAINT `fk_lineacompra_producto_idproducto_fk`
            FOREIGN KEY (`producto_idproducto`)
            REFERENCES `producto` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE)
            ENGINE = InnoDB DEFAULT CHARSET=utf8;");
            
            #tabla empleado
            
            $dbh->exec("CREATE TABLE IF NOT EXISTS `empleado` (
           `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
           `nieempleado` VARCHAR(10) NOT NULL,
           `nombre` VARCHAR(45) NOT NULL,
           `apellido1` VARCHAR(50) NOT NULL,
           `apellido2` VARCHAR(50) NOT NULL,
           `email` VARCHAR(100) NOT NULL,
           `telefono` VARCHAR(10) NOT NULL,
           `puesto` int(11) NOT NULL,
           `usuario_idusuario` int(11) UNSIGNED NULL,
            UNIQUE KEY `nie_telefono_email_uk`(`nieempleado`,`telefono`,`email`),
            PRIMARY KEY (`id`),
            CONSTRAINT `fk_empleado_usuario_idusuario_fk`
            FOREIGN KEY (`usuario_idusuario`)
            REFERENCES `usuario` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
            )ENGINE = InnoDB DEFAULT CHARSET=utf8;");
            
            #tabla transportista
            
            $dbh->exec("CREATE TABLE IF NOT EXISTS `transportista` (
            `compañia` VARCHAR(50) NOT NULL,
            `nombrecontacto` VARCHAR(50) NOT NULL,
            `direccion` VARCHAR(150) NOT NULL,
            `localidad` VARCHAR(50) NULL,
            `estado` VARCHAR(10) NULL,
            `codigopostal` VARCHAR(10) NULL,
            PRIMARY KEY (`compañia`))
            ENGINE = InnoDB DEFAULT CHARSET=utf8;");
            
            #tabla pedido
            
            $dbh->exec("CREATE TABLE IF NOT EXISTS `pedido` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `fechapedido` DATE NOT NULL,
        `fechasuministro` DATE NOT NULL,
        `fechaentrega` DATE NOT NULL,
        `estado` VARCHAR(15) NOT NULL,
        `comentarios` TEXT NULL,
        `proveedor_id` int(11) UNSIGNED NOT NULL,
        `empleado_id` int(11) UNSIGNED NOT NULL,
        `transportista_compañia` VARCHAR(50) NOT NULL,
        PRIMARY KEY (`id`),
        INDEX `pedido_proveedor_id_fk_idx` (`proveedor_id` ASC),
        INDEX `pedido_empleado_id_fk_idx` (`empleado_id` ASC),
        INDEX `pedido_transportista_compañia_fk_idx` (`transportista_compañia` ASC),
        CONSTRAINT `pedido_proveedor_id_fk`
        FOREIGN KEY (`proveedor_id`)
        REFERENCES `proveedor` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
        CONSTRAINT `pedido_empleado_id_fk`
        FOREIGN KEY (`empleado_id`)
        REFERENCES `empleado` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
        CONSTRAINT `pedido_transportista_compañia_fk`
        FOREIGN KEY (`transportista_compañia`)
        REFERENCES `transportista` (`compañia`)
        ON DELETE CASCADE
        ON UPDATE CASCADE)
        ENGINE = InnoDB DEFAULT CHARSET=utf8;");
        
        #Tabla linea pedido
            
        $dbh->exec("CREATE TABLE IF NOT EXISTS `lineapedido` (
        `pedido_idpedido` INT(11) UNSIGNED NOT NULL,
        `producto_idproducto` INT(11) UNSIGNED NOT NULL,
        `cantidad` INT(11) UNSIGNED NOT NULL,
        `preciounidad` DECIMAL(15,2) UNSIGNED NOT NULL,
        INDEX `lineapedido_pedido_idpedido_fk_idx` (`pedido_idpedido` ASC),
        INDEX `lineapedido_producto_idproducto_fk_idx` (`producto_idproducto` ASC),
        CONSTRAINT `lineapedido_pedido_idpedido_fk`
        FOREIGN KEY (`pedido_idpedido`)
        REFERENCES `pedido` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
        CONSTRAINT `fk_lineapedido_producto_idproducto_fk`
        FOREIGN KEY (`producto_idproducto`)
        REFERENCES `producto` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE)
        ENGINE = InnoDB DEFAULT CHARSET=utf8;");
        
        //Relaciones ISA generalización tabla productos        
        #tabla plantas
        
        $dbh->exec("CREATE TABLE IF NOT EXISTS `plantas`(
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `exposicion_solar` varchar(45),
        `riego` varchar(45),
        `floracion` varchar(45),
        `temperatura` varchar(40),
        `cuidados` text not null,
        PRIMARY KEY(`id`))
        ENGINE = InnoDB DEFAULT CHARSET=utf8;");
        
        #tabla semillas
        
        $dbh->exec("CREATE TABLE IF NOT EXISTS `semillas`(
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `genero` varchar(50) NOT NULL,
        `marcoplantacion` varchar(30) not NULL,
        `recoleccion` VARCHAR(70),
        `profundidad_siembra` numeric(15,2),
        `observaciones` text,
        `laboreo` varchar(150),
        `propiedadesgastronomicas` text,
        PRIMARY KEY(`id`))
        ENGINE = InnoDB DEFAULT CHARSET=utf8;");
        
        #tabla fitosanitarios
        
        $dbh->exec("CREATE TABLE IF NOT EXISTS `fitosanitarios`(
        `id` INT(11) UNSIGNED NOT NULL,
        `tipofuncion` varchar(150) NOT NULL,
        `tipoenvase` varchar(30) NOT NULL,
        `composicion` VARCHAR(150) NOT NULL,
        `ambitoutilizacion` varchar(200),
         PRIMARY KEY(`id`))
        ENGINE = InnoDB DEFAULT CHARSET=utf8;");
        
        $dbh->commit();
        $create_tables = true;
        }
        catch (PDOException $ex) 
        {
            echo "<p>Ha ocurrido un error al crear las tablas ".$ex->getMessage()."</p>";
            $dbh->rollBack(); //cancelo la transaccion

        }
      
       return $create_tables;    
    }
    
    //Quitando espacios en blanco, convirtiendo caracteres especiales a html de los campos del formulario
    function test_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }