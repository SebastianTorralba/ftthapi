<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\Criteria;
use AppBundle\Entity\Factura;

/**
 * @ORM\Table("dbo.Conexiones")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ConexionRepository")
 */
class Conexion
{
    public static $servicios = [
      "HFC06"  => [12,30,40,48,49],
      "HFC10"  => [27,111],
      "HFC12"  => [3,131,135],
      "HFC20"  => [28,112,134,136],
      "HFC30"  => [29,113],
      "HFC40"  => [132,137],
      "HFC50"  => [119,138],
      "WIFI06"  => [47,37,25],
      "WIFI10"  => [120],
      "WIFI20"  => [103,121],
      "TV"      => [100,101],
      "FTTH50"  => [139,144],
      "FTTH100" => [133,143],
      "FTTH111" => [140],
      "FTTH262" => [141],
      "FTTH200" => [155,156],
      "FTTH300" => [157,158],
    ];

    /**
     * @ORM\Column(name="id_conexion", type="decimal", precision=18, scale=0)
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Inmueble", cascade={"persist"})
     * @ORM\JoinColumn(name="id_inmueble", referencedColumnName="id_inmueble")
     */
    private $inmueble;

    /**
     * @ORM\Column(name="id_tarifa", type="integer")
     */
    private $idTarifa;

    /**
     * @ORM\Column(name="id_tarifa_television", type="integer")
     */
    private $idTarifaTelevision;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Conexion\Referencia", mappedBy="conexion")
     */
    private $referencia;

    /**
     * @ORM\Column(name="id_tarifa_ftth", type="integer")
     */
    private $idTarifaFtth;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Conexion\Tarifa", mappedBy="conexion", cascade={"persist"})
     */
    private $tarifasInformacion;

    public function getTarifas()
    {
      $tarifas = [];
      foreach (self::$servicios as $key => $value) {
        if (in_array($this->getIdTarifa(), $value) || in_array($this->getIdTarifaTelevision(), $value) || in_array($this->getIdTarifaFtth(), $value)) {
          $tarifas[] = [
            "id"          => $key,
            "descripcion" => $key,
            "estado"      => $this->getInmueble()->getEstado(),
            "cantidad"    => 1,
            "unidad"      => ""
          ];
        }
      }

      return count($tarifas) > 0 ? $tarifas : null;
    }

    public function getElementoArray()
    {

      $conexion = [];

      $conexion["id"]       = $this->getId();
      $conexion["latitud"]  = $this->getInmueble() ? $this->getInmueble()->getLatitud() : null;
      $conexion["longitud"] = $this->getInmueble() ? $this->getInmueble()->getLongitud() : null;

      $conexion["resumen"]  = [
        "categoria" => "Conexiones",
        "elementos" => $this->getTarifas()
      ];

      return $conexion;
    }

    public function isVisible()
    {
        return $this->inmueble->inEstadoDeclarados();
    }

    public function getIdFormateado()
    {
        return sprintf('%07d', $this->id);
    }

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Conexion
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idTarifa
     *
     * @param integer $idTarifa
     *
     * @return Conexion
     */
    public function setIdTarifa($idTarifa)
    {
        $this->idTarifa = $idTarifa;

        return $this;
    }

    /**
     * Get idTarifa
     *
     * @return integer
     */
    public function getIdTarifa()
    {
        return $this->idTarifa ? $this->idTarifa : 0;
    }

    /**
     * Set idTarifaTelevision
     *
     * @param integer $idTarifaTelevision
     *
     * @return Conexion
     */
    public function setIdTarifaTelevision($idTarifaTelevision)
    {
        $this->idTarifaTelevision = $idTarifaTelevision;

        return $this;
    }

    /**
     * Get idTarifaTelevision
     *
     * @return integer
     */
    public function getIdTarifaTelevision()
    {
        return $this->idTarifaTelevision ? $this->idTarifaTelevision : 0;
    }

    /**
     * Set idTarifaFtth
     *
     * @param integer $idTarifaFtth
     *
     * @return Conexion
     */
    public function setIdTarifaFtth($idTarifaFtth)
    {
        $this->idTarifaFtth = $idTarifaFtth;

        return $this;
    }

    /**
     * Get idTarifaFtth
     *
     * @return integer
     */
    public function getIdTarifaFtth()
    {
        return $this->idTarifaFtth ? $this->idTarifaFtth : 0;
    }

    /**
     * Set inmueble
     *
     * @param \AppBundle\Entity\Inmueble $inmueble
     *
     * @return Conexion
     */
    public function setInmueble(\AppBundle\Entity\Inmueble $inmueble = null)
    {
        $this->inmueble = $inmueble;

        return $this;
    }

    /**
     * Get inmueble
     *
     * @return \AppBundle\Entity\Inmueble
     */
    public function getInmueble()
    {
        return $this->inmueble;
    }

    /**
     * Set referencia
     *
     * @param \AppBundle\Entity\Conexion\Referencia $referencia
     *
     * @return Conexion
     */
    public function setReferencia(\AppBundle\Entity\Conexion\Referencia $referencia = null)
    {
        $this->referencia = $referencia;

        return $this;
    }

    /**
     * Get referencia
     *
     * @return \AppBundle\Entity\Conexion\Referencia
     */
    public function getReferencia()
    {
        return $this->referencia;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->referencia = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tarifasInformacion = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add referencium
     *
     * @param \AppBundle\Entity\Conexion\Referencia $referencium
     *
     * @return Conexion
     */
    public function addReferencium(\AppBundle\Entity\Conexion\Referencia $referencium)
    {
        $this->referencia[] = $referencium;

        return $this;
    }

    /**
     * Remove referencium
     *
     * @param \AppBundle\Entity\Conexion\Referencia $referencium
     */
    public function removeReferencium(\AppBundle\Entity\Conexion\Referencia $referencium)
    {
        $this->referencia->removeElement($referencium);
    }

    /**
     * Add tarifasInformacion
     *
     * @param \AppBundle\Entity\Conexion\Tarifa $tarifasInformacion
     *
     * @return Conexion
     */
    public function addTarifasInformacion(\AppBundle\Entity\Conexion\Tarifa $tarifasInformacion)
    {
        $this->tarifasInformacion[] = $tarifasInformacion;

        return $this;
    }

    /**
     * Remove tarifasInformacion
     *
     * @param \AppBundle\Entity\Conexion\Tarifa $tarifasInformacion
     */
    public function removeTarifasInformacion(\AppBundle\Entity\Conexion\Tarifa $tarifasInformacion)
    {
        $this->tarifasInformacion->removeElement($tarifasInformacion);
    }

    /**
     * Get tarifasInformacion
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTarifasInformacion()
    {
        return $this->tarifasInformacion;
    }
}
