        <?php
                
         $CFG = array(
        'host'=> 'localhost',
        'database' => 'viversoft',
        'user' => 'ut3_user',
        'password' => 'ut3_pass'
       );
       ORM::configure(array(
       'connection_string' => 'mysql:host='.$CFG['host'].';dbname='.$CFG['database'],
       'username' => $CFG['user'],
       'password' => $CFG['password'],
       'driver_options' => array(PDO::MYSQL_ATTR_INIT_COMMAND=>'set NAMES utf8')));
       ORM::configure('id_column_overrides', array(
       'empleado' => 'nieempleado',
       'proveedor' => 'nieproveedor'
));