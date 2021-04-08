<?php

namespace AppBundle\Repository\Red\Obra;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity;

class TareaElementoRepository extends EntityRepository
{
  public function findPorTareaElemento($tarea = null, $elemento = null)
  {
    $qb = $this->createQueryBuilder("te")
               ->innerJoin("te.elemento", "e")
          ->where("e.elemento =:elemento and te.tarea=:tarea and te.estado=:estado")
          ->setParameter("tarea", $tarea)
          ->setParameter("elemento", $elemento)
          ->setParameter("estado", Entity\Red\Obra\TareaElemento::ESTADO_PENDIENTE)
          ->setMaxResults(1)
    ;

    return $qb->getQuery()->getOneOrNullResult();
  }

  public function findElementosPorTarea($tarea = null)
  {
    $qb = $this->createQueryBuilder("te")
               ->innerJoin("te.elemento", "oe")
               ->innerJoin("oe.elemento", "node")
               ->select("
                  partial te.{id,estado},
                  partial oe.{id},
                  partial node.{id,codigo},
                  partial ea.{id,estado},
                  partial et.{id,tipoGeoreferencia,nombre,colorHexa},
                  partial eg.{id,latitud,longitud}                                    
               ")
               ->innerJoin('node.estadoActual', 'ea')
               ->innerJoin('node.elementoTipo', 'et')
               ->innerJoin('node.georeferencias', 'eg')
               ->orderBy("node.codigo", "asc")
               ->where("te.tarea=:tarea")
               ->setParameter("tarea", $tarea)
    ;

    return $qb->getQuery()->getArrayResult();
  }
}
