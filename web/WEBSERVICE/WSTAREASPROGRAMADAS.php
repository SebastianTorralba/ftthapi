<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}

require_once('WBCONEXIONMYSQL.php');

$fecha = date("Ymd");

$query = "SELECT edelar_cortes_programados.id_corte_edelar,  
edelar_cortes_programados.localidades,  
edelar_cortes_programados.fecha,  
edelar_cortes_programados.hora_desde,  
edelar_cortes_programados.hora_hasta,  
edelar_cortes_programados.sector,  
edelar_cortes_programados.calles,  
edelar_cortes_programados.motivo,  
edelar_cortes_programados.notificado,  
edelar_cortes_programados.fecha_hora_creado  
FROM edelar_cortes_programados  
WHERE fecha = :fecha
ORDER BY fecha ASC";

$stmt = $dbh->prepare($query);
$stmt->bindParam(':fecha', $fecha);

if($stmt->execute()){

    while ($return = $stmt->fetch()) {
        
        $Aux['id_corte_edelar'] = $return['id_corte_edelar'];
        $Aux['localidades'] = $return['localidades'];
        $Aux['fecha'] = $return['fecha'];
        $Aux['hora_desde'] = $return['hora_desde'];
        $Aux['hora_hasta'] = $return['hora_hasta'];
        $Aux['sector'] = $return['sector'];
        $Aux['calles'] = $return['calles'];
        $Aux['motivo'] = $return['motivo'];
        $Aux['notificado'] = $return['notificado'];
        $Aux['fecha_hora_creado'] = $return['fecha_hora_creado'];
        
        $json [] =  $Aux;
    }

    echo json_encode($json);
    $stmt = null;
    $dbh = null;

}else{
    $json = null;
    echo json_encode($json);
}
?>