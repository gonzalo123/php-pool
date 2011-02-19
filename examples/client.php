<?php
include('../conf/PoolConf.php');
include('../lib/Pool/Client.php');
include('../lib/Pool/Server.php');
include('../lib/Pool/Exception.php');
include('../lib/Pool/Server/Connection.php');
include('../lib/Pool/Server/Stmt.php');

use Pool\Client;
$conn = Client::singleton()->getConnection(PoolConf::PG1);

$sql = "SELECT * FROM TEST.TBL1";
$stmt = $conn->prepare($sql);

$stmt->execute();
$data = $stmt->fetchall();
echo "<p>count: " . count($data) . "</p>";

try {
    $sql = "SELECT * FROM NON_EXISTENT_TABLE WHERE sealeccion=1";
    $stmt = $conn->prepare($sql);

    $stmt->execute();
    $data = $stmt->fetchall();
    echo "<p>count: " . count($data) . "</p>";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}



echo "<pre>";
print_r(Client::singleton()->info(PoolConf::PG1));
echo "</pre>";

/*
$conn->execute(array('NAME' => 'gonzalo'));
$conn->commit();
*/
