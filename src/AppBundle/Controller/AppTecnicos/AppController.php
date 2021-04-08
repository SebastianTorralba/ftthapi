<?php

namespace AppBundle\Controller\AppTecnicos;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/tecnicos")
 */
class AppController extends Controller
{
    /**
     * @Route("/app-asdfghjkl-app-tecnicos-lsosisu-123/get-ods-from-lote", name="appTecnicos_getOdsFromLote")
     */
    public function getOdsFromLoteAction(Request $request)
    {
        $loteId = 0;
        $data = array();

        if($request->query->has('loteId')) {
            $loteId = $request->query->get('loteId');
        }

        $query= "select
                    ld.id_orden_servicio as 'ods&Orden de Servicio',
                    ih.fecha_reclamo as 'fechareclamo&Fecha Reclamo',
                    cu.descripcion as 'cuadrilla&Cuadrilla',
                    ih.fecha_visita as 'fechavisita&Fecha Visita',
                    ih.id_incidencia as 'id_incidencia&Incidencia N°',
                    tsi.Descripcion as 'servicio&Servicios',
                    ld.id_conexion as 'id_conexion&ID Conexión',
                    tar.descripcion as 'tarifa&Tarifa',
                    loc.cloca as 'localidad&Localidad',
                    ab.apellido_nombre as 'abonado&Abonado',
                    bar.nom_barrio as 'barrio&Barrio',
                    ab.celular as 'tel&Telefonos',
                    cal.nom_calle as 'calle&Calle',
                    vcd.dom_manzana as 'mzna&Manzana',
                    vcd.dom_numero as 'nro&Número',
                    vcd.dom_piso as 'piso&Piso',
                    vcd.dom_dpto as 'dpto&Depto',
                    cro.referencia as 'ref&Referencia',
                    inm.latitud as 'lat&Latitud',
                    inm.longitud as 'long&Longitud',
                    tsi.tipo_incidencia as 'tipo_incidencia&Tipo'
                from lotes_detalle ld
                inner join Ordenes_Servicios os on ld.id_orden_servicio = os.id_Orden_Servicio
                inner join incidencias_header ih on ih.id_incidencia = os.id_incidencia
                inner join tipo_subtipo_incidencias tsi on tsi.subtipo_inicidencia = ih.subtipo_incidencia
                inner join abonados ab on ab.id_abonado = ld.id_abonado
                inner join v_con_dom vcd on vcd.id_conexion = ld.id_conexion
                inner join localidades loc on loc.ccodloca = vcd.id_localidad
                left join barrios bar on vcd.cod_barrio = bar.cod_barrio
                left join calles cal on vcd.cod_calle = cal.cod_calle
                inner join tarifas tar on tar.id_tarifa = vcd.tarifa
                inner join croquis cro on cro.id_conexion = ld.id_conexion
                inner join inmuebles inm on inm.id_inmueble = vcd.id_inmueble
                inner join cuadrillas cu on cu.id_cuadrilla = os.id_cuadrilla
                where ld.id_lote = :idLote
                ORDER BY fecha_reclamo desc
        ";

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindValue('idLote', $loteId);
        $statement->execute();
        $results = $statement->fetchAll();

        $i = 0;
        foreach($results as $result) {

            $data[$i]["operacion"] = "ods_alta";

            foreach($result as $key => $value) {

                $arrayKey =  explode("&", $key);

                $data[$i]["datos"][$arrayKey[0]]["label"] = $arrayKey[1] ;
                $data[$i]["datos"][$arrayKey[0]]["valor"] = $value == null ? "" : utf8_encode(trim($value));
            }

            $i++;
        }

        return new JsonResponse($data);
        exit();
    }

