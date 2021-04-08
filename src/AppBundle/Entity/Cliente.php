<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table("dbo.abonados")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ClienteRepository")
 */
class Cliente implements UserInterface
{
    const EMPRESA_TELEFONO_CLARO    = 'CLARO';
    const EMPRESA_TELEFONO_PERSONAL = 'PERSO';
    const EMPRESA_TELEFONO_MOVISTAR = 'MOVIS';    
    const EMPRESA_TELEFONO_NEXTEL   = 'NEXTE';    
    
    static $empresasDeTelefono = array(
        self::EMPRESA_TELEFONO_CLARO    => 'Claro',
        self::EMPRESA_TELEFONO_PERSONAL => 'Personal',        
        self::EMPRESA_TELEFONO_MOVISTAR => 'Movistar',        
        self::EMPRESA_TELEFONO_NEXTEL   => 'Nextel'        
    );
    
    /*ACLARACIÓN: PARA LA EMPRESA LOS EXENTOS DE IVA SON 1 O NULL EN LA BD*/
    const CONDICION_IVA_RESPONSABLE_INSCRIPTO    = 1;
    const CONDICION_IVA_CONSUMIDOR_FINAL = 2;
    const CONDICION_IVA_MONOTRIBUTO = 6;    

    static $tiposDeFacturaSegunCondicionIva = array(
        self::CONDICION_IVA_RESPONSABLE_INSCRIPTO    => 'A',
        self::CONDICION_IVA_CONSUMIDOR_FINAL => 'B',        
        self::CONDICION_IVA_MONOTRIBUTO => 'B',           
    );    

    static $tiposCondicionIvaEnLosQuSeMuestraDetalleIva = array(
        self::CONDICION_IVA_RESPONSABLE_INSCRIPTO,
    );     
    
    /**
     * @ORM\Column(name="id_abonado", type="decimal", precision=18, scale=0)
     * @ORM\Id
     */
    private $username;

    /**
     * @ORM\Column(name="apellido_nombre", type="string", length=100)
     */
    private $nombre;
    
    /**
     * @ORM\Column(name="correo_electronico", type="string", length=100, nullable=true)
     * @Assert\NotBlank(message="Por favor, ingrese email")
     * @Assert\Email(
     *      message="Por favor, ingrese email válido",
     *      checkMX=true
     * )
     */
    private $email;   
    
    /**
     * @ORM\Column(name="celular", type="string", length=20, nullable=true)
     * @Assert\Length(
     *      min = 10,
     *      max = 10,
     *      exactMessage = "El número de celular debe tener 10 caracteres",
     * )
     */
    private $celular;   
    
    /**
     * @ORM\Column(name="celular_empresa", type="string", length=100, nullable=true)
     */
    private $celularEmpresa;     

    /**
     * @ORM\Column(name="password", type="string", length=100)
     */
    private $password;      

    /**
     * @ORM\OneToMany(targetEntity="Conexion", mappedBy="cliente")
     */
    private $conexiones;    
    
    /**
     * @ORM\Column(name="cuil_cuit", type="string", length=100)
     */
    private $cuit;    
    
    /**
     * @ORM\Column(name="factura_e", type="integer")
     */
    private $facturaElectronica;       
    
    /**
     * @ORM\Column(name="factura_e_fecha", type="datetime")
     */
    private $facturaElectronicaFecha;      

    /**
     * @ORM\Column(name="notificaciones_e", type="boolean")
     */
    private $notificacionElectronica;     
    
    /**
     * @ORM\Column(name="id_hash", type="string", length=250)
     */
    private $idHash;         
    
    /**
     * @ORM\Column(name="condicion_iva", type="integer")
     */
    private $condicionIva;     
    
    private $roles = ['ROLE_USER'];
    
    public function datosDeContactoCompletos()
    {
        return $this->celular != '' && $this->email != '' && $this->celularEmpresa != '';
    }
    
    //retorna las conexiones en estado conectadas
    public function getConexionesConectadas()
    {
        return $this->getConexiones()->filter(function($conexion){
            return $conexion->getInmueble()->isConectado();
        });
    }

