<?php

namespace AppBundle\Entity\Conexion;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("dbo.Conexion_tarifas")
 * @ORM\Entity()
 */
class Tarifa
{

  /**
   * @ORM\Column(name="id_conexion_ref", type="decimal", precision=18, scale=0)
   * @ORM\Id
   */
  private $id;

  /**
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Conexion", inversedBy="tarifasInformacion", cascade={"persist"})
   * @ORM\JoinColumn(name="id_conexion", referencedColumnName="id_conexion")
   */
  private $conexion;

  /*
   * @ORM\Column(name="id_tarifa", type="integer")
   */
  private $tarifa;

  /*
   * @ORM\Column(name="estado", type="integer")
   */
  private $estado;

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Tarifa
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set conexion
     *
     * @param \AppBundle\Entity\Conexion $conexion
     *
     * @return Tarifa
     */
    public function setConexion(\AppBundle\Entity\Conexion $conexion = null)
    {
        $this->conexion = $conexion;

        return $this;
    }

    /**
     * Get conexion
     *
     * @return \AppBundle\Entity\Conexion
     */
    public function getConexion()
    {
        return $this->conexion;
    }
}
