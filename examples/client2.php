<?php
include('../conf/PoolConf.php');
include('../lib/Pool/Client.php');
include('../lib/Pool/Server.php');
include('../lib/Pool/Exception.php');
include('../lib/Pool/Server/Connection.php');
include('../lib/Pool/Server/Stmt.php');

use Pool\Client;
$conn = Client::singleton()->getConnection(PoolConf::PG1);

$data = $conn->prepare("SELECT * FROM TEST.TBL1 WHERE FIELD1=:S")->execute(array('S' => 1))->fetchall();

echo count($data);

echo "<pre>";
print_r(Client::singleton()->info(PoolConf::PG1));
echo "</pre>";



/*
$conn->execute(array('NAME' => 'gonzalo'));
$conn->commit();
*/
