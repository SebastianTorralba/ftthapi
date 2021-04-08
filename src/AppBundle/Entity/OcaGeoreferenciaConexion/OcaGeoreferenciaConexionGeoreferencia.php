<?php

namespace AppBundle\Entity\OcaGeoreferenciaConexion;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("dbo.OcaGeoreferenciaConexionGeoreferencia")
 * @ORM\Entity()
 */
class OcaGeoreferenciaConexionGeoreferencia
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OcaGeoreferenciaConexion", inversedBy="georeferencias")
     * @ORM\JoinColumn(name="georeferenciaConexion_id", referencedColumnName="id")
     */
    private $georeferenciaConexion;

    /**
     * @ORM\Column(name="id_conexion", type="decimal", precision=18, scale=0)
     */
    private $idConexion;

    /**
     * @ORM\Column(name="latitud", type="string", length=100)
     */
    private $latitud;

    /**
     * @ORM\Column(name="longitud", type="string", length=100)
     */
    private $longitud;

    /**
     * @ORM\Column(name="direccion", type="string", length=100)
     */
    private $direccion;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id_usuario")
     */
    private $usuario;

    /**
     * @ORM\Column(name="fechaHoraGeoreferencia", type="datetime")
     */
    private $fechaHoraGeoreferencia;

    /**
     * @ORM\Column(name="fechaHoraSincronizado", type="datetime")
     */
    private $fechaHoraSincronizado;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idConexion
     *
     * @param string $idConexion
     *
     * @return OcaGeoreferenciaConexionGeoreferencia
     */
    public function setIdConexion($idConexion)
    {
        $this->idConexion = $idConexion;

        return $this;
    }

    /**
     * Get idConexion
     *
     * @return string
     */
    public function getIdConexion()
    {
        return $this->idConexion;
    }

    /**
     * Set latitud
     *
     * @param string $latitud
     *
     * @return OcaGeoreferenciaConexionGeoreferencia
     */
    public function setLatitud($latitud)
    {
        $this->latitud = $latitud;

        return $this;
    }

    /**
     * Get latitud
     *
     * @return string
     */
    public function getLatitud()
    {
        return $this->latitud;
    }

    /**
     * Set longitud
     *
     * @param string $longitud
     *
     * @return OcaGeoreferenciaConexionGeoreferencia
     */
    public function setLongitud($longitud)
    {
        $this->longitud = $longitud;

        return $this;
    }

    /**
     * Get longitud
     *
     * @return string
     */
    public function getLongitud()
    {
        return $this->longitud;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     *
     * @return OcaGeoreferenciaConexionGeoreferencia
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set usuario
     *
     * @param \AppBundle\Entity\Usuario $usuario
     *
     * @return OcaGeoreferenciaConexionGeoreferencia
     */
    public function setUsuario(\AppBundle\Entity\Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \AppBundle\Entity\Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set georeferenciaConexion
     *
     * @param \AppBundle\Entity\OcaGeoreferenciaConexion $georeferenciaConexion
     *
     * @return OcaGeoreferenciaConexionGeoreferencia
     */
    public function setGeoreferenciaConexion(\AppBundle\Entity\OcaGeoreferenciaConexion $georeferenciaConexion = null)
    {
        $this->georeferenciaConexion = $georeferenciaConexion;

        return $this;
    }

    /**
     * Get georeferenciaConexion
     *
     * @return \AppBundle\Entity\OcaGeoreferenciaConexion
     */
    public function getGeoreferenciaConexion()
    {
        return $this->georeferenciaConexion;
    }

    /**
     * Set fechaHoraGeoreferencia
     *
     * @param \DateTime $fechaHoraGeoreferencia
     *
     * @return OcaGeoreferenciaConexionGeoreferencia
     */
    public function setFechaHoraGeoreferencia($fechaHoraGeoreferencia)
    {
        $this->fechaHoraGeoreferencia = $fechaHoraGeoreferencia;

        return $this;
    }

    /**
     * Get fechaHoraGeoreferencia
     *
     * @return \DateTime
     */
    public function getFechaHoraGeoreferencia()
    {
        return $this->fechaHoraGeoreferencia;
    }

    /**
     * Set fechaHoraSincronizado
     *
     * @param \DateTime $fechaHoraSincronizado
     *
     * @return OcaGeoreferenciaConexionGeoreferencia
     */
    public function setFechaHoraSincronizado($fechaHoraSincronizado)
    {
        $this->fechaHoraSincronizado = $fechaHoraSincronizado;

        return $this;
    }

    /**
     * Get fechaHoraSincronizado
     *
     * @return \DateTime
     */
    public function getFechaHoraSincronizado()
    {
        return $this->fechaHoraSincronizado;
    }
}