    /**
     * @Route("/app-asdfghjkl-app-tecnicos-lsosisu-123/get-ods-from-lote-reclamo", name="appTecnicos_getOdsFromLoteReclamo")
     */
    public function getOdsFromLoteReclamoAction(Request $request)
    {
        $loteId = 0;
        $codSucursal = 0;
        $data = array();

        if($request->query->has('loteId') && $request->query->has('codSucursal')) {
            $loteId = $request->query->get('loteId');
            $codSucursal = $request->query->get('codSucursal');
        }

        $query= "select
            os.id_orden_servicio as 'ods&Orden de Servicio',
            ih.fecha_reclamo as 'fechareclamo&Fecha Reclamo',
            cu.descripcion as 'cuadrilla&Cuadrilla',
            ih.fecha_visita as 'fechavisita&Fecha Visita',
            ih.id_incidencia as 'id_incidencia&Incidencia N°',
            tsi.Descripcion as 'servicio&Servicios',
            ih.id_conexion as 'id_conexion&ID Conexión',
            tar.descripcion as 'tarifa&Tarifa',
            ab.apellido_nombre as 'abonado&Abonado',
            loc.cloca as 'localidad&Localidad',
            bar.nom_barrio as 'barrio&Barrio',
            ab.celular as 'tel&Telefonos',
            cal.nom_calle as 'calle&Calle',
            vcd.dom_manzana as 'mzna&Manzana',
            vcd.dom_numero as 'nro&Número',
            vcd.dom_piso as 'piso&Piso',
            vcd.dom_dpto as 'dpto&Depto',
            cro.referencia as 'ref&Referencia',
            pr.problema_descripcion as 'problema&Problema',
            ihd.problema_detalle as 'problema_detalle&Problema Detalle',
            inm.latitud as 'lat&Latitud',
            inm.longitud as 'long&Longitud',
            tsi.tipo_incidencia as 'tipo_incidencia&Tipo'
        from lotes_ods lo
        inner join lotes_ods_detalle lod on lo.id_lote_ods = lod.id_lote_ods and lo.cod_sucursal = lod.cod_sucursal
        inner join ordenes_servicios os on lod.id_ods =  os.id_orden_servicio
        inner join incidencias_header ih on ih.id_incidencia = os.id_incidencia
        inner join incidencias_detalle ihd on ihd.id_incidencia = ih.id_incidencia
        inner join tipo_subtipo_incidencias tsi on tsi.subtipo_inicidencia = ih.subtipo_incidencia
        inner join abonados ab on ab.id_abonado = ih.id_abonado
        inner join v_con_dom vcd on vcd.id_conexion = ih.id_conexion
        inner join localidades loc on loc.ccodloca = vcd.id_localidad
        left join barrios bar on vcd.cod_barrio = bar.cod_barrio
        left join calles cal on vcd.cod_calle = cal.cod_calle
        inner join tarifas tar on tar.id_tarifa = vcd.tarifa
        inner join croquis cro on cro.id_conexion = ih.id_conexion
        inner join inmuebles inm on inm.id_inmueble = vcd.id_inmueble
        inner join cuadrillas cu on cu.id_cuadrilla = os.id_cuadrilla
        inner join problemas pr on pr.id_problema =ihd.id_problema
        where
            lo.cod_sucursal = :codSucursal
            and  lo.id_lote_ods = :loteId
            and tsi.tipo_incidencia = 2
        order by os.id_orden_servicio asc";

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindValue('loteId', $loteId);
        $statement->bindValue('codSucursal', $codSucursal);
        $statement->execute();
        $results = $statement->fetchAll();

        $i = 0;
        foreach($results as $result) {

            $data[$i]["operacion"] = "ods_alta_reclamo";

            foreach($result as $key => $value) {

                $arrayKey =  explode("&", $key);

                $data[$i]["datos"][$arrayKey[0]]["label"] = $arrayKey[1] ;
                $data[$i]["datos"][$arrayKey[0]]["valor"] = $value == null ? "" : utf8_encode(trim($value));
            }

            $i++;
        }

        return new JsonResponse($data);
        exit();
    }

