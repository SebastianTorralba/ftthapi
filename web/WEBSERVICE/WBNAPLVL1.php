<?php
//phpinfo()

$json = array();
require_once('WBCONEXION.php');

$query = "SELECT re.id, ret.nombre, re.nombre as nombre2, re.codigo, ree.estado, reg.latitud, reg.longitud
FROM red_elemento re INNER JOIN red_elemento_estado ree on re.estado_actual_id = ree.id 
INNER JOIN red_elemento_georeferencia reg on reg.elemento_id = re.id
INNER JOIN red_elemento_tipo ret on re.elemento_tipo_id = ret.id
WHERE lvl> 0;";

$stmt = $dbh->prepare($query);
$stmt->execute();
while ($return = $stmt->fetch()) {
  //var_dump($return);
  
  $Aux["id"] = $return['id'];
    $Aux["nombre"] = $return['nombre'];
    $Aux["nombre2"] = $return['nombre2'];
    $Aux["codigo"] = $return['codigo'];
    $Aux["estado"] = $return['estado'];
    $Aux["latitud"] = $return['latitud'];
    $Aux["longitud"] = $return['longitud'];
    
    $json [] =  $Aux;
}

/*
$resultado = mssql_query($query,$conexion);
if($resultado){
while($return = mssql_fetch_array($resultado)){
    $Aux["id"] = $return['id'];
    $Aux["nombre"] = $return['nombre'];
    $Aux["nombre2"] = $return['nombre2'];
    $Aux["codigo"] = $return['codigo'];
    $Aux["estado"] = $return['estado'];
    $Aux["latitud"] = $return['latitud'];
    $Aux["longitud"] = $return['longitud'];
    $json [] =  $resultado;

}
mssql_close($con);
}
*/
echo json_encode($json);


?>
