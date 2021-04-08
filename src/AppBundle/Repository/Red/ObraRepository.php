<?php

namespace AppBundle\Repository\Red;
use Doctrine\ORM\EntityRepository;

class ObraRepository extends EntityRepository
{
    public function findByParams($searchString = "", $searchElemento = 0, $searchNombre = 0)
    {
        $qb = $this->createQueryBuilder("o")
              ->select("o")
              ->where("o.tipoRed = 'FTTH'")
              ->orderBy("o.nombre", "asc")
        ;

        if(trim($searchString) != "") {

          if($searchElemento == 1 && $searchNombre == 1) {

            $qb
              ->leftJoin("o.elementos", "eo")
              ->leftJoin("eo.elemento", "e")
              ->andWhere('e.codigo like :searchString or o.nombre like :searchString')
              ->setParameter("searchString", '%'.$searchString."%")
            ;
          }


          if($searchElemento == 1 && $searchNombre == 0) {

            $qb
              ->leftJoin("o.elementos", "eo")
              ->leftJoin("eo.elemento", "e")
              ->andWhere('e.codigo like :searchString')
              ->setParameter("searchString", $searchString."%")
            ;
          }


          if($searchNombre == 1 && $searchElemento == 0) {

            $qb
              ->andWhere("o.nombre like :searchString")
              ->setParameter("searchString", "%".$searchString."%")
            ;
          }

        }

        return $qb
                ->getQuery()
                ->getResult()
        ;
    }
}