    /**
     * @Route("/app-asdfghjkl-app-tecnicos-lsosisu-123/get-ods-from-ods", name="appTecnicos_getOdsFromOds")
     */
    public function getOdsFromOdsAction(Request $request)
    {
        $odsId = 0;
        $data = array();

        if($request->query->has('odsId')) {
            $odsId = $request->query->get('odsId');
        }

        $query= "select
                    ld.id_orden_servicio as 'ods&Orden de Servicio',
                    ih.fecha_reclamo as 'fechareclamo&Fecha Reclamo',
                    cu.descripcion as 'cuadrilla&Cuadrilla',
                    ih.fecha_visita as 'fechavisita&Fecha Visita',
                    ih.id_incidencia as 'id_incidencia&Incidencia N°',
                    tsi.Descripcion as 'servicio&Servicios',
                    ld.id_conexion as 'id_conexion&ID Conexión',
                    tar.descripcion as 'tarifa&Tarifa',
                    loc.cloca as 'localidad&Localidad',
                    ab.apellido_nombre as 'abonado&Abonado',
                    bar.nom_barrio as 'barrio&Barrio',
                    ab.celular as 'tel&Telefonos',
                    cal.nom_calle as 'calle&Calle',
                    vcd.dom_manzana as 'mzna&Manzana',
                    vcd.dom_numero as 'nro&Número',
                    vcd.dom_piso as 'piso&Piso',
                    vcd.dom_dpto as 'dpto&Depto',
                    cro.referencia as 'ref&Referencia',
                    inm.latitud as 'lat&Latitud',
                    inm.longitud as 'long&Longitud',
                    tsi.tipo_incidencia as 'tipo_incidencia&Tipo'
                from lotes_detalle ld
                inner join Ordenes_Servicios os on ld.id_orden_servicio = os.id_Orden_Servicio
                inner join incidencias_header ih on ih.id_incidencia = os.id_incidencia
                inner join tipo_subtipo_incidencias tsi on tsi.subtipo_inicidencia = ih.subtipo_incidencia
                inner join abonados ab on ab.id_abonado = ld.id_abonado
                inner join v_con_dom vcd on vcd.id_conexion = ld.id_conexion
                inner join localidades loc on loc.ccodloca = vcd.id_localidad
                left join barrios bar on vcd.cod_barrio = bar.cod_barrio
                left join calles cal on vcd.cod_calle = cal.cod_calle
                inner join tarifas tar on tar.id_tarifa = vcd.tarifa
                inner join croquis cro on cro.id_conexion = ld.id_conexion
                inner join inmuebles inm on inm.id_inmueble = vcd.id_inmueble
                inner join cuadrillas cu on cu.id_cuadrilla = os.id_cuadrilla
                where ld.id_orden_servicio = :odsId
        ";

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindValue('odsId', $odsId);
        $statement->execute();
        $results = $statement->fetchAll();

        $i = 0;
        foreach($results as $result) {

            $data[$i]["operacion"] = "ods_alta";

            foreach($result as $key => $value) {

                $arrayKey =  explode("&", $key);

                $data[$i]["datos"][$arrayKey[0]]["label"] = $arrayKey[1] ;
                $data[$i]["datos"][$arrayKey[0]]["valor"] = $value == null ? "" : utf8_encode(trim($value));
            }

            $i++;
        }

        return new JsonResponse($data);
        exit();
    }

