<?php

namespace AppBundle\Entity\MercadoPagoCupon;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("dbo.MercadoPagoCuponEstado")
 * @ORM\Entity()
 */
class MercadoPagoCuponEstado
{
    /**
     * @ORM\Column(name="id", type="decimal", precision=18, scale=0)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="nombre", type="string", length=250) 
     */
    private $nombre;
       
    /**
     * @ORM\Column(name="informacionExtra", type="text") 
     */
    private $informacionExtra;
    
    /**
     * @ORM\Column(name="descripcion", type="text") 
     */
    private $descripcion;
        
    /**
     * @ORM\Column(name="fechaHora", type="string") 
     */    
    private $fechaHora;        
        
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MercadoPagoCupon", inversedBy="estados", cascade={"persist"})
     * @ORM\JoinColumn(name="mercadoPagoCupon_id", referencedColumnName="id")
     */
    private $mercadoPagoCupon;           

    
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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return MercadoPagoCuponEstado
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set informacionExtra
     *
     * @param string $informacionExtra
     *
     * @return MercadoPagoCuponEstado
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

    /**
     * Set fechaHora
     *
     * @param \DateTime $fechaHora
     *
     * @return MercadoPagoCuponEstado
     */
    public function setFechaHora($fechaHora)
    {
        $this->fechaHora = $fechaHora;

        return $this;
    }

    /**
     * Get fechaHora
     *
     * @return \DateTime
     */
    public function getFechaHora()
    {
        return $this->fechaHora;
    }

    /**
     * Set mercadoPagoCupon
     *
     * @param \AppBundle\Entity\MercadoPagoCupon $mercadoPagoCupon
     *
     * @return MercadoPagoCuponEstado
     */
    public function setMercadoPagoCupon(\AppBundle\Entity\MercadoPagoCupon $mercadoPagoCupon = null)
    {
        $this->mercadoPagoCupon = $mercadoPagoCupon;

        return $this;
    }

    /**
     * Get mercadoPagoCupon
     *
     * @return \AppBundle\Entity\MercadoPagoCupon
     */
    public function getMercadoPagoCupon()
    {
        return $this->mercadoPagoCupon;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return MercadoPagoCuponEstado
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }
}
