<?php
include('../conf/PoolConf.php');
include('../lib/Pool/Client.php');
include('../lib/Pool/Server.php');
include('../lib/Pool/Exception.php');
include('../lib/Pool/Server/Connection.php');
include('../lib/Pool/Server/Stmt.php');

use Pool\Client;
$conn = Client::singleton()->getConnection(PoolConf::PG1);

$conn->beginTransaction();
$data = $conn->prepare("SELECT * FROM TEST.TBL1 WHERE SELECCION=:S")->execute(array('S' => 1))->fetchall();
$conn->rollback();

echo "<pre>";
print_r(Client::singleton()->info(PoolConf::PG1));
echo "</pre>";
