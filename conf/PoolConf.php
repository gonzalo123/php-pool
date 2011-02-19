<?php
class PoolConf
{
    const PG1 = 'PG1';
    static $DB = array(
        self::PG1 => array(
            'dsn'      => "pgsql:dbname=gonzalo;host=localhost",
            'username' => 'user',
            'password' => 'password',
            'options'  => null),
    );

    static $SERVERS = array(
        array('127.0.0.1', 4730),
        array('127.0.0.1', 4731),
    );
}