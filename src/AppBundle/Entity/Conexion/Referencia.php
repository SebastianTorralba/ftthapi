<?php

namespace AppBundle\Entity\Conexion;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("dbo.Conexiones_referencia")
 * @ORM\Entity()
 */
class Referencia
{

  /**
   * @ORM\Column(name="id_conexion_ref", type="decimal", precision=18, scale=0)
   * @ORM\Id
   */
  private $id;

  /**
   * @ORM\OneToOne(targetEntity="AppBundle\Entity\Conexion", inversedBy="referencia", cascade={"persist"})
   * @ORM\JoinColumn(name="id_conexion", referencedColumnName="id_conexion")
   *
   */
  private $conexion;

  /**
  * @ORM\Column(name="nap_segundo_nivel", type="string", length=20)
  */
  private $napSegundoNivel;

  /**
  * @ORM\Column(name="nap_primer_nivel", type="string", length=20)
  */
  private $napPrimerNivel;

  /**
  * @ORM\Column(name="comentario", type="string", length=50)
  */
  private $comentario;

  /**
  * @ORM\Column(name="habilitado", type="string", length=1)
  */
  private $habilitado;

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Referencia
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
     * @return Referencia
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

    public function setNapSegundoNivel($napSegundoNivel)
    {
        $this->napSegundoNivel = $napSegundoNivel;

        return $this;
    }

    public function getNapSegundoNivel()
    {
        return $this->napSegundoNivel;
    }

    public function setNapPrimerNivel($napPrimerNivel)
    {
        $this->napPrimerNivel = $napPrimerNivel;

        return $this;
    }

    public function getComentario()
    {
        return $this->comentario;
    }

    public function setComentario($comentario)
    {
        $this->comentario = $comentario;

        return $this;
    }

    public function getNapPrimerNivel()
    {
        return $this->napPrimerNivel;
    }

    public function setHabilitado($habilitado)
    {
        $this->habilitado = $habilitado;

        return $this;
    }

    public function getHabilitado()
    {
        return $this->habilitado;
    }
}