    //retorna las conexiones en estado conectadas
    public function getConexionesSinServicio()
    {
        return $this->getConexiones()->filter(function($conexion){
            return !$conexion->getInmueble()->isConectado();
        });
    }
        
    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return $this->roles;
    }
    
    public function addRole($role)
    {
        $this->roles[] = $role;
        
        return $this->roles;
    }    

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }    
    
    public function isEnabled()
    {
        return false;
    }
    
    public function getUsernameFormateado()
    {
        return sprintf("%06d", $this->username);
    }    
    
    
    /**
     * Set id
     *
     * @param string $id
     * @return Cliente
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * Set password
     *
     * @param string $password
     * @return Cliente
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }    

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Cliente
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
        return substr(utf8_encode($this->nombre), 0, 40);
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Cliente
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
     * Set celular
     *
     * @param string $celular
     * @return Cliente
     */
    public function setCelular($celular)
    {
        $this->celular = $celular;

        return $this;
    }

    /**
     * Get celular
     *
     * @return string 
     */
    public function getCelular()
    {
        return $this->celular;
    }

    /**
     * Set celularEmpresa
     *
     * @param string $celularEmpresa
     * @return Cliente
     */
    public function setCelularEmpresa($celularEmpresa)
    {
        $this->celularEmpresa = $celularEmpresa;

        return $this;
    }

    /**
     * Get celularEmpresa
     *
     * @return string 
     */
    public function getCelularEmpresa()
    {
        return $this->celularEmpresa;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->conexiones = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add conexiones
     *
     * @param \AppBundle\Entity\Conexion $conexiones
     * @return Cliente
     */
    public function addConexione(\AppBundle\Entity\Conexion $conexiones)
    {
        $this->conexiones[] = $conexiones;

        return $this;
    }

    /**
     * Remove conexiones
     *
     * @param \AppBundle\Entity\Conexion $conexiones
     */
    public function removeConexione(\AppBundle\Entity\Conexion $conexiones)
    {
        $this->conexiones->removeElement($conexiones);
    }

    /**
     * Get conexiones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConexiones()
    {
        return $this->conexiones->filter(function($conexion){
            return $conexion->isVisible();
        });
    }
    
    public function __toString() 
    {
        return $this->getNombre();
    }

    /**
     * Set cuit
     *
     * @param string $cuit
     * @return Cliente
     */
    public function setCuit($cuit)
    {
        $this->cuit = $cuit;

        return $this;
    }

    /**
     * Get cuit
     *
     * @return string 
     */
    public function getCuit()
    {
        return $this->cuit;
    }

    /**
     * Set facturaElectronica
     *
     * @param boolean $facturaElectronica
     * @return Cliente
     */
    public function setFacturaElectronica($facturaElectronica)
    {
        $this->facturaElectronica = $facturaElectronica;

        return $this;
    }

    /**
     * Get facturaElectronica
     *
     * @return boolean 
     */
    public function getFacturaElectronica()
    {
        return $this->facturaElectronica;
    }

    /**
     * Set idHash
     *
     * @param string $idHash
     * @return Cliente
     */
    public function setIdHash($idHash)
    {
        $this->idHash = $idHash;

        return $this;
    }

    /**
     * Get idHash
     *
     * @return string 
     */
    public function getIdHash()
    {
        return $this->idHash;
    }

    /**
     * Set facturaElectronicaFecha
     *
     * @param boolean $facturaElectronicaFecha
     * @return Cliente
     */
    public function setFacturaElectronicaFecha($facturaElectronicaFecha)
    {
        $this->facturaElectronicaFecha = $facturaElectronicaFecha;

        return $this;
    }

    /**
     * Get facturaElectronicaFecha
     *
     * @return boolean 
     */
    public function getFacturaElectronicaFecha()
    {
        return $this->facturaElectronicaFecha;
    }

    /**
     * Set notificacionElectronica
     *
     * @param boolean $notificacionElectronica
     * @return Cliente
     */
    public function setNotificacionElectronica($notificacionElectronica)
    {
        $this->notificacionElectronica = $notificacionElectronica;

        return $this;
    }

    /**
     * Get notificacionElectronica
     *
     * @return boolean 
     */
    public function getNotificacionElectronica()
    {
        return $this->notificacionElectronica;
    }

    /**
     * Set condicionIva
     *
     * @param integer $condicionIva
     * @return Cliente
     */
    public function setCondicionIva($condicionIva)
    {
        $this->condicionIva = $condicionIva;

        return $this;
    }

    /**
     * Get condicionIva
     *
     * @return integer 
     */
    public function getCondicionIva()
    {
        // Si no tiene ningún tipo de iva para la empresa es como consumidor final
        return $this->condicionIva == null ? 2 : $this->condicionIva;
    }
}
