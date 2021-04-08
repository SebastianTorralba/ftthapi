<?php

namespace AppBundle\Entity\Red\Elemento;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity as Entity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table("dbo.red_elemento_estado")
 * @ORM\Entity()
 */
class Estado
{

  const ESTADO_PLANIFICADO = "PLANIFICADO";
  const ESTADO_EN_OBRA = "EN_OBRA";
  const ESTADO_EN_OBRA_CON_TAREA = "EN_OBRA_CON_TAREA";
  const ESTADO_INSTALADO = "INSTALADO";
  const ESTADO_FUSIONADO = "FUSIONADO";
  const ESTADO_ILUMINADO = "ILUMINADO";

  public static $estados = [
    "PLANIFICADO" => self::ESTADO_PLANIFICADO,
    "EN OBRA" => self::ESTADO_EN_OBRA,
    "EN OBRA CON TAREA" => self::ESTADO_EN_OBRA_CON_TAREA,
    "INSTALADO" => self::ESTADO_INSTALADO,
    "FUSIONADO" => self::ESTADO_FUSIONADO,
    "ILUMINADO" => self::ESTADO_ILUMINADO,
  ];

  public static $estadosColores = [
    "#000"    => self::ESTADO_PLANIFICADO,
    "#ccc"    => self::ESTADO_EN_OBRA,
    "#fff200" => self::ESTADO_EN_OBRA_CON_TAREA,
    "#ff9000" => self::ESTADO_INSTALADO,
    "#397f31" => self::ESTADO_FUSIONADO,
    "#51ff0c" => self::ESTADO_ILUMINADO,
  ];

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
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="fecha_hora", type="datetime")
     */
    private $fechaHora;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id_usuario")
     */
    private $creadaPor;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Red\Elemento", inversedBy="estados", cascade={"persist"})
     * @ORM\JoinColumn(name="elemento_id", referencedColumnName="id")
     */
    private $elemento;

    public function isFusionado()
    {
      return $this->getEstado() == self::ESTADO_FUSIONADO;
    }

    public function isIluminado()
    {
      return $this->getEstado() == self::ESTADO_ILUMINADO;
    }

    public function getArray()
    {
      return [
        "estado"       => $this->getEstado(),
        "estadoNombre" => $this->getEstadoNombre(),
        "estadoColor"  => $this->getColorFromEstado(),
        "fechaHora"    => $this->getFechaHora() ? $this->getFechaHora()->format("Y-m-d H:i:s") : "",
      ];
    }

    public function __construct($estado = self::ESTADO_PLANIFICADO)
    {
      $this->estado    = $estado;
      $this->fechaHora = new \DateTime('NOW');
    }

    public function getColorFromEstado()
    {
      $estados = array_flip(self::$estados);
      return isset($estados[$this->estado]) ? $estados[$this->estado] : "#999";
    }

    public function getEstadoNombre()
    {
      $estados = array_flip(self::$estados);
      return isset($estados[$this->estado]) ? $estados[$this->estado] : "s/n";
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
     * @return Estado
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


    public function setFechaHora($fechaHora)
    {
        $this->fechaHora = $fechaHora;

        return $this;
    }

    public function getFechaHora()
    {
        return $this->fechaHora;
    }

    public function setCreadaPor(\AppBundle\Entity\Usuario $creadaPor = null)
    {
        $this->creadaPor = $creadaPor;

        return $this;
    }

    public function getCreadaPor()
    {
        return $this->creadaPor;
    }

    /**
     * Set elemento
     *
     * @param \AppBundle\Entity\Red\Elemento $elemento
     *
     * @return Estado
     */
    public function setElemento(\AppBundle\Entity\Red\Elemento $elemento = null)
    {
        $this->elemento = $elemento;

        return $this;
    }

    /**
     * Get elemento
     *
     * @return \AppBundle\Entity\Red\Elemento
     */
    public function getElemento()
    {
        return $this->elemento;
    }
}
