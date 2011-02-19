<?php
namespace Pool\Server;

class Stmt
{
	private $_smtpId = null;
	private $_cid    = null;
    private $_client = null;

    function __construct($stmtId, $cid, $client)
    {
        $this->_stmt = $stmtId;
        $this->_cid = $cid;
        $this->_client = $client;
    }

    public function execute($parameters=array())
    {
        $out = $this->_client->do('execute', serialize(array(
            'parameters' => $parameters,
            'stmt'       => $this->_stmt,
            )));
        $error = unserialize($out);
        if (is_a($error, '\Pool\Exception')) {
            throw $error;
        }
        $this->_stmt = $out;
        return $this;
    }

    public function fetchAll()
    {
        $data = $this->_client->do('fetchAll', serialize(array(
            'stmt' => $this->_stmt,
            )));
        return unserialize($data);
    }

}
