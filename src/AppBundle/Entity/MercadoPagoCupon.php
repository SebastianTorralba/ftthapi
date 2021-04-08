<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("dbo.MercadoPagoCupon")
 * @ORM\Entity()
 */
class MercadoPagoCupon
{
    /**
     * @ORM\Column(name="id", type="decimal", precision=18, scale=0)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * 
     * @ORM\Column(name="token", type="text")
     */
    private $token;

    /**
     * @ORM\Column(name="ventaId", type="integer")
     */
    private $ventaId;

    /**
     * @ORM\Column(name="dni", type="string", length=10)
     */
    private $dni;

    /**
     * @ORM\Column(name="cuit", type="string", length=20)
     */
    private $cuit;

    /**
     * @ORM\Column(name="nombre", type="string", length=200)
     */
    private $nombre;

    /**
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(name="resumenEstadoActualPago", type="string", length=250)
     */
    private $resumenEstadoActualPago;

    /**
     * @ORM\Column(name="telefono", type="string", length=10)
     */
    private $telefono;

    /**
     * @ORM\Column(name="direccionCalle", type="string", length=150)
     */
    private $direccionCalle;

    /**
     * @ORM\Column(name="direccionNumero", type="integer")
     */
    private $direccionNumero;

    /**
     * @ORM\Column(name="direccionBarrio", type="string", length=150)
     */
    private $direccionBarrio;

    /**
     * @ORM\Column(name="direccionCp", type="integer")
     */
    private $direccionCp;

    /**
     * @ORM\Column(name="direccionLocalidad", type="string", length=150)
     */
    private $direccionLocalidad;

    /**
     * @ORM\Column(name="direccionProvincia", type="string", length=150)
     */
    private $direccionProvincia;

    /**
     * @ORM\Column(name="tipoPersona", type="integer")
     */
    private $tipoPersona;

    /**
     * @ORM\Column(name="condicionIva", type="integer")
     */
    private $condicionIva;

    /**
     * @ORM\Column(name="sexo", type="string", length=3)
     */
    private $sexo;

    /**
     * @ORM\Column(name="url", type="text")
     */
    private $url;

    /**
     * @ORM\Column(name="urlPrueba", type="text")
     */
    private $urlPrueba;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MercadoPagoCupon\MercadoPagoCuponItem", mappedBy="mercadoPagoCupon")
     */
    private $items;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\MercadoPagoCupon\MercadoPagoCuponEstado",cascade={"persist"})
     * @ORM\JoinColumn(name="estadoActual_id", referencedColumnName="id")
     */
    private $estadoActual;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MercadoPagoCupon\MercadoPagoCuponEstado", mappedBy="mercadoPagoCupon")
     */
    private $estados;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MercadoPagoCupon\MercadoPagoCuponMovimiento", mappedBy="mercadoPagoCupon",cascade={"persist"})
     */
    private $movimientos;

    /**
     * @ORM\Column(name="enviarDomicilio", type="integer")
     */
    private $enviarDomicilio;

    /**
     * @ORM\Column(name="envioTarifaId", type="integer")
     */
    private $envioTarifaId;

