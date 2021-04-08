<?php

namespace AppBundle\Entity\PreventaInternet;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("dbo.preventaInternetItem")
 * @ORM\Entity()
 */
class PreventaInternetItem
{
    /**
     * @ORM\Column(name="id", type="decimal", precision=18, scale=0)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @Assert\NotBlank(message="Por favor, ingrese nombre.")
     * @ORM\Column(name="nombre", type="string", length=200) 
     */
    private $nombre;
    
    /**
     * @Assert\NotBlank(message="Por favor, ingrese nombre.")
     * @ORM\Column(name="tipo", type="string", length=20) 
     */
    private $tipo;
    
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PreventaInternet", inversedBy="preventaInternetItems", cascade={"persist"})
     * @ORM\JoinColumn(name="preventaInternet_id", referencedColumnName="id")
     * 
     */
    private $preventaInternet;       
    
    /**
     * @ORM\Column(name="cantidad", type="integer") 
     */    
    private $cantidad = 1;    
    
    private $valor = 0;    
    
    function setValor($valor) {
        $this->valor = $valor;
    }

    function getValor() {
        return $this->valor;
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
     * @return PreventaInternetItem
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
     * Set tipo
     *
     * @param string $tipo
     * @return PreventaInternetItem
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
     * Set PreventaInternet
     *
     * @param \AppBundle\Entity\PreventaInternet $preventaInternet
     * @return PreventaInternetItem
     */
    public function setPreventaInternet(\AppBundle\Entity\PreventaInternet $preventaInternet = null)
    {
        $this->preventaInternet = $preventaInternet;

        return $this;
    }

    /**
     * Get PreventaInternet
     *
     * @return \AppBundle\Entity\PreventaInternet 
     */
    public function getPreventaInternet()
    {
        return $this->preventaInternet;
    }

    /**
     * Set cantidad
     *
     * @param integer $cantidad
     * @return PreventaInternetItem
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
}