    /**
     * @Route("/app-asdfghjkl-app-tecnicos-lsosisu-123/get-ods-from-ods-reclamo", name="appTecnicos_getOdsFromOdsReclamo")
     */
    public function getOdsFromOdsReclamoAction(Request $request)
    {
        $odsId = 0;
        $data = array();

        if($request->query->has('odsId')) {
            $odsId = $request->query->get('odsId');
        }

        $query= "select
            os.id_orden_servicio as 'ods&Orden de Servicio',
            ih.fecha_reclamo as 'fechareclamo&Fecha Reclamo',
            cu.descripcion as 'cuadrilla&Cuadrilla',
            ih.fecha_visita as 'fechavisita&Fecha Visita',
            ih.id_incidencia as 'id_incidencia&Incidencia N°',
            tsi.Descripcion as 'servicio&Servicios',
            ih.id_conexion as 'id_conexion&ID Conexión',
            tar.descripcion as 'tarifa&Tarifa',
            loc.cloca as 'localidad&Localidad',
            a.apellido_nombre as 'abonado&Abonado',
            bar.nom_barrio as 'barrio&Barrio',
            a.celular as 'tel&Telefonos',
            cal.nom_calle as 'calle&Calle',
            vcd.dom_manzana as 'mzna&Manzana',
            vcd.dom_numero as 'nro&Número',
            vcd.dom_piso as 'piso&Piso',
            vcd.dom_dpto as 'dpto&Depto',
            cro.referencia as 'ref&Referencia',
            inm.latitud as 'lat&Latitud',
            inm.longitud as 'long&Longitud',
            tsi.tipo_incidencia as 'tipo_incidencia&Tipo'
            from incidencias_header ih
            inner join Ordenes_Servicios os on ih.id_incidencia = os.id_incidencia
            inner join abonados a on a.id_abonado = ih.id_abonado
            inner join tipo_subtipo_incidencias tsi on tsi.subtipo_inicidencia = ih.subtipo_incidencia
            inner join v_con_dom vcd on vcd.id_conexion = ih.id_conexion
            inner join localidades loc on loc.ccodloca = vcd.id_localidad
            left join barrios bar on vcd.cod_barrio = bar.cod_barrio
            left join calles cal on vcd.cod_calle = cal.cod_calle
            inner join tarifas tar on tar.id_tarifa = vcd.tarifa
            inner join croquis cro on cro.id_conexion = ih.id_conexion
            inner join inmuebles inm on inm.id_inmueble = vcd.id_inmueble
            inner join cuadrillas cu on cu.id_cuadrilla = os.id_cuadrilla
            where tsi.tipo_incidencia = 2 and os.id_orden_servicio = :odsId
        ";

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindValue('odsId', $odsId);
        $statement->execute();
        $results = $statement->fetchAll();

        $i = 0;
        foreach($results as $result) {

            $data[$i]["operacion"] = "ods_alta_reclamo";

            foreach($result as $key => $value) {

                $arrayKey =  explode("&", $key);

                $data[$i]["datos"][$arrayKey[0]]["label"] = $arrayKey[1] ;
                $data[$i]["datos"][$arrayKey[0]]["valor"] = $value == null ? "" : utf8_encode(trim($value));
            }

            $i++;
        }

        return new JsonResponse($data);
        exit();
    }

    /**
    * @Route("/descargar", name="descargar_app")
    */
    public function descargarPlataformaAction(Request $request)
    {
//        $baseurl = $request->getBasePath();
//        $path = $this->get('kernel')->getRootDir(). "/../web/app/tecnicos/ipt-tecnicos.apk";
//
//        if (!file_exists($path)) {
//          throw $this->createNotFoundException();
//        }
//
//        // Generate response
//        $response = new Response();
//
//        // Set headers
//        $response->headers->set('Cache-Control', 'private');
//        $response->headers->set('Content-type', 'application/vnd.android.package-archive');
//        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($path) . '";');
//        $response->headers->set('Content-length', filesize($path));
//
//        // Send headers before outputting anything
//        $response->sendHeaders();
//        $response->setContent(readfile($path));
//
        return $this->redirect("https://drive.google.com/open?id=1bSnZWPbL-k5J6vewpeE8JMV45HIlp51H");
    }

