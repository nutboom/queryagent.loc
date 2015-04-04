<?php

return array(
        'db' => array(
            'tablePrefix' => 'tbl_',
            'connectionString' => 'mysql:host=localhost;dbname=cavegroup_qa',
            'emulatePrepare' => true,
            'username' => 'cavegroup_qa',
            'password' => 'kiqxPZ9C',
            'charset' => 'utf8',
        ),
        'memcache_servers' => array(
                array('host'=>'127.0.0.1', 'port'=>11211, 'weight'=>60), //РІР°С€Рё РЅР°СЃС‚СЂРѕР№РєРё memcached
        ),
        'apns_type' => 'production', // sandbox, production
		'bootstrap' =>
            preg_match('/\/page\/.*/i', $_SERVER['REQUEST_URI']) ?
                        array('yiiCss'=>false,'coreCss'=>false,'enableJS'=>false,) :
                        array(),
);
