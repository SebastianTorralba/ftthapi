<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Conexion;
use  AppBundle\Entity as Entity;
use Doctrine\ORM\Query\ResultSetMapping;

class ConexionRepository extends EntityRepository
{
  public function findForRedTopologiaByParams($params = [])
  {
    $servicios = Entity\Conexion::$servicios;
    $estados = Entity\Inmueble::$estados;

    $qb = $this
            ->createQueryBuilder('c')
            ->select('c')
            ->innerJoin("c.inmueble", "i", 'WITH', 'i.id > 0')
            ->innerJoin("c.tarifasInformacion", "ti")
            ->setFirstResult($params["offset"]*1000)
            ->setMaxResults(1000)
            ->addOrderBy("c.id", "asc")
            ->where('c.id > 0 and i.latitud is not null and i.longitud is not null')
    ;

   $serviciosSeleccionados = [];
   if(isset($params["servicios"]) && $params["servicios"] != '') {

        $serviciosParams = explode(";",$params["servicios"]);

        foreach ($serviciosParams as $key => $value) {
          if (isset($servicios[$value])) {
            $serviciosSeleccionados = array_merge($serviciosSeleccionados, $servicios[$value]);
          }
        }

    } else {

      foreach ($servicios as $key => $value) {
        if ($value) {
          $serviciosSeleccionados = array_merge($serviciosSeleccionados, $value);
        }
      }
    }

    $qb
        ->andWhere('c.idTarifa IN (:tarifaIds) or c.idTarifaTelevision IN (:tarifaIds) or c.idTarifaFtth IN (:tarifaIds) ')
        ->setParameter('tarifaIds', $serviciosSeleccionados, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY);
    ;

    if(isset($params["estados"])  && $params["estados"] != '') {

      $estadosSeleccionados = [];
      $estadosParams=explode(";",$params["estados"]);

      foreach ($estadosParams as $key => $value) {

          if ($value) {

            // si tiene servicios seleccionados
              /*foreach($serviciosSeleccionados as $ss) {
                echo $ss;
              }

              exit();*/

              $qb
                 ->andWhere('i.estado IN (:estadosIds)')
                 ->setParameter('estadosIds', $estadosSeleccionados, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY);
              ;
          }

        }
     }

     return $qb
            ->getQuery()
            ->getResult();
  }

  public function findConexionesFtthPendientes($params)
  {
    $em = $this->getEntityManager();
    $conn = $em->getConnection();

    //RESTRICCIONES
    // que no tenga un gpon activo
    // que no este como en un estado distionto de conectado o pendiente
    // Que no tenga factura vencida
    $sql = "
      select
        c.id_conexion, i.latitud, i.longitud
      from conexiones c
      inner join conexiones_referencia cr on cr.id_conexion=c.id_conexion
      inner join inmuebles i on i.id_inmueble=c.id_inmueble
      inner join conexion_tarifas ct on ct.id_conexion=c.id_conexion
      where
        ct.id_tarifa in (?) and
        i.latitud is not null and
        i.longitud is not null and
        i.latitud <> '' and
        i.longitud <> '' and
        (cr.nap_segundo_nivel is null or cr.nap_segundo_nivel = '') and
        cr.habilitado='N' and
        ct.estado_servicio in (?) and
        c.id_conexion not in (select id_conexion from conexion_dispostivos where estado=0 and id_dispositivo in (select id_dispositivo from dispositivos where tipo_dispositivo='FTTH')) and
        c.id_conexion  not in (select id_conexion from facturas where factura_tipo='COMUN' and doc_tipo='CPTPG' and  id_conexion=c.id_conexion and factura_estado in (select factura_estado from FACTURAS_ESTADOS where estado_informes = 'DEUDA VENCIDA'))
    ";

    $i = 0;
    $paramsQ[$i] = [133,139,143,144,155,156,157,158];
    $paramsQTipos[$i] = \Doctrine\DBAL\Connection::PARAM_INT_ARRAY;
    if(isset($params["servicios"]) && $params["servicios"] != '') {
      $paramsQ[$i] = explode(";",$params["servicios"]);
    }

    $i++;
    $paramsQ[$i] = [1,3];
    $paramsQTipos[$i] = \Doctrine\DBAL\Connection::PARAM_INT_ARRAY;
    if(isset($params["estados"]) && $params["estados"] != '') {
      $paramsQ[$i] = explode(";",$params["estados"]);
    }

    // trato de forzar que sea int
    try {
      $params["conexion"] = (int)$params["conexion"];
    } catch(Exeption $e){}

    if(isset($params["conexion"]) && $params["conexion"] != '' && is_int($params["conexion"])) {
      $i++;
      $paramsQ[$i] = $params["conexion"];
      $paramsQTipos[$i] = null;
      $sql .= " and c.id_conexion = ?";
    }

    if(isset($params["operaciones"]) && $params["operaciones"] != '') {
      $sql .= " and exists (select 1 from cambio_dispositivos where estado = 0 and id_conexion=c.id_conexion and c.id_tarifa_ftth=id_tarifa) ";
    }

    $stmt = $conn->executeQuery($sql, $paramsQ, $paramsQTipos);

    return $stmt->fetchAll();
  }
}