    /**
     * @Route("/app-asd.-sdfghjkl-app-tecnicos-lsosddd-isu-123/login", name="appTecnicos_login")
     */
    public function loginAction(Request $request)
    {
        $data = array();
        $usuario = '';
        $clave   = '';

        $data[0] = array("resultado" => 0, "usuarioNombre" => "");

        if($request->query->has('usuario')==false || $request->query->has('clave')==false) {
            return new JsonResponse($data);
        }

        $usuario = utf8_decode($request->query->get('usuario'));
        $clave   = utf8_decode($request->query->get('clave'));


        // encriptación de clave del sistema de ipt
        $lsAbc = str_split(utf8_decode('ABCDEFGHIJKLMNÑOPQRSTUVWXYZabcdefghijklmnñopqrstuvwxyz1234567890!"·$%&/()=?¿*'));
        $lsKey = str_split(utf8_decode('hgFGJKLÑedcBNM0ñnmlkRTYU65yxw)(/&%4321zvutsrZXCV987qpojifba*¿?=$·"!QWEIOPASDH'));
        $claveIngresada = str_split($clave);
        $claveEncriptada = "";
        $indice = 0;

        for($i = 0; $i < count($claveIngresada); $i++) {
            $indice = array_search($claveIngresada[$i], $lsAbc);
            $claveEncriptada .= $lsKey[$indice];
        }

        $query= "select
            id_usuario,
            desc_usr
            from Usuarios
            where nom_usr = :usuario and password2016 = :clave
        ";

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindValue('usuario', $usuario);
        $statement->bindValue('clave', $claveEncriptada);
        $statement->execute();
        $results = $statement->fetchAll();

        if (count($results)>0) {
            $data[0] = array("resultado" => 1, "usuarioNombre" => utf8_encode($results[0]['desc_usr']), "usuarioId" => $results[0]['id_usuario']);
        }

        return new JsonResponse($data);
        exit();
    }

    /**
     * @Route("/app-asd.-sdfghjkl-app-tecnicos-lsosddd-isu-123/version-actual", name="appTecnicos_version_actual")
     */
    public function versionActualAction(Request $request)
    {
        return new JsonResponse(array(0=>array("versionNumero" => 1)));
    }

    /**
     * @Route("/app-asd.-sdfghjkl-app-tecnicos-lsosddd-isu-123/actualizar-conexion-coordenadas", name="appTecnicos_actualizar_conexion_coordenadas")
     */
    public function actualizarConexionCoordenadasAction(Request $request)
    {
        $data = array();
        $data[0] = array("resultado" => 0);

        $f = fopen("log/log_apptecnicos_modificar_coordenadas.txt", "a");
        fwrite($f, "\n\n\n abierto".date('d-m-Y H:i:s'));
        fwrite($f, ",sss:".$request->query->get('id_usuario'));
        fwrite($f, ",sss:".$request->query->get('id_conexion'));
        fwrite($f, ",sss:".$request->query->get('latitud'));
        fwrite($f, ",sss:".$request->query->get('longitud'));
        fwrite($f, ",sss:".$request->query->get('direccion'));

        if($request->query->has('id_usuario')==false || $request->query->has('id_conexion')==false
                || $request->query->has('latitud')==false || $request->query->has('longitud')==false || $request->query->has('direccion')==false) {

            fwrite($f, "error params: ");

            return new JsonResponse($data);
        }

        $id_usuario  = $request->query->get('id_usuario');
        $id_conexion = $request->query->get('id_conexion');
        $latitud     = $request->query->get('latitud');
        $longitud    = $request->query->get('longitud');
        $direccion   = utf8_decode($request->query->get('direccion'));

        $query= "update inmuebles set latitud='".$latitud."', longitud='".$longitud."', direccion_google='".$direccion."', id_usuario=".$id_usuario.",app_ventana='APP_CEL_TECNICOS' from inmuebles i inner join conexiones c on c.id_inmueble=i.id_inmueble where c.id_conexion=".$id_conexion.";";
        fwrite($f, "\n SQL: ".$query);

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $res = $connection->executeUpdate($query);

        fwrite($f, "\n RESULTADO: ".$res);
        fclose($f);

        $data[0] = array("resultado" => $res);


        return new JsonResponse($data);
        exit();
    }
}