    /**
     * @ORM\Column(name="id_usuario", type="integer")
     */
    private $idUsuario;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
        $this->estados = new \Doctrine\Common\Collections\ArrayCollection();
        $this->movimientos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getPagoTotal()
    {
        $paid_amount = 0;
        $pagosId = array();

        foreach ($this->getMovimientos() as $mov) {
            if ($mov->getEstado() == 'approved' && in_array($mov->getIdMovimiento(), $pagosId) == false){
                $paid_amount += $mov->getMonto();
                $paid_amount -= $mov->getMontoDevuelto();
                $pagosId[] = $mov->getIdMovimiento();
            }
        }

        return $paid_amount;
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
     * Set token
     *
     * @param string $token
     *
     * @return MercadoPagoCupon
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set dni
     *
     * @param string $dni
     *
     * @return MercadoPagoCupon
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

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return MercadoPagoCupon
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
     *
     * @return MercadoPagoCupon
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
     *
     * @return MercadoPagoCupon
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
     * Set direccionCalle
     *
     * @param string $direccionCalle
     *
     * @return MercadoPagoCupon
     */
    public function setDireccionCalle($direccionCalle)
    {
        $this->direccionCalle = $direccionCalle;

        return $this;
    }

    /**
     * Get direccionCalle
     *
     * @return string
     */
    public function getDireccionCalle()
    {
        return $this->direccionCalle;
    }

    /**
     * Set direccionNumero
     *
     * @param string $direccionNumero
     *
     * @return MercadoPagoCupon
     */
    public function setDireccionNumero($direccionNumero)
    {
        $this->direccionNumero = $direccionNumero;

        return $this;
    }

    /**
     * Get direccionNumero
     *
     * @return string
     */
    public function getDireccionNumero()
    {
        return $this->direccionNumero;
    }

    /**
     * Set direccionBarrio
     *
     * @param string $direccionBarrio
     *
     * @return MercadoPagoCupon
     */
    public function setDireccionBarrio($direccionBarrio)
    {
        $this->direccionBarrio = $direccionBarrio;

        return $this;
    }

    /**
     * Get direccionBarrio
     *
     * @return string
     */
    public function getDireccionBarrio()
    {
        return $this->direccionBarrio;
    }

    /**
     * Set direccionCp
     *
     * @param string $direccionCp
     *
     * @return MercadoPagoCupon
     */
    public function setDireccionCp($direccionCp)
    {
        $this->direccionCp = $direccionCp;

        return $this;
    }

    /**
     * Get direccionCp
     *
     * @return string
     */
    public function getDireccionCp()
    {
        return $this->direccionCp;
    }

    /**
     * Set direccionLocalidad
     *
     * @param string $direccionLocalidad
     *
     * @return MercadoPagoCupon
     */
    public function setDireccionLocalidad($direccionLocalidad)
    {
        $this->direccionLocalidad = $direccionLocalidad;

        return $this;
    }

    /**
     * Get direccionLocalidad
     *
     * @return string
     */
    public function getDireccionLocalidad()
    {
        return $this->direccionLocalidad;
    }

    /**
     * Set direccionProvincia
     *
     * @param string $direccionProvincia
     *
     * @return MercadoPagoCupon
     */
    public function setDireccionProvincia($direccionProvincia)
    {
        $this->direccionProvincia = $direccionProvincia;

        return $this;
    }

    /**
     * Get direccionProvincia
     *
     * @return string
     */
    public function getDireccionProvincia()
    {
        return $this->direccionProvincia;
    }

    /**
     * Add item
     *
     * @param \AppBundle\Entity\MercadoPagoCupon\MercadoPagoCuponItem $item
     *
     * @return MercadoPagoCupon
     */
    public function addItem(\AppBundle\Entity\MercadoPagoCupon\MercadoPagoCuponItem $item)
    {
        $item->setMercadoPagoCupon($this);
        $this->items[] = $item;

        return $this;
    }

    /**
     * Remove item
     *
     * @param \AppBundle\Entity\MercadoPagoCupon\MercadoPagoCuponItem $item
     */
    public function removeItem(\AppBundle\Entity\MercadoPagoCupon\MercadoPagoCuponItem $item)
    {
        $this->items->removeElement($item);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set estadoActual
     *
     * @param \AppBundle\Entity\MercadoPagoCupon\MercadoPagoCuponEstado $estadoActual
     *
     * @return MercadoPagoCupon
     */
    public function setEstadoActual(\AppBundle\Entity\MercadoPagoCupon\MercadoPagoCuponEstado $estadoActual = null)
    {
        $this->estadoActual = $estadoActual;

        return $this;
    }

    /**
     * Get estadoActual
     *
     * @return \AppBundle\Entity\MercadoPagoCupon\MercadoPagoCuponEstado
     */
    public function getEstadoActual()
    {
        return $this->estadoActual;
    }

    /**
     * @param \AppBundle\Entity\MercadoPagoCupon\MercadoPagoCuponEstado $estado
     * @return MercadoPagoCupon
     */
    public function addEstado(\AppBundle\Entity\MercadoPagoCupon\MercadoPagoCuponEstado $estado)
    {
        $estado->setMercadoPagoCupon($this);
        $this->setEstadoActual($estado);
        $this->estados[] = $estado;

        return $this;
    }

    /**
     * Remove estado
     *
     * @param \AppBundle\Entity\MercadoPagoCupon\MercadoPagoCuponEstado $estado
     */
    public function removeEstado(\AppBundle\Entity\MercadoPagoCupon\MercadoPagoCuponEstado $estado)
    {
        $this->estados->removeElement($estado);
    }

    /**
     * Get estados
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEstados()
    {
        return $this->estados;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return MercadoPagoCupon
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set urlPrueba
     *
     * @param string $urlPrueba
     *
     * @return MercadoPagoCupon
     */
    public function setUrlPrueba($urlPrueba)
    {
        $this->urlPrueba = $urlPrueba;

        return $this;
    }

    /**
     * Get urlPrueba
     *
     * @return string
     */
    public function getUrlPrueba()
    {
        return $this->urlPrueba;
    }

    /**
     * Add movimiento
     *
     * @param \AppBundle\Entity\MercadoPagoCupon\MercadoPagoCuponMovimiento $movimiento
     *
     * @return MercadoPagoCupon
     */
    public function addMovimiento(\AppBundle\Entity\MercadoPagoCupon\MercadoPagoCuponMovimiento $movimiento)
    {
        $movimiento->setMercadoPagoCupon($this);
        $this->movimientos[] = $movimiento;

        return $this;
    }

    /**
     * Remove movimiento
     *
     * @param \AppBundle\Entity\MercadoPagoCupon\MercadoPagoCuponMovimiento $movimiento
     */
    public function removeMovimiento(\AppBundle\Entity\MercadoPagoCupon\MercadoPagoCuponMovimiento $movimiento)
    {
        $this->movimientos->removeElement($movimiento);
    }

    /**
     * Get movimientos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMovimientos()
    {
        return $this->movimientos;
    }

    /**
     * Set resumenEstadoActualPago
     *
     * @param string $resumenEstadoActualPago
     *
     * @return MercadoPagoCupon
     */
    public function setResumenEstadoActualPago($resumenEstadoActualPago)
    {
        $this->resumenEstadoActualPago = $resumenEstadoActualPago;

        return $this;
    }

    /**
     * Get resumenEstadoActualPago
     *
     * @return string
     */
    public function getResumenEstadoActualPago()
    {
        return $this->resumenEstadoActualPago;
    }

    /**
     * Set cuit
     *
     * @param string $cuit
     *
     * @return MercadoPagoCupon
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
     * Set tipoPersona
     *
     * @param integer $tipoPersona
     *
     * @return MercadoPagoCupon
     */
    public function setTipoPersona($tipoPersona)
    {
        $this->tipoPersona = $tipoPersona;

        return $this;
    }

    /**
     * Get tipoPersona
     *
     * @return integer
     */
    public function getTipoPersona()
    {
        return $this->tipoPersona;
    }

    /**
     * Set condicionIva
     *
     * @param integer $condicionIva
     *
     * @return MercadoPagoCupon
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
        return $this->condicionIva;
    }

    /**
     * Set sexo
     *
     * @param string $sexo
     *
     * @return MercadoPagoCupon
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;

        return $this;
    }

    /**
     * Get sexo
     *
     * @return string
     */
    public function getSexo()
    {
        return $this->sexo;
    }


    /**
     * Set ventaId
     *
     * @param integer $ventaId
     *
     * @return MercadoPagoCupon
     */
    public function setVentaId($ventaId)
    {
        $this->ventaId = $ventaId;

        return $this;
    }

    /**
     * Get ventaId
     *
     * @return integer
     */
    public function getVentaId()
    {
        return $this->ventaId;
    }

    /**
     * Set enviarDomicilio
     *
     * @param integer $enviarDomicilio
     *
     * @return MercadoPagoCupon
     */
    public function setEnviarDomicilio($enviarDomicilio)
    {
        $this->enviarDomicilio = $enviarDomicilio;

        return $this;
    }

    /**
     * Get enviarDomicilio
     *
     * @return integer
     */
    public function getEnviarDomicilio()
    {
        return $this->enviarDomicilio;
    }

    /**
     * Set envioTarifaId
     *
     * @param integer $envioTarifaId
     *
     * @return MercadoPagoCupon
     */
    public function setEnvioTarifaId($envioTarifaId)
    {
        $this->envioTarifaId = $envioTarifaId;

        return $this;
    }

    /**
     * Get envioTarifaId
     *
     * @return integer
     */
    public function getEnvioTarifaId()
    {
        return $this->envioTarifaId;
    }

    /**
     * Set idUsuario
     *
     * @param integer $idUsuario
     *
     * @return MercadoPagoCupon
     */
    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario
     *
     * @return integer
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }
}
