<?php
namespace Pool;
class Server
{
    private static $usedPool = array();
    private static $pool = array();
    private static $stmts = array();
    private static $number = 10;
    static function init()
    {
        self::resetAll();
        // Create connections
        foreach (array_keys(\PoolConf::$DB) as $key) {
            // Create transaction pools
            for($i=0; $i< self::$number; $i++) {
                self::_newConnection($key);
            }
        }
    }

    private static function resetAll()
    {
        self::$usedPool = array();
        self::$stmts = array();
        self::$pool = array();
    }

    public function beginTransaction($cid, $key)
    {
        $conn = Server::_getConnection($key, $cid);
        $conn->beginTransaction();
    }

    public function commit($cid, $key)
    {
        $conn = Server::_getConnection($key, $cid);
        $conn->commit();
    }

    public function rollback($cid, $key)
    {
        $conn = Server::_getConnection($key, $cid);
        $conn->rollback();
    }

    public static function _newConnection($key)
    {
        $pdoConf = \PoolConf::$DB[$key];
        $conn = new \PDO($pdoConf['dsn'], $pdoConf['username'], $pdoConf['password']);
        $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        self::$pool[$key][] = $conn;
    }

    public static function _getConnection($key, $cid)
    {
        return self::$usedPool[$key][$cid];
    }

    public static function _getStmt($stmtId)
    {
		if (array_key_exists($stmtId, self::$stmts)) {
        	return self::$stmts[$stmtId];
		} else {
			return false;
		}
    }

    public static function _setStmt($stmtId, $stmt)
    {
        self::$stmts[$stmtId] = $stmt;
    }

	public static function prepare($key, $cid, $sql)
	{
		// @TODO Cache stmts
		$conn = Server::_getConnection($key, $cid);

		// @TODO Checks if conn is not null
		if (!is_null($conn)) {
    		$stmtId = md5(serialize(array($key, $sql)));
			$stmt = Server::_getStmt($stmtId);
			if (false === $stmt) {
    			$stmt = $conn->prepare($sql);
			}
    		self::_setStmt($stmtId, $stmt);
		} else {
		    echo "ERROR: cid:{$cid}, sql:{$sql}\n";
		}
		return $stmtId;
	}

	public static function execute($stmtId, $parameters)
	{
	    $stmt = Server::_getStmt($stmtId);
	    try {
            $stmt->execute($parameters);
	    } catch (\PDOException $e) {
	       return serialize(new Exception($e->getMessage(), $e->getCode()));
	    }
        self::_setStmt($stmtId, $stmt);
		return $stmtId;
	}

	public static function fetchAll($stmtId)
	{
	    $stmt = Server::_getStmt($stmtId);
        // @TODO Checks if conn is not null
		if (!is_null($stmt)) {
		  return $stmt->fetchAll();
		} else {
		    echo "ERROR: stmtId:-{$stmtId}-\n";
		}
	}

	public static function info($key)
    {
        return array(
            'usedPool' => count(self::$usedPool[$key]),
            'pool'     => count(self::$pool[$key]),
            'stmts'    => count(self::$stmts)
            );
    }

    public static function release($key, $cid)
	{
        $conn = array_shift(self::$usedPool[$key]);
        self::$pool[$key][] = $conn;
	}

    public static function getConnection($key)
    {
        $conn = array_shift(self::$pool[$key]);
        $cid = uniqid();
        self::$usedPool[$key][$cid] = $conn;

        return $cid;
    }
}
