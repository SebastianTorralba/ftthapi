<?php
namespace AppBundle\Service;

use \Symfony\Component\DependencyInjection\ContainerInterface;
use League\Geotools\Geotools as Geotools;
use League\Geotools\Coordinate\Coordinate as Coordinate;
use League\Geotools\Polygon\Polygon as Polygon;

class GeotoolsService
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    protected $geotools;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->geotools  = new Geotools();
    }

    public function distanciaEntreArrayPuntos($puntos)
    {

      $distancia = 0;

      if (count($puntos) > 1) {

        for($i = 0; $i < (count($puntos)-1); $i++) {
            $distancia += $this->distanciaEntrePuntos($puntos[$i], $puntos[$i+1]);
        }
      }

      return $distancia;
    }

    public function distanciaEntrePuntos($origen = array(null,null), $destino = array(null,null))
    {
        $coordOrigen   = new Coordinate($origen);
        $coordDestino   = new Coordinate($destino);

        $distance = $this->geotools->distance()->setFrom($coordOrigen)->setTo($coordDestino);

        return $distance->in('m')->vincenty();
    }

    public function puntoEnPoligono($punto = [], $poligono=[])
    {
        $polygon = new Polygon($poligono);
        $polygon->setPrecision(6);

        return $polygon->pointInPolygon(new Coordinate($punto));
    }


}
