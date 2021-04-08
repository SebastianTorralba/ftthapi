<?php

namespace AppBundle\Entity\Red\Obra;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table("dbo.obras_tarea")
 * @ORM\Entity()
 */
class Tarea
{
    const TIPO_INSTALACION = "INSTALACION";
    const TIPO_FUSION = "FUSION";
    const TIPO_INSTALACION_FUSION = "INSTALACION_FUSION";

    public static $tipos = [
      "Instalación" => self::TIPO_INSTALACION,
      "Fusión"  => self::TIPO_FUSION,
      "Instalación y fusión" => self::TIPO_INSTALACION_FUSION,
    ];

    /**
     * @ORM\Column(name="id", type="decimal", precision=18, scale=0)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Red\Obra", inversedBy="tareas")
     * @ORM\JoinColumn(name="obra_id", referencedColumnName="id_obra")
     */
    private $obra;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Cuadrilla")
     * @ORM\JoinColumn(name="cuadrilla_id", referencedColumnName="id_cuadrilla")
     */
    private $cuadrilla;

    /**
     * @ORM\Column(name="tipo", type="string", length=50)
     */
    private $tipo;

    /**
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="fecha_carga", type="datetime")
     */
    private $fechaCarga;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id_usuario")
     */
    private $creadaPor;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Red\Obra\TareaElemento", orphanRemoval=true, mappedBy="tarea", cascade={"persist"})
     */
    private $elementos;

    /**
     * @ORM\Column(name="observacion", type="text")
     */
    private $observacion;

    public function getArrayShort()
    {
      $tarea = [];

      $tipos = array_flip(self::$tipos);

      $tarea["id"] = $this->getId();
      $tarea["fecha"] = !$this->getFecha() ? '' : $this->getFecha()->format("Y-m-d");
      $tarea["fechaFormat"] = !$this->getFecha() ? '' : $this->getFecha()->format("d/m/Y");
      $tarea["obra"] = ["id" => $this->getObra()->getId(), "nombre" => $this->getObra()->getNombre()];
      $tarea["cuadrilla"] = !$this->getCuadrilla() ? [] : $this->getCuadrilla()->getArray();
      $tarea["tipo"] = $this->getTipo() ? ["tipo" => $this->getTipo(), "label" => $this->getTipo() ? $tipos[$this->getTipo()] : "" , "value" => $this->getTipo()] : [];
      $tarea["avance"] = $this->getAvance();

      return $tarea;
    }

    public function getArray()
    {
      $tarea = [];

      $tipos = array_flip(self::$tipos);

      $tarea["id"] = $this->getId();
      $tarea["fecha"] = !$this->getFecha() ? '' : $this->getFecha()->format("Y-m-d");
      $tarea["fechaFormat"] = !$this->getFecha() ? '' : $this->getFecha()->format("d/m/Y");
      $tarea["obra"] = ["id" => $this->getObra()->getId(), "nombre" => $this->getObra()->getNombre()];
      $tarea["cuadrilla"] = !$this->getCuadrilla() ? [] : $this->getCuadrilla()->getArray();
      $tarea["tipo"] = $this->getTipo() ? ["tipo" => $this->getTipo(), "label" => $this->getTipo() ? $tipos[$this->getTipo()] : "" , "value" => $this->getTipo()] : [];
      $tarea["observacion"] = $this->getObservacion();
      $tarea["avance"] = $this->getAvance();
      $tarea["elementos"] = $this->getElementosArray();

      return $tarea;
    }

    public function elementosFinalizados()
    {
      $cantidad = 0;

      foreach ($this->getElementos() as $elemento) {

        if ($elemento->isFinalizada()) {
          $cantidad++;
        }
      }

      return $cantidad;
    }

    public function getAvance()
    {
      $elementosFinalizados = 0;

      if (count($this->getElementos())) {
        foreach ($this->getElementos() as $elemento) {

          if ($elemento->isFinalizada()) {
            $elementosFinalizados++;
          }
        }

        return number_format((($elementosFinalizados*100)/count($this->getElementos())),2);

      } else {
        return 0;
      }

    }

    public function getElementosArray()
    {
      $elementos = [];

      if ($this->getElementos()) {
        foreach ($this->getElementos() as $elemento) {
          $elementos[] = array_merge(
            $elemento->getElemento()->getArray(),
            [
              "tareaElementoId" => $elemento->getId(),
              "estadoTarea" => $elemento->getEstado(),
              "estadoTareaNombre" => $elemento->getEstadoNombre()
            ]
          );
        }
      }

      return $elementos;
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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Tarea
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set fechaCarga
     *
     * @param \DateTime $fechaCarga
     *
     * @return Elemento
     */
    public function setFechaCarga($fechaCarga)
    {
        $this->fechaCarga = $fechaCarga;

        return $this;
    }

    /**
     * Get fechaCarga
     *
     * @return \DateTime
     */
    public function getFechaCarga()
    {
        return $this->fechaCarga;
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
     * Set obra
     *
     * @param \AppBundle\Entity\Red\Obra $obra
     *
     * @return Tarea
     */
    public function setObra(\AppBundle\Entity\Red\Obra $obra = null)
    {
        $this->obra = $obra;

        return $this;
    }

    /**
     * Get obra
     *
     * @return \AppBundle\Entity\Red\Obra
     */
    public function getObra()
    {
        return $this->obra;
    }

    /**
     * Set cuadrilla
     *
     * @param \AppBundle\Entity\Cuadrilla $cuadrilla
     *
     * @return Tarea
     */
    public function setCuadrilla(\AppBundle\Entity\Cuadrilla $cuadrilla = null)
    {
        $this->cuadrilla = $cuadrilla;

        return $this;
    }

    /**
     * Get cuadrilla
     *
     * @return \AppBundle\Entity\Cuadrilla
     */
    public function getCuadrilla()
    {
        return $this->cuadrilla;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->elementos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add elemento
     *
     * @param \AppBundle\Entity\Red\Obra\Elemento $elemento
     *
     * @return Tarea
     */
    public function addElemento(\AppBundle\Entity\Red\Obra\TareaElemento $elemento)
    {
        $elemento->setTarea($this);
        $this->elementos[] = $elemento;

        return $this;
    }

    /**
     * Remove elemento
     *
     * @param \AppBundle\Entity\Red\Obra\Elemento $elemento
     */
    public function removeElemento(\AppBundle\Entity\Red\Obra\TareaElemento $elemento)
    {
        $this->elementos->removeElement($elemento);
    }

    /**
     * Get elementos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getElementos()
    {
        return $this->elementos;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     *
     * @return Tarea
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
     * Set observacion
     *
     * @param string $observacion
     *
     * @return Tarea
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion
     *
     * @return string
     */
    public function getObservacion()
    {
        return $this->observacion;
    }
}
