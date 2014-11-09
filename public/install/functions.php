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
                
       ORM::configure('mysql:host='.\$CFG['host'].';dbname='.\$CFG['database']);
       ORM::configure('username',\$CFG['user']);
       ORM::configure('password',\$CFG['password']);
       ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));               
EOD;
        
        try
        {
            $file = touch("../../".$path);
            $fh = fopen("../../".$path,"w+");
            fwrite($fh,$string_config);
            fclose($fh);
            chmod("../../".$path,'0777');#Cambiando permisos
            $create_file = true;
            
        } catch (PDOException $e)
        { 
          die('error al crear el archivo'.$path."".$e->getMessage());
        }
    }
    
    
    function create_AdminUser($server,$db_name)
    {
        global $user,$user_pass;
        $sw = false;
        $username = test_input($_POST['username']);
        $password = password_hash(test_input($_POST['password1']),PASSWORD_DEFAULT);
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
            $sw = true;
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>".$e->getMessage()."</div>";
        }
        return $sw;
    }
    function create_db($server,$db_name)
    {
        $sw = false; #variable para comprobar que la base de datos se ha creado
        global $user, $user_pass;
        try
        {
            $dbh = new PDO('mysql:host='. $server .';charset=utf8',$user,$user_pass);
            $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
            $createDB = $dbh->query('CREATE DATABASE IF NOT EXISTS '.$db_name.' DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;');
            $createDB->execute();
            $sw = true;
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $sw;
    }
    function create_tables($server,$db_name)
    {
        $sw = false;
        #tabla usuario
        $tabla_usuario = <<<EOD
        CREATE TABLE IF NOT EXISTS `usuario` (
        `idusuario` int UNSIGNED NOT NULL AUTO_INCREMENT,
        `username` varchar(45) NOT NULL,
        `contrasenia` varchar(255) NOT NULL,
        `email` varchar(45) NOT NULL,
        `admin` tinyint(1) DEFAULT NULL,
        PRIMARY KEY (`idusuario`)) 
        ENGINE = InnoDB; 
EOD;

        #tabla gama
         $tabla_gama = <<<EOD
         CREATE TABLE IF NOT EXISTS `gama` (
        `idgama` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `descripcion` TEXT NULL,
        `imagen` BLOB NULL,
        PRIMARY KEY (`idgama`))
        ENGINE = InnoDB
EOD;
         
       #tabla proveedor
       $tabla_proveedor = <<<EOD
       CREATE TABLE IF NOT EXISTS `proveedor` (
       `nieproveedor` BIGINT UNSIGNED NOT NULL,
       `nombreproveedor` VARCHAR(45) NOT NULL,
       `direccion` VARCHAR(150) NOT NULL,
       `telefono` VARCHAR(15) NOT NULL,
       PRIMARY KEY (`nieproveedor`))
       ENGINE = InnoDB       
EOD;
     
       #tabla producto
       $tabla_producto = <<<EOD
       CREATE TABLE IF NOT EXISTS `producto` (
       `idproducto` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
       `nombre_producto` VARCHAR(70) NOT NULL,
       `nombre_latin` VARCHAR(70) NULL,
       `peso` DECIMAL(15,2) UNSIGNED NULL,
       `descatalogado` TINYINT(1) NOT NULL,
       `dimensiones` VARCHAR(15) NOT NULL,
       `descripcion` TEXT NULL,
       `cantidad_stock` SMALLINT UNSIGNED NOT NULL,
       `precionVenta` DECIMAL(15,2) UNSIGNED NOT NULL,
       `precioProveedor` DECIMAL(15,2) UNSIGNED NOT NULL,
       `gama_idgama` int(11) UNSIGNED NOT NULL,
       `proveedor_nieproveedor` BIGINT UNSIGNED NOT NULL,
        PRIMARY KEY (`idproducto`),
        INDEX `fk_producto_gama_igamafk_idx` (`gama_idgama` ASC),
        INDEX `fk_producto_proveedor_idproveedor_idx` (`proveedor_nieproveedor` ASC),
        CONSTRAINT `fk_producto_gama_idgama_fk`
        FOREIGN KEY (`gama_idgama`)
        REFERENCES `gama` (`idgama`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
        CONSTRAINT `fk_producto_proveedor_nieproveedor_fk`
        FOREIGN KEY (`proveedor_nieproveedor`)
        REFERENCES `proveedor` (`nieproveedor`)
        ON DELETE CASCADE
        ON UPDATE CASCADE)
        ENGINE = InnoDB
EOD;
       
       #tabla cliente
       $tabla_cliente = <<<EOD
       CREATE TABLE IF NOT EXISTS `cliente` (
       `niecliente` BIGINT UNSIGNED NOT NULL,
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
       PRIMARY KEY (`niecliente`))
       ENGINE = InnoDB
EOD;
      #tabla compra
       $tabla_compra = <<<EOD
       CREATE TABLE IF NOT EXISTS `compra` (
       `idcompra` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
       `idtransaccion` VARCHAR(50) NOT NULL,
       `fechacompra` DATE NOT NULL,
       `ingreso` DECIMAL(15,2) UNSIGNED NOT NULL,
       `cliente_niecliente` BIGINT UNSIGNED NOT NULL,
       PRIMARY KEY (`idcompra`),
       INDEX `compra_cliente_niecliente_fk_idx` (`cliente_niecliente` ASC),
       CONSTRAINT `fk_compra_cliente_niecliente_fk`
       FOREIGN KEY (`cliente_niecliente`)
       REFERENCES `cliente` (`niecliente`)
       ON DELETE CASCADE
       ON UPDATE CASCADE)
       ENGINE = InnoDB
EOD;
       #tabla lineacompra
       $tabla_lineaCompra = <<<EOD
       CREATE TABLE IF NOT EXISTS `lineacompra` (
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
       REFERENCES `compra` (`idcompra`)
       ON DELETE CASCADE
       ON UPDATE CASCADE,
       CONSTRAINT `fk_lineacompra_producto_idproducto_fk`
       FOREIGN KEY (`producto_idproducto`)
       REFERENCES `producto` (`idproducto`)
       ON DELETE CASCADE
       ON UPDATE CASCADE)
       ENGINE = InnoDB
EOD;
       
       #tabla empleado
       $tabla_empleado = <<<EOD
       CREATE TABLE IF NOT EXISTS `empleado` (
       `nieempleado` BIGINT UNSIGNED NOT NULL,
       `nombre` VARCHAR(45) NOT NULL,
       `apellido1` VARCHAR(50) NOT NULL,
       `apellido2` VARCHAR(50) NOT NULL,
       `email` VARCHAR(100) NOT NULL,
       `telefono` VARCHAR(10) NULL,
       `puesto` VARCHAR(50) NULL,
       `usuario_idusuario` INT UNSIGNED,
        PRIMARY KEY (`nieempleado`),
        INDEX `fk_empleado_usuario1_idx` (`usuario_idusuario` ASC),
        CONSTRAINT `fk_empleado_usuario_idusuario_fk`
        FOREIGN KEY (`usuario_idusuario`)
        REFERENCES `usuario` (`idusuario`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
        )
        ENGINE = InnoDB        
EOD;
       #tabla transportista
       $tabla_transportista = <<<EOD
       CREATE TABLE IF NOT EXISTS `transportista` (
       `compañia` VARCHAR(50) NOT NULL,
       `nombrecontacto` VARCHAR(50) NOT NULL,
       `direccion` VARCHAR(150) NOT NULL,
       `localidad` VARCHAR(50) NULL,
       `estado` VARCHAR(10) NULL,
       `codigopostal` VARCHAR(10) NULL,
        PRIMARY KEY (`compañia`))
        ENGINE = InnoDB
EOD;

        #tabla pedido
        $tabla_pedido = <<<EOD
        CREATE TABLE IF NOT EXISTS `pedido` (
        `idpedido` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `fechapedido` DATE NOT NULL,
        `fechasuministro` DATE NOT NULL,
        `fechaentrega` DATE NOT NULL,
        `estado` VARCHAR(15) NOT NULL,
        `comentarios` TEXT NULL,
        `proveedor_nieproveedor` BIGINT UNSIGNED NOT NULL,
        `empleado_nieempleado` BIGINT UNSIGNED NOT NULL,
        `transportista_compañia` VARCHAR(50) NOT NULL,
        PRIMARY KEY (`idpedido`),
        INDEX `pedido_proveedor_nieproveedor_fk_idx` (`proveedor_nieproveedor` ASC),
        INDEX `pedido_empleado_nieempleado_fk_idx` (`empleado_nieempleado` ASC),
        INDEX `pedido_transportista_compañia_fk_idx` (`transportista_compañia` ASC),
        CONSTRAINT `pedido_proveedor_nieproveedor_fk`
        FOREIGN KEY (`proveedor_nieproveedor`)
        REFERENCES `proveedor` (`nieproveedor`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
        CONSTRAINT `pedido_empleado_nifempleado_fk`
        FOREIGN KEY (`empleado_nieempleado`)
        REFERENCES `empleado` (`nieempleado`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
        CONSTRAINT `pedido_transportista_compañia_fk`
        FOREIGN KEY (`transportista_compañia`)
        REFERENCES `transportista` (`compañia`)
        ON DELETE CASCADE
        ON UPDATE CASCADE)
        ENGINE = InnoDB
EOD;
        
        #tabla linea pedido
        $tabla_lineapedido = <<<EOD
        CREATE TABLE IF NOT EXISTS `lineapedido` (
        `pedido_idpedido` INT(11) UNSIGNED NOT NULL,
        `producto_idproducto` INT(11) UNSIGNED NOT NULL,
        `cantidad` INT(11) UNSIGNED NOT NULL,
        `preciounidad` DECIMAL(15,2) UNSIGNED NOT NULL,
        INDEX `lineapedido_pedido_idpedido_fk_idx` (`pedido_idpedido` ASC),
        INDEX `lineapedido_producto_idproducto_fk_idx` (`producto_idproducto` ASC),
        CONSTRAINT `lineapedido_pedido_idpedido_fk`
        FOREIGN KEY (`pedido_idpedido`)
        REFERENCES `pedido` (`idpedido`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
        CONSTRAINT `fk_lineapedido_producto_idproducto_fk`
        FOREIGN KEY (`producto_idproducto`)
        REFERENCES `producto` (`idproducto`)
        ON DELETE CASCADE
        ON UPDATE CASCADE)
        ENGINE = InnoDB
EOD;
//Relaciones ISA generalización tabla productos        
        #tabla plantas
        $tabla_plantas = <<<EOD
        CREATE TABLE IF NOT EXISTS `plantas`(
        `idproducto` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `exposicion_solar` varchar(45),
        `riego` varchar(45),
        `floracion` varchar(45),
        `temperatura` varchar(40),
        `cuidados` text not null,
        PRIMARY KEY(`idproducto`))
        ENGINE = InnoDB        
                
EOD;
        
        #tabla semillas
        $tabla_semillas = <<<EOD
        CREATE TABLE IF NOT EXISTS `semillas`(
        `idproducto` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `genero` varchar(50) NOT NULL,
        `marcoplantacion` varchar(30) not NULL,
        `recoleccion` VARCHAR(70),
        `profundidad_siembra` numeric(15,2),
        `observaciones` text,
        `laboreo` varchar(150),
        `propiedadesgastronomicas` text,
        PRIMARY KEY(`idproducto`))
        ENGINE = InnoDB
        
                
EOD;
        
        #tabla fitosanitarios
        $tabla_fitosanitarios = <<<EOD
        CREATE TABLE IF NOT EXISTS `fitosanitarios`(
        `idproducto` INT(11) UNSIGNED NOT NULL,
        `tipofuncion` varchar(150) NOT NULL,
        `tipoenvase` varchar(30) NOT NULL,
        `composicion` VARCHAR(150) NOT NULL,
        `ambitoutilizacion` varchar(200),
        PRIMARY KEY(`idproducto`))
        ENGINE = InnoDB
EOD;
       
       
        global $user, $user_pass;
        try
        {
            $dbh = new PDO('mysql:host='. $server .";dbname=". $db_name .";charset=utf8",$user,$user_pass);
            $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
            $tablaSQL = $dbh->prepare($tabla_usuario);
            $tablaSQL->execute();
            $tablaSQL = $dbh->prepare($tabla_gama);
            $tablaSQL->execute();
            $tablaSQL = $dbh->prepare($tabla_proveedor);
            $tablaSQL->execute();
            $tablaSQL = $dbh->prepare($tabla_producto);
            $tablaSQL->execute();
            $tablaSQL = $dbh->prepare($tabla_cliente);
            $tablaSQL->execute();
            $tablaSQL = $dbh->prepare($tabla_compra);
            $tablaSQL->execute();
            $tablaSQL = $dbh->prepare($tabla_lineaCompra);
            $tablaSQL->execute();
            $tablaSQL = $dbh->prepare($tabla_empleado);
            $tablaSQL->execute();
            $tablaSQL = $dbh->prepare($tabla_transportista);
            $tablaSQL->execute();
            $tablaSQL = $dbh->prepare($tabla_pedido);
            $tablaSQL->execute();
            $tablaSQL = $dbh->prepare($tabla_lineapedido);
            $tablaSQL->execute();
            $tablaSQL = $dbh->prepare($tabla_plantas);
            $tablaSQL->execute();
            $tablaSQL = $dbh->prepare($tabla_semillas);
            $tablaSQL->execute();
            $tablaSQL = $dbh->prepare($tabla_fitosanitarios);
            $tablaSQL->execute();
            $sw = true;
        }catch (PDOException $e) 
        {
            echo "<div class='alert alert-danger'>".$e->getMessage()."</div>";
        }
    return $sw;       
    }
    
    //Quitando espacios en blanco, convirtiendo caracteres especiales a html de los campos del formulario
    function test_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }