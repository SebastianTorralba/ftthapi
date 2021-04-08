<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("dbo.MercadoPagoNotificacion")
 * @ORM\Entity()
 */
class MercadoPagoNotificacion
{
    /**
     * @ORM\Column(name="id", type="decimal", precision=18, scale=0)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * 
     * @ORM\Column(name="idMp", type="string", length=250)
     */    
    private $idMp;         

    /**
     * 
     * @ORM\Column(name="topicMp", type="string", length=250)
     */    
    private $topicMp;        

    /**
     * 
     * @ORM\Column(name="estado", type="string", length=250)
     */    
    private $estado;        
    
    /**
     * @ORM\Column(name="fechaHora", type="string") 
     */    
    private $fechaHora;    
    
    /**
     * @ORM\Column(name="fechaHoraProcesada", type="string") 
     */    
    private $fechaHoraProcesada;   

    /**
     * @ORM\Column(name="fechaHoraRecibida", type="string") 
     */    
    private $fechaHoraRecibida;       
    
    /**
     * @ORM\Column(name="informacionExtra", type="text") 
     */
    private $informacionExtra;        
    
    public function __construct() {
        
        $fh = new \DateTime('NOW');
        $this->fechaHora = $fh->format('Y-m-d H:i:s');
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
     * Set idMp
     *
     * @param string $idMp
     *
     * @return MercadoPagoNotificacion
     */
    public function setIdMp($idMp)
    {
        $this->idMp = $idMp;

        return $this;
    }

    /**
     * Get idMp
     *
     * @return string
     */
    public function getIdMp()
    {
        return $this->idMp;
    }

    /**
     * Set topicMp
     *
     * @param string $topicMp
     *
     * @return MercadoPagoNotificacion
     */
    public function setTopicMp($topicMp)
    {
        $this->topicMp = $topicMp;

        return $this;
    }

    /**
     * Get topicMp
     *
     * @return string
     */
    public function getTopicMp()
    {
        return $this->topicMp;
    }

    /**
     * Set fechaHora
     *
     * @param string $fechaHora
     *
     * @return MercadoPagoNotificacion
     */
    public function setFechaHora($fechaHora)
    {
        $this->fechaHora = $fechaHora;

        return $this;
    }

    /**
     * Get fechaHora
     *
     * @return string
     */
    public function getFechaHora()
    {
        return $this->fechaHora;
    }

    /**
     * Set estado
     *
     * @param string $estado
     *
     * @return MercadoPagoNotificacion
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set fechaHoraProcesada
     *
     * @param string $fechaHoraProcesada
     *
     * @return MercadoPagoNotificacion
     */
    public function setFechaHoraProcesada($fechaHoraProcesada)
    {
        $this->fechaHoraProcesada = $fechaHoraProcesada;

        return $this;
    }

    /**
     * Get fechaHoraProcesada
     *
     * @return string
     */
    public function getFechaHoraProcesada()
    {
        return $this->fechaHoraProcesada;
    }

    /**
     * Set fechaHoraRecibida
     *
     * @param string $fechaHoraRecibida
     *
     * @return MercadoPagoNotificacion
     */
    public function setFechaHoraRecibida($fechaHoraRecibida)
    {
        $this->fechaHoraRecibida = $fechaHoraRecibida;

        return $this;
    }

    /**
     * Get fechaHoraRecibida
     *
     * @return string
     */
    public function getFechaHoraRecibida()
    {
        return $this->fechaHoraRecibida;
    }

    /**
     * Set informacionExtra
     *
     * @param string $informacionExtra
     *
     * @return MercadoPagoNotificacion
     */
    public function setInformacionExtra($informacionExtra)
    {
        $this->informacionExtra = $informacionExtra;

        return $this;
    }

    /**
     * Get informacionExtra
     *
     * @return string
     */
    public function getInformacionExtra()
    {
        return $this->informacionExtra;
    }
}
