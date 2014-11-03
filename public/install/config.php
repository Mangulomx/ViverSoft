        <?php
                
        $CFG = array(
        'host'=> 'localhost',
        'database' => 'vivero',
        'user' => 'root',
        'password' => 'oretania'
       );
                
       ORM::configure('mysql:host='.$CFG['host'].';dbname='.$CFG['database']);
       ORM::configure('username',$CFG[root]);
       ORM::configure('password',$CFG[]);
       ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));               