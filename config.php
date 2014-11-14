        <?php
                
        $CFG = array(
        'host'=> 'localhost',
        'database' => 'viversoft',
        'user' => 'ut3_user',
        'password' => 'ut3_pass'
       );
                
       ORM::configure('mysql:host='.$CFG['host'].';dbname='.$CFG['database']);
       ORM::configure('username',$CFG['user']);
       ORM::configure('password',$CFG['password']);
       ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));               