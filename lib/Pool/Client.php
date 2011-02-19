<?php
namespace Pool;

class Client
{
    private static $_instance = null;
    private static $_conn = array();
    private $_client = null;

    public static function shutdown()
    {
        if (count(self::$_conn) > 0) {
            foreach (self::$_conn as $conn) {
                $conn->release();
            }
        }
    }

    public static function singleton()
    {
        if (is_null(self::$_instance)) {
            register_shutdown_function(array("\Pool\Client", shutdown));
            $client = new \GearmanClient();
            foreach (\PoolConf::$SERVERS as $server) {
                $client->addServer($server[0], $server[1]);
            }
            self::$_instance = new Client($client);
        }
        return self::$_instance;
    }

    protected function __construct($client)
    {
        $this->_client = $client;
    }

    public function getConnection($key)
    {
        $cid = $this->_client->do("getConnection", $key);
        $conn = new Server\Connection($cid, $this->_client, $key);
        self::$_conn[] = $conn;
        return $conn;
    }

    public function info($key)
    {
        return unserialize($this->_client->do("info", $key));
    }
}
