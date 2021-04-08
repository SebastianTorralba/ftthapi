<?php

namespace AppBundle\Entity\MercadoPagoCupon;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("dbo.MercadoPagoCuponMovimiento")
 * @ORM\Entity()
 */
class MercadoPagoCuponMovimiento
{
    /**
     * @ORM\Column(name="id", type="decimal", precision=18, scale=0)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="estado", type="string", length=250) 
     */
    private $estado;

    /**
     * @ORM\Column(name="medio", type="string", length=250) 
     */
    private $medio;    
    
    /**
     * @ORM\Column(name="estadoDetalle", type="text") 
     */
    private $estadoDetalle;
    
    
    /**
     * @ORM\Column(name="fechaHora", type="string") 
     */    
    private $fechaHora;        
        
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MercadoPagoCupon", inversedBy="movimientos", cascade={"persist"})
     * @ORM\JoinColumn(name="mercadoPagoCupon_id", referencedColumnName="id")
     */
    private $mercadoPagoCupon;     

    /**
     * @ORM\Column(name="monto", type="float") 
     */
    private $monto;    

    /**
     * @ORM\Column(name="montoDevuelto", type="float") 
     */
    private $montoDevuelto;      
    
    /**
     * @ORM\Column(name="montoFinal", type="float") 
     */
    private $montoFinal;   

    /**
     * @ORM\Column(name="montoFinalDevuelto", type="float") 
     */
    private $montoFinalDevuelto;  
    
    /**
     * @ORM\Column(name="idMovimiento", type="decimal", precision=18, scale=0)
     */
    private $idMovimiento;   
    
    /**
     * @ORM\Column(name="tipo", type="string", length=250) 
     */
    private $tipo;   
    
    /**
     * @ORM\Column(name="informacionExtra", type="text") 
     */
    private $informacionExtra;    
    
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MercadoPagoNotificacion", cascade={"persist"})
     * @ORM\JoinColumn(name="mercadoPagoNotificacion_id", referencedColumnName="id")
     */
    private $mercadoPagoNotificacion;      
        
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
     * Set estado
     *
     * @param string $estado
     *
     * @return MercadoPagoCuponMovimiento
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
     * Set medio
     *
     * @param string $medio
     *
     * @return MercadoPagoCuponMovimiento
     */
    public function setMedio($medio)
    {
        $this->medio = $medio;

        return $this;
    }

    /**
     * Get medio
     *
     * @return string
     */
    public function getMedio()
    {
        return $this->medio;
    }

    /**
     * Set estadoDetalle
     *
     * @param string $estadoDetalle
     *
     * @return MercadoPagoCuponMovimiento
     */
    public function setEstadoDetalle($estadoDetalle)
    {
        $this->estadoDetalle = $estadoDetalle;

        return $this;
    }

    /**
     * Get estadoDetalle
     *
     * @return string
     */
    public function getEstadoDetalle()
    {
        return $this->estadoDetalle;
    }

    /**
     * Set fechaHora
     *
     * @param string $fechaHora
     *
     * @return MercadoPagoCuponMovimiento
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
     * Set monto
     *
     * @param float $monto
     *
     * @return MercadoPagoCuponMovimiento
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return float
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set idMovimiento
     *
     * @param string $idMovimiento
     *
     * @return MercadoPagoCuponMovimiento
     */
    public function setIdMovimiento($idMovimiento)
    {
        $this->idMovimiento = $idMovimiento;

        return $this;
    }

    /**
     * Get idMovimiento
     *
     * @return string
     */
    public function getIdMovimiento()
    {
        return $this->idMovimiento;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     *
     * @return MercadoPagoCuponMovimiento
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set mercadoPagoCupon
     *
     * @param \AppBundle\Entity\MercadoPagoCupon $mercadoPagoCupon
     *
     * @return MercadoPagoCuponMovimiento
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
     * Set informacionExtra
     *
     * @param string $informacionExtra
     *
     * @return MercadoPagoCuponMovimiento
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
     * Set mercadoPagoNotificacion
     *
     * @param \AppBundle\Entity\MercadoPagoNotificacion $mercadoPagoNotificacion
     *
     * @return MercadoPagoCuponMovimiento
     */
    public function setMercadoPagoNotificacion(\AppBundle\Entity\MercadoPagoNotificacion $mercadoPagoNotificacion = null)
    {
        $this->mercadoPagoNotificacion = $mercadoPagoNotificacion;

        return $this;
    }

    /**
     * Get mercadoPagoNotificacion
     *
     * @return \AppBundle\Entity\MercadoPagoNotificacion
     */
    public function getMercadoPagoNotificacion()
    {
        return $this->mercadoPagoNotificacion;
    }

    /**
     * Set montoFinal
     *
     * @param float $montoFinal
     *
     * @return MercadoPagoCuponMovimiento
     */
    public function setMontoFinal($montoFinal)
    {
        $this->montoFinal = $montoFinal;

        return $this;
    }

    /**
     * Get montoFinal
     *
     * @return float
     */
    public function getMontoFinal()
    {
        return $this->montoFinal;
    }

    /**
     * Set montoFinalDevuelto
     *
     * @param float $montoFinalDevuelto
     *
     * @return MercadoPagoCuponMovimiento
     */
    public function setMontoFinalDevuelto($montoFinalDevuelto)
    {
        $this->montoFinalDevuelto = $montoFinalDevuelto;

        return $this;
    }

    /**
     * Get montoFinalDevuelto
     *
     * @return float
     */
    public function getMontoFinalDevuelto()
    {
        return $this->montoFinalDevuelto;
    }

    /**
     * Set montoDevuelto
     *
     * @param float $montoDevuelto
     *
     * @return MercadoPagoCuponMovimiento
     */
    public function setMontoDevuelto($montoDevuelto)
    {
        $this->montoDevuelto = $montoDevuelto;

        return $this;
    }

    /**
     * Get montoDevuelto
     *
     * @return float
     */
    public function getMontoDevuelto()
    {
        return $this->montoDevuelto;
    }
}
