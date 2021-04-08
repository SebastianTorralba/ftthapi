<?php
//phpinfo()

$json = array();
require_once('WBCONEXION.php');
if(isset($_GET["dni"])){
$dni = $_GET['dni'];

$query = "SELECT c.id_conexion, (b.nom_barrio+', '+ ca.nom_calle+' '+i.dom_numero+', '+l.cloca) as domicilio
FROM Conexiones c 
	INNER JOIN inmuebles i ON c.id_inmueble  = i.id_inmueble 
	INNER JOIN localidades l ON i.id_localidad = l.ccodloca
	INNER JOIN Calles ca ON ca.cod_calle = i.cod_calle 
	INNER JOIN barrios b ON b.cod_barrio = i.cod_barrio
	INNER JOIN abonados a ON C.id_abonado = a.id_abonado
	INNER JOIN tipo_estado_servicio ts ON ts.id_estado_servicio = i.Estado_Servicio
WHERE a.documento_numero = '{$dni}'
AND ts.facturable = 1;";

$stmt = $dbh->prepare($query);
$stmt->execute();
while ($return = $stmt->fetch()) {
 
    $Aux["id_conexion"] = $return['id_conexion'];
    $Aux["domicilio"] = $return['domicilio'];
    
    $json [] =  $Aux;
}
if(empty($json)){
    $Aux["id_conexion"] = "SinConexion";
    $json [] =  $Aux;
    echo json_encode($json);
}else{
    echo json_encode($json);
}
$stmt = null;
$dbh = null;

}else{
    $Aux["id_conexion"] = "Sin Conexiones";
    $json [] =  $Aux;

echo json_encode($json);
}



?>
