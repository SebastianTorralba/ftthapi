<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("dbo.preventaInternet")
 * @ORM\Entity()
 */
class PreventaInternet
{
    public static $planesInternet = array(
        "PLAN 2 MB",
        "PLAN 3 MB",
        "PLAN 6 MB",
        "PLAN 10 MB",
        "PLAN 20 MB",
        "PLAN 30 MB",        
    ); 

    public static $planesTv = array(
        "PACK CLÁSICO",
        "PACK FULL",
        "PACK CINE PREMIUN FOX",
        "PACK CINE PREMIUN HBO",
        "PACK ADULTO",
    );     

    public static $decodificadoresTv = array(1, 2, 3);         
    
    public static $promociones = array(
        "PACK CLÁSICO",
        "PACK FULL",
        "PACK CINE PREMIUN FOX",
        "PACK CINE PREMIUN HBO",
        "PACK ADULTO",
    );         
    
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
     * @Assert\Email(message="Por favor, ingrese una dirección de email válida.")
     * @ORM\Column(name="email", type="string", length=80, nullable=true)
     */    
    private $email;

    /**
     * @Assert\NotBlank(message="Por favor, ingrese teléfono")
     * @Assert\Length(
     *      min = 10,
     *      max = 10,
     *      exactMessage = "Debe ingresar 10 números. Sin el 15"
     * )
     * @ORM\Column(name="telefono", type="string", length=10)
     */    
    private $telefono;

    /**
     * 
     * @Assert\NotBlank(message="Por favor, ingrese su dirección")
     * @ORM\Column(name="direccion", type="string", length=100)
     */    
    private $direccion;    

    /**
     * @ORM\Column(name="fechaHoraCreado", type="datetime")
     */    
    private $fechaHoraCreado;  
    
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PreventaInternet\PreventaInternetItem", mappedBy="preventaInternet", cascade={"persist"})
     */
    private $preventaInternetItems;    
    
    /**
     * @Assert\IsTrue(message="Debe aceptar los términos y condiciones")
     */    
    private $terminos = false;  
    
    /**
     * 
     * @ORM\Column(name="fuente", type="string", length=100)
     */    
    private $fuente = 'web';        
    
    /**
     * @ORM\Column(name="dni", type="string", length=10) 
     */
    private $dni;    
    
    /**
     * @Assert\GreaterThan(
     *     value = 0,
           message="La cantidad de productos debe ser mayor que 0",
     *     groups={"preventa"}
     * )
     */    
    private $cantidad = 0;    
    
    /**
     * @ORM\Column(name="observacion", type="text", nullable=true)
     */    
    private $observacion;        
            
    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id_usuario")
     */ 
    private $usuarioCreo;     
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->preventaInternetItems = new \Doctrine\Common\Collections\ArrayCollection();
        $this->fechaHoraCreado = new \DateTime();
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
     * @return PreventaInternet
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
     * Set email
     *
     * @param string $email
     * @return PreventaInternet
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     * @return PreventaInternet
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string 
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     * @return PreventaInternet
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
     * Add preventaInternetItems
     *
     * @param \AppBundle\Entity\PreventaInternet\PreventaInternetItem $preventaInternetItems
     * @return PreventaInternet
     */
    public function addPreventaInternetItem(\AppBundle\Entity\PreventaInternet\PreventaInternetItem $preventaInternetItems)
    {
        $preventaInternetItems->setPreventaInternet($this);
        $this->preventaInternetItems[] = $preventaInternetItems;
        
        return $this;
    }

    /**
     * Remove preventaInternetItems
     *
     * @param \AppBundle\Entity\PreventaInternet\PreventaInternetItem $preventaInternetItems
     */
    public function removePreventaInternetItem(\AppBundle\Entity\PreventaInternet\PreventaInternetItem $preventaInternetItems)
    {
        $this->preventaInternetItems->removeElement($preventaInternetItems);
    }

    /**
     * Get preventaInternetItems
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPreventaInternetItems()
    {
        return $this->preventaInternetItems;
    }

    /**
     * Set fechaHoraCreado
     *
     * @param \DateTime $fechaHoraCreado
     * @return PreventaInternet
     */
    public function setFechaHoraCreado($fechaHoraCreado)
    {
        $this->fechaHoraCreado = $fechaHoraCreado;

        return $this;
    }

    /**
     * Get fechaHoraCreado
     *
     * @return \DateTime 
     */
    public function getFechaHoraCreado()
    {
        return $this->fechaHoraCreado;
    }

    /**
     * Set terminos
     *
     * @param boolean $terminos
     * @return PreventaInternet
     */
    public function setTerminos($terminos)
    {
        $this->terminos = $terminos;

        return $this;
    }

    /**
     * Get terminos
     *
     * @return boolean 
     */
    public function getTerminos()
    {
        return $this->terminos;
    }    

    /**
     * Set fuente
     *
     * @param string $fuente
     *
     * @return PreventaInternet
     */
    public function setFuente($fuente)
    {
        $this->fuente = $fuente;

        return $this;
    }

    /**
     * Get fuente
     *
     * @return string
     */
    public function getFuente()
    {
        return $this->fuente;
    }

    /**
     * Set dni
     *
     * @param string $dni
     *
     * @return PreventaInternet
     */
    public function setDni($dni)
    {
        $this->dni = $dni;

        return $this;
    }

    /**
     * Get dni
     *
     * @return string
     */
    public function getDni()
    {
        return $this->dni;
    }
    
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getCantidad()
    {
        return $this->cantidad;
    }    
    
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;

        return $this;
    }

    public function getObservacion()
    {
        return $this->observacion;
    }      

    /**
     * Set usuarioCreo
     *
     * @param \AppBundle\Entity\Usuario $usuarioCreo
     *
     * @return PreventaInternet
     */
    public function setUsuarioCreo(\AppBundle\Entity\Usuario $usuarioCreo = null)
    {
        $this->usuarioCreo = $usuarioCreo;

        return $this;
    }

    /**
     * Get usuarioCreo
     *
     * @return \AppBundle\Entity\Usuario
     */
    public function getUsuarioCreo()
    {
        return $this->usuarioCreo;
    }
}
