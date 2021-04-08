<?php
//phpinfo()

$json = array();
require_once('WBCONEXION.php');
if(isset($_GET["id"])){
$id = $_GET['id'];

$query = "SELECT a.apellido_nombre, c.Descripcion, d.monto_moneda, f.MONTO, f.FECHA_VENCIMIENTO_1, f.ID_CONEXION,
f.FACTURA_NRO
FROM FACTURAS f 
INNER JOIN abonados a on f.ID_ABONADO = a.id_abonado 
INNER JOIN FACTURAS_DETALLE d ON f.ID_FACTURA = d.facturas_id 
INNER JOIN FACTURAS_CONCEPTOS c ON d.cod_concepto = c.cod_concepto
WHERE f.ID_CONEXION = '{$id}'
AND f.ID_FACTURA = (SELECT TOP 1 ID_FACTURA FROM FACTURAS FAC INNER JOIN abonados ABO ON FAC.ID_ABONADO = ABO.id_abonado WHERE ABO.documento_numero = '25425698'ORDER BY ID_FACTURA DESC)
AND f.doc_tipo = 'CPTPG' AND f.FACTURA_ESTADO <> 'FAC90' AND f.FACTURA_ESTADO <> 'FAC30'
ORDER BY ID_FACTURA DESC;";

$stmt = $dbh->prepare($query);
$stmt->execute();
while ($return = $stmt->fetch()) {
 
    $Aux["apellido_nombre"] = $return['apellido_nombre'];
    $Aux["Descripcion"] = $return['Descripcion'];
    $Aux["monto_moneda"] = $return['monto_moneda'];
    $Aux["MONTO"] = $return['MONTO'];
    $Aux["FECHA_VENCIMIENTO_1"] = $return['FECHA_VENCIMIENTO_1'];
    $Aux["domicilio"] = $return['domicilio'];
    $Aux["ID_CONEXION"] = $return['ID_CONEXION'];
    $Aux["FACTURA_NRO"] = $return['FACTURA_NRO'];
    $json [] =  $Aux;
}
if(empty($json)){
    $Aux["FACTURA_NRO"] = "Sinfactura";
    $json [] =  $Aux;
    echo json_encode($json);
}else{
    echo json_encode($json);
}
$stmt = null;
$dbh = null;

}else{
    $Aux["FACTURA_NRO"] = "Sinfactura";
    $json [] =  $Aux;

echo json_encode($json);
}



?>
