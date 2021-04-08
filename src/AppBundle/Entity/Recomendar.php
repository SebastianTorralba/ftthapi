<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("dbo.preventaInternetRecomendacion")
 * @ORM\Entity()
 */
class Recomendar
{    
    /**
     * @ORM\Column(name="id", type="decimal", precision=18, scale=0)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @Assert\NotBlank(message="Por favor, ingrese nombre de su amigo.")
     * @ORM\Column(name="nombre", type="string", length=200) 
     */
    private $nombre;

    /**
     * @Assert\NotBlank(message="Por favor, ingrese email de su amigo.")
     * @Assert\Email(message="Por favor, ingrese una dirección de email válida.")
     * @ORM\Column(name="email", type="string", length=80, nullable=true)
     */    
    private $email;
    
    /**
     * @ORM\Column(name="nombreRecomendador", type="string", length=200, nullable=true) 
     */
    private $nombreRecomendador;

    /**
     * @ORM\Column(name="emailRecomendador", type="string", length=80, nullable=true)
     */    
    private $emailRecomendador;

    /**
     * @ORM\Column(name="mensaje", type="string", length=200, nullable=true) 
     */
    private $mensaje;    
    
    /**
     * @ORM\Column(name="fechaHoraCreado", type="datetime")
     */    
    private $fechaHoraCreado;     
 
    /**
     * Constructor
     */
    public function __construct()
    {
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
     * @return Recomendar
     */
    public function setNombre($nombre)
    {
        $this->nombre = utf8_decode($nombre);

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

    public function getNombre2()
    {
        return utf8_encode($this->nombre);
    }    
    
    /**
     * Set email
     *
     * @param string $email
     * @return Recomendar
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
     * Set nombreRecomendador
     *
     * @param string $nombreRecomendador
     * @return Recomendar
     */
    public function setNombreRecomendador($nombreRecomendador)
    {
        $this->nombreRecomendador = utf8_decode($nombreRecomendador);

        return $this;
    }
  
    /**
     * Get nombreRecomendador
     *
     * @return string 
     */
    public function getNombreRecomendador()
    {
        return $this->nombreRecomendador;
    }

    /**
     * Get nombreRecomendador
     *
     * @return string 
     */
    public function getNombreRecomendador2()
    {
        return utf8_encode($this->nombreRecomendador);
    }
    
    /**
     * Set emailRecomendador
     *
     * @param string $emailRecomendador
     * @return Recomendar
     */
    public function setEmailRecomendador($emailRecomendador)
    {
        $this->emailRecomendador = $emailRecomendador;

        return $this;
    }

    /**
     * Get emailRecomendador
     *
     * @return string 
     */
    public function getEmailRecomendador()
    {
        return $this->emailRecomendador;
    }

    /**
     * Set mensaje
     *
     * @param string $mensaje
     * @return Recomendar
     */
    public function setMensaje($mensaje)
    {
        $this->mensaje = utf8_decode($mensaje);

        return $this;
    }

    /**
     * Get mensaje
     *
     * @return string 
     */
    public function getMensaje()
    {
        return $this->mensaje;
    }

    /**
     * Get nombreRecomendador
     *
     * @return string 
     */
    public function getMensaje2()
    {
        return utf8_encode($this->mensaje);
    }
    
    /**
     * Set fechaHoraCreado
     *
     * @param \DateTime $fechaHoraCreado
     * @return Recomendar
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
}
