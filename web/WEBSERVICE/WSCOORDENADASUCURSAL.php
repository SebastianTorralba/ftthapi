<?php
//phpinfo()

$json = array();
require_once('WBCONEXION.php');
if(isset($_GET["sucId"])){
$sucId = $_GET['sucId'];

$query = "SELECT s.latitud, s.longitud
FROM Sucursales s
WHERE s.cod_sucursal ='{$sucId}';";

$stmt = $dbh->prepare($query);
$stmt->execute();
while ($return = $stmt->fetch()) {
 
    $Aux["latitud"] = $return['latitud'];
    $Aux["longitud"] = $return['longitud'];
    
    $json [] =  $Aux;
}
echo json_encode($json);
$stmt = null;
$dbh = null;

}else{
    $Aux["latitud"] = "-29.412685";
    $Aux["longitud"] = "-66.855974";
    
    $json [] =  $Aux;

echo json_encode($json);
}



?>
