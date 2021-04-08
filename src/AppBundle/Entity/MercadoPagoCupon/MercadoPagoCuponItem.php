<?php

namespace AppBundle\Entity\MercadoPagoCupon;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("dbo.MercadoPagoCuponItem")
 * @ORM\Entity()
 */
class MercadoPagoCuponItem
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
     * @ORM\Column(name="cantidad", type="integer") 
     */
    private $cantidad;
    
    /**
     * @ORM\Column(name="monto", type="float") 
     */    
    private $monto;        
    
    /**
     * @ORM\Column(name="montoEnvio", type="float") 
     */    
    private $montoEnvio;  

    /**
     * @ORM\Column(name="montoTotal", type="float") 
     */    
    private $montoTotal;      
    
    /**
     * @ORM\Column(name="codigo", type="string", length=50) 
     */    
    private $codigo;       
        
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MercadoPagoCupon", inversedBy="items", cascade={"persist"})
     * @ORM\JoinColumn(name="mercadoPagoCupon_id", referencedColumnName="id")
     */
    private $mercadoPagoCupon;           

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
     * @return MercadoPagoCuponItem
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
     * Set cantidad
     *
     * @param integer $cantidad
     *
     * @return MercadoPagoCuponItem
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set monto
     *
     * @param float $monto
     *
     * @return MercadoPagoCuponItem
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
     * Set mercadoPagoCupon
     *
     * @param \AppBundle\Entity\MercadoPagoCupon $mercadoPagoCupon
     *
     * @return MercadoPagoCuponItem
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
     * Set codigo
     * 
     * @param string $codigo
     *
     * @return MercadoPagoCuponItem
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set montoEnvio
     *
     * @param float $montoEnvio
     *
     * @return MercadoPagoCuponItem
     */
    public function setMontoEnvio($montoEnvio)
    {
        $this->montoEnvio = $montoEnvio;

        return $this;
    }

    /**
     * Get montoEnvio
     *
     * @return float
     */
    public function getMontoEnvio()
    {
        return $this->montoEnvio;
    }

    /**
     * Set montoTotal
     *
     * @param float $montoTotal
     *
     * @return MercadoPagoCuponItem
     */
    public function setMontoTotal($montoTotal)
    {
        $this->montoTotal = $montoTotal;

        return $this;
    }

    /**
     * Get montoTotal
     *
     * @return float
     */
    public function getMontoTotal()
    {
        return $this->montoTotal;
    }
}
