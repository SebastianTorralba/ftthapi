<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("dbo.productos_detalle")
 * @ORM\Entity()
 */
class ProductoDetalle
{
    public static $planesInternet = array(
        "C01" => "Instalación modem",
        "C14" => "IPT 2 MB",
        "C72" => "IPT 3 MB",
        "C74" => "IPT 6 MB",
        "C51" => "IPT 10 MB",
        "C52" => "IPT 20 MB",
        "C53" => "IPT 30 MB"
    );    
    
    /**
     * @ORM\Column(name="id_dispositivo", type="integer")
     * @ORM\Id
     */
    private $id;
    
    /**
     * @Assert\NotBlank(message="Por favor, ingrese nombre.")
     * @ORM\Column(name="monto", type="float") 
     */
    private $monto;    
    
    /**
     * @Assert\NotBlank(message="Por favor, ingrese nombre.")
     * @ORM\Column(name="dcto_lista", type="float") 
     */
    private $dctoLista;     
    
    /**
     * @Assert\NotBlank(message="Por favor, ingrese nombre.")
     * @ORM\Column(name="precio_lista", type="float") 
     */
    private $precioLista;         

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return ProductoDetalle
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set monto
     *
     * @param float $monto
     *
     * @return ProductoDetalle
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
     * Set dctoLista
     *
     * @param float $dctoLista
     *
     * @return ProductoDetalle
     */
    public function setDctoLista($dctoLista)
    {
        $this->dctoLista = $dctoLista;

        return $this;
    }

    /**
     * Get dctoLista
     *
     * @return float
     */
    public function getDctoLista()
    {
        return $this->dctoLista;
    }

    /**
     * Set precioLista
     *
     * @param float $precioLista
     *
     * @return ProductoDetalle
     */
    public function setPrecioLista($precioLista)
    {
        $this->precioLista = $precioLista;

        return $this;
    }

    /**
     * Get precioLista
     *
     * @return float
     */
    public function getPrecioLista()
    {
        return $this->precioLista;
    }
}
