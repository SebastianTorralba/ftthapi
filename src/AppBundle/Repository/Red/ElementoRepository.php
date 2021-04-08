<?php

namespace AppBundle\Repository\Red;
use Doctrine\ORM\EntityRepository;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use AppBundle\Entity as Entity;

class ElementoRepository extends NestedTreeRepository
{
    public function findElementosSinObra()
    {
      $qb = $this->getElements()
      ->andWhere("ea.estado=:ESTADO_PLANIFICADO")
      ->setParameter("ESTADO_PLANIFICADO", Entity\Red\Elemento\Estado::ESTADO_PLANIFICADO);

      return $qb->getQuery()->getArrayResult();
    }

    public function findElementosObra($obraId = 0)
    {
      $sql = 'select elemento_id from obras_red_elemento where obra_id='.$obraId;
      $qbn = $this->getEntityManager()->getConnection()->prepare($sql);
      $qbn->execute();

      $in = $qbn->fetchAll();

      $qb = $qb = $this->getElements();
      $qb->where("node.id in (:in)");
      $qb->setParameter("in", $in);

      return $qb->getQuery()->getArrayResult();
    }

    public function findElementosTarea($tareaId = 0)
    {
      $qb = $this->getElements();
      $qb->addSelect("partial oe.{id},partial te.{id,estado}");
      $qb->innerJoin("node.obra", "oe");
      $qb->innerJoin("oe.tareas", "te");
      $qb->innerJoin("te.tarea", "t");
      $qb->where("t.id=:tareaId");
      $qb->setParameter("tareaId", $tareaId);

      return $qb->getQuery()->getArrayResult();
    }

    public function findByParams($searchString = "",$searchTipo = 0, $searchCodigo = 0, $searchNombre = 0)
    {
        $qb = $this->getElements();

        if(trim($searchString) != "") {

          if($searchTipo == 1) {
            $qb
              ->innerJoin("node.elementoTipo", "t")
              ->orWhere('t.nombre like :searchString')
              ->setParameter("searchString", "%".$searchString."%")
            ;
          }

          if($searchCodigo == 1) {

            $qb
              ->orWhere('node.codigo like :searchString')
              ->setParameter("searchString", $searchString."%")
            ;
          }

          if($searchNombre == 1) {
            $qb
              ->orWhere("node.nombre like :searchString")
              ->setParameter("searchString", "%".$searchString."%")
            ;
          }

        }

        return $qb
                ->getQuery()
                ->getArrayResult();
        ;
    }

    public function findElementosPorTipo($tipo)
    {
      $qb = $this->getElements()
                  ->andWhere("et.id =:tipo")
                  ->setParameter("tipo", $tipo)
      ;

      return $qb
              ->getQuery()
              ->getArrayResult();
      ;
    }

    public function getElements()
    {
      return $this->childrenQueryBuilder(null, false, null, 'ASC',false)
                 ->select("
                    partial node.{id,nombre,codigo,lvl},
                    partial ep.{id},
                    partial ea.{id,estado},
                    partial et.{id,tipoGeoreferencia,nombre,colorHexa},
                    partial eg.{id,latitud,longitud}
                 ")
                 ->innerJoin('node.estadoActual', 'ea')
                 ->leftJoin('node.parent', 'ep')                 
                 ->innerJoin('node.elementoTipo', 'et')
                 ->innerJoin('node.georeferencias', 'eg')
                 ->orderBy("node.codigo", "asc")
                 ->setMaxResults(10000)
                 ->setFirstResult(0)
      ;
    }

}
