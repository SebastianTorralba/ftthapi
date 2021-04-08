<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("dbo.OcaGeoreferenciaConexion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OcaGeoreferenciaConexionRepository")
 */
class OcaGeoreferenciaConexion
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="id_conexion", type="decimal", precision=18, scale=0)
     */
    private $idConexion;

    /**
     *
     * @ORM\Column(name="id_dpto", type="integer")
     */
    private $idDpto;

    /**
     * @ORM\Column(name="ccodloca", type="string", length=30)
     */
    private $ccodloca;

    /**
     * @ORM\Column(name="cod_barrio", type="integer")
     */
    private $codBarrio;

    /**
     * @ORM\Column(name="cod_calle", type="integer")
     */
    private $codCalle;

    /**
     * @ORM\Column(name="fechaAsignacion", type="date")
     */
    private $fechaAsignacion;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\OcaGeoreferenciaConexion\OcaGeoreferenciaConexionGeoreferencia", cascade={"persist"})
     * @ORM\JoinColumn(name="georeferenciaActual_id", referencedColumnName="id")
     */
    private $georeferenciaActual;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\OcaGeoreferenciaConexion\OcaGeoreferenciaConexionGeoreferencia", mappedBy="georeferenciaConexion", cascade={"persist"})
     */
    private $georeferencias;

    /**
     * @ORM\Column(name="id_estado_servicio", type="integer")
     */
    private $idEstadoServicio;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->georeferencias = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return OcaGeoreferenciaConexion
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
     * Set idDpto
     *
     * @param integer $idDpto
     *
     * @return OcaGeoreferenciaConexion
     */
    public function setIdDpto($idDpto)
    {
        $this->idDpto = $idDpto;

        return $this;
    }

    /**
     * Get idDpto
     *
     * @return integer
     */
    public function getIdDpto()
    {
        return $this->idDpto;
    }

    /**
     * Set ccodloca
     *
     * @param string $ccodloca
     *
     * @return OcaGeoreferenciaConexion
     */
    public function setCcodloca($ccodloca)
    {
        $this->ccodloca = $ccodloca;

        return $this;
    }

    /**
     * Get ccodloca
     *
     * @return string
     */
    public function getCcodloca()
    {
        return $this->ccodloca;
    }

    /**
     * Set codBarrio
     *
     * @param integer $codBarrio
     *
     * @return OcaGeoreferenciaConexion
     */
    public function setCodBarrio($codBarrio)
    {
        $this->codBarrio = $codBarrio;

        return $this;
    }

    /**
     * Get codBarrio
     *
     * @return integer
     */
    public function getCodBarrio()
    {
        return $this->codBarrio;
    }

    /**
     * Set codCalle
     *
     * @param integer $codCalle
     *
     * @return OcaGeoreferenciaConexion
     */
    public function setCodCalle($codCalle)
    {
        $this->codCalle = $codCalle;

        return $this;
    }

    /**
     * Get codCalle
     *
     * @return integer
     */
    public function getCodCalle()
    {
        return $this->codCalle;
    }

    /**
     * Set fechaAsignacion
     *
     * @param \DateTime $fechaAsignacion
     *
     * @return OcaGeoreferenciaConexion
     */
    public function setFechaAsignacion($fechaAsignacion)
    {
        $this->fechaAsignacion = $fechaAsignacion;

        return $this;
    }

    /**
     * Get fechaAsignacion
     *
     * @return \DateTime
     */
    public function getFechaAsignacion()
    {
        return $this->fechaAsignacion;
    }

    /**
     * Set idEstadoServicio
     *
     * @param integer $idEstadoServicio
     *
     * @return OcaGeoreferenciaConexion
     */
    public function setIdEstadoServicio($idEstadoServicio)
    {
        $this->idEstadoServicio = $idEstadoServicio;

        return $this;
    }

    /**
     * Get idEstadoServicio
     *
     * @return integer
     */
    public function getIdEstadoServicio()
    {
        return $this->idEstadoServicio;
    }

    /**
     * Add georeferencia
     *
     * @param \AppBundle\Entity\OcaGeoreferenciaConexion\OcaGeoreferenciaConexionGeoreferencia $georeferencia
     *
     * @return OcaGeoreferenciaConexion
     */
    public function addGeoreferencia(\AppBundle\Entity\OcaGeoreferenciaConexion\OcaGeoreferenciaConexionGeoreferencia $georeferencia)
    {
        $georeferencia->setGeoreferenciaConexion($this);

        $this->setGeoreferenciaActual($georeferencia);
        $this->georeferencias[] = $georeferencia;

        return $this;
    }

    /**
     * Remove georeferencia
     *
     * @param \AppBundle\Entity\OcaGeoreferenciaConexion\OcaGeoreferenciaConexionGeoreferencia $georeferencia
     */
    public function removeGeoreferencia(\AppBundle\Entity\OcaGeoreferenciaConexion\OcaGeoreferenciaConexionGeoreferencia $georeferencia)
    {
        $this->georeferencias->removeElement($georeferencia);
    }

    /**
     * Get georeferencias
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGeoreferencias()
    {
        return $this->georeferencias;
    }

    /**
     * Set georeferenciaActual
     *
     * @param \AppBundle\Entity\OcaGeoreferenciaConexion\OcaGeoreferenciaConexionGeoreferencia $georeferenciaActual
     *
     * @return OcaGeoreferenciaConexion
     */
    public function setGeoreferenciaActual(\AppBundle\Entity\OcaGeoreferenciaConexion\OcaGeoreferenciaConexionGeoreferencia $georeferenciaActual = null)
    {
        $this->georeferenciaActual = $georeferenciaActual;

        return $this;
    }

    /**
     * Get georeferenciaActual
     *
     * @return \AppBundle\Entity\OcaGeoreferenciaConexion\OcaGeoreferenciaConexionGeoreferencia
     */
    public function getGeoreferenciaActual()
    {
        return $this->georeferenciaActual;
    }
}
