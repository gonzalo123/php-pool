<?php
namespace Pool\Server;

class Connection
{
    private $_cid = null;
    private $_client = null;
    private $_key = null;

    function __construct($cid, $client, $key)
    {
        $this->_cid    = $cid;
        $this->_client = $client;
        $this->_key    = $key;
    }

    public function prepare($sql)
    {
        $stmtId = $this->_client->do('prepare', serialize(array(
            'sql' => $sql,
            'cid' => $this->_cid,
            'key' => $this->_key,
            )));
        return new Stmt($stmtId, $this->_cid, $this->_client);
    }

    public function beginTransaction()
    {
        $this->_client->do('beginTransaction', serialize(array(
            'cid' => $this->_cid,
            'key' => $this->_key,
            )));
    }

    public function commit()
    {
        $this->_client->do('commit', serialize(array(
            'cid' => $this->_cid,
            'key' => $this->_key,
            )));
    }

    public function rollback()
    {
        $this->_client->do('rollback', serialize(array(
            'cid' => $this->_cid,
            'key' => $this->_key,
            )));
    }

    public function release()
    {
        $this->_client->do('release', serialize(array(
            'key' => $this->_key,
            'cid' => $this->_cid,
            )));
    }
}