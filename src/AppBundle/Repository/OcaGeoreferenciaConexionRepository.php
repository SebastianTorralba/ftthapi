<?php

namespace AppBundle\Repository;
use Doctrine\ORM\EntityRepository;

class OcaGeoreferenciaConexionRepository extends EntityRepository
{
    public function findByParams($firstResult, $params = [])
    {
        $qb = $this
                ->createQueryBuilder('c')
                ->select('c')
                ->innerJoin("c.georeferenciaActual", "g")
                ->setMaxResults(1000)
                ->setFirstResult($firstResult)
                ->addOrderBy("c.idConexion", "asc")
        ;

        if(isset($params["fechaAsignacion"])) {

            $fechas = explode(',', $params["fechaAsignacion"]);
            $qb->andWhere('c.fechaAsignacion in (:fechaAsignacion)')
            ->setParameter('fechaAsignacion', $fechas, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ;

        }

        if(isset($params["id_conexion"])) {
            $qb->andWhere('c.idConexion = :id_conexion')
            ->setParameter('id_conexion', $params["id_conexion"])
            ;
        }

        if(isset($params["id_dpto"])) {
            $qb->andWhere('c.idDpto = :id_dpto')
            ->setParameter('id_dpto', $params["id_dpto"])
            ;
        }

        if(isset($params["ccodloca"])) {
            $qb->andWhere('c.ccodloca = :ccodloca')
            ->setParameter('ccodloca', $params["ccodloca"])
            ;
        }

        if(isset($params["cod_barrio"])) {
            $qb->andWhere('c.codBarrio = :cod_barrio')
            ->setParameter('cod_barrio', $params["cod_barrio"])
            ;
        }

        if(isset($params["cod_calle"])) {
            $qb->andWhere('c.codCalle = :cod_calle')
            ->setParameter('cod_calle', $params["cod_calle"])
            ;
        }

        if(isset($params["id_estado_servicio"])) {

            $ids = explode(',', $params["id_estado_servicio"]);
            $qb->andWhere('c.idEstadoServicio in (:id_estado_servicio)')
            ->setParameter('id_estado_servicio', $ids, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ;
        }

        if(isset($params["fechaHoraGeoreferenciaDesde"])) {
            $qb->andWhere('g.fechaHoraGeoreferencia >= :fechaHoraGeoreferenciaDesde')
            ->setParameter('fechaHoraGeoreferenciaDesde', $params["fechaHoraGeoreferenciaDesde"])
            ;
        }

        if(isset($params["fechaHoraGeoreferenciaHasta"])) {
            $qb->andWhere('g.fechaHoraGeoreferencia <= :fechaHoraGeoreferenciaHasta')
            ->setParameter('fechaHoraGeoreferenciaHasta', $params["fechaHoraGeoreferenciaHasta"])
            ;
        }

        if(isset($params["id_usuario_georeferencia"])) {
            $qb
                ->innerJoin("g.usuario", "u")
                ->andWhere('u.id = :id_usuario_georeferencia')
                ->setParameter('id_usuario_georeferencia', $params["id_usuario_georeferencia"])
            ;
        }

        return $qb
                ->getQuery()
                ->getResult();
    }
}
