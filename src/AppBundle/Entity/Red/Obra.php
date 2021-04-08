<?php

namespace AppBundle\Entity\Red;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table("dbo.obras")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Red\ObraRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Obra
{
  const ESTADO_PENDIENTE = 3;
  const ESTADO_DETENIDA = 4;
  const ESTADO_COMPLETA = 1;
  const ESTADO_EN_EJECUCION = 5;

  public static $estados = [
    "Pendiente" => self::ESTADO_PENDIENTE,
    "Detenida" => self::ESTADO_DETENIDA,
    "Completa" => self::ESTADO_COMPLETA,
    "En ejecución" => self::ESTADO_EN_EJECUCION,
  ];

    /**
     * @ORM\Column(name="id_obra", type="decimal", precision=18, scale=0)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="comentario", type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(name="fecha_carga", type="datetime")
     */
    private $fechaCarga;

    /**
     * @ORM\Column(name="fecha_inicio_aprox", type="datetime")
     */
    private $fechaInicioEstimada;

    /**
     * @ORM\Column(name="fecha_fin_aprox", type="datetime")
     */
    private $fechaFinEstimada;


    /**
     * @ORM\Column(name="fecha_inicio", type="datetime")
     */
    private $fechaInicio;

    /**
     * @ORM\Column(name="fecha_fin", type="datetime")
     */
    private $fechaFin;

    /**
     * @ORM\Column(name="tipo_red", type="string", length=100)
     */
    private $tipoRed;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Red\Obra\Elemento", orphanRemoval=true, mappedBy="obra", cascade={"persist", "remove"})
     */
    private $elementos;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Red\Obra\Tarea", orphanRemoval=true, mappedBy="obra", cascade={"persist", "remove"})
     */
    private $tareas;

    /**
     * @ORM\Column(name="avancePorcentaje", type="integer")
     */
    private $avance;

    /**
     * @ORM\Column(name="estado", type="integer")
     */
    private $estado;

    public function getAvance()
    {
      return $this->avance;
    }

    public function setAvance()
    {
      // cantidad de elementos que tiene asignado la obra
      $cantidad = count($this->getElementos());
      $avanceAcumulado = count($this->getElementos()->filter(function($e){
        $eea = $e->getElemento()->getEstadoActual();
        return $eea->isIluminado() || $eea->isFusionado();
      }));

      $this->avance = $cantidad > 0 ? number_format((($avanceAcumulado/$cantidad)*100),2) : 0;
    }

    public function getAvanceTarea()
    {
      return count($this->getTareas()->filter(function($t){
        return $t->getAvance() == 100;
      }));
    }

    public function __construct()
    {
      $this->tipoRed = 'FTTH';

      $this->elementos = new \Doctrine\Common\Collections\ArrayCollection();
      $this->tareas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getArrayShort()
    {
      $obra = [];

      $obra["id"] = $this->getId();
      $obra["nombre"] = $this->getNombre();

      $obra["estado"] = $this->getEstado();
      $obra["estadoNombre"] = $this->getEstadoNombre();
      $obra["avance"] = $this->getAvance();
      $obra["avanceTarea"] = $this->getAvanceTarea();
      $obra["tareas"] = count($this->getTareas());

      return $obra;
    }

    public function getArray()
    {
      $obra = [];

      $obra["id"] = $this->getId();
      $obra["nombre"] = $this->getNombre();

      $obra["fechaInicioEstimada"] = !$this->getFechaInicioEstimada() ? '' : $this->getFechaInicioEstimada()->format("Y-m-d");
      $obra["fechaFinEstimada"] = !$this->getFechaFinEstimada()  ? '' : $this->getFechaFinEstimada()->format("Y-m-d");
      $obra["fechaInicio"] = !$this->getFechaInicio() ? '' : $this->getFechaInicio()->format("Y-m-d");
      $obra["fechaFin"] = !$this->getFechaFin()  ? '' : $this->getFechaFin()->format("Y-m-d");
      $obra["tareas"] = !$this->getTareas()  ? [] : $this->getTareasArray();

      $obra["estado"] = $this->getEstado();
      $obra["estadoNombre"] = $this->getEstadoNombre();
      $obra["avance"] = $this->getAvance();
      $obra["avanceTarea"] = $this->getAvanceTarea();

      return $obra;
    }

    /*public function getElementosArray()
    {
      $elementos = [];

      if ($this->getElementos()) {
        foreach ($this->getElementos() as $elemento) {
          $elementos[] = $elemento->getArray();
        }
      }

      return $elementos;
    }*/

    public function getTareasArray()
    {
      $tareas = [];

      foreach ($this->getTareas() as $tarea) {
        $tareas[] = $tarea->getArrayShort();
      }

      return $tareas;
    }

    public function getEstadoNombre()
    {
       $estados = array_flip(self::$estados);
       return array_key_exists($this->estado, $estados) ? $estados[$this->estado] : "indefinido";
    }

    public function setEstado($estado)
    {
       $estados = array_flip(self::$estados);
       $this->estado = array_key_exists($estado, $estados) ? $estado : $this->estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return integer
     */
    public function getEstado()
    {
        return $this->estado;
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
     *
     * @return Obra
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
        return utf8_encode($this->nombre);
    }

    /**
     * Set fechaCarga
     *
     * @param \DateTime $fechaCarga
     *
     * @return Obra
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

    /**
     * Set fechaInicioEstimada
     *
     * @param \DateTime $fechaInicioEstimada
     *
     * @return Obra
     */
    public function setFechaInicioEstimada($fechaInicioEstimada)
    {
        $this->fechaInicioEstimada = $fechaInicioEstimada;

        return $this;
    }

    /**
     * Get fechaInicioEstimada
     *
     * @return \DateTime
     */
    public function getFechaInicioEstimada()
    {
        return $this->fechaInicioEstimada;
    }

    /**
     * Set fechaFinEstimada
     *
     * @param \DateTime $fechaFinEstimada
     *
     * @return Obra
     */
    public function setFechaFinEstimada($fechaFinEstimada)
    {
        $this->fechaFinEstimada = $fechaFinEstimada;

        return $this;
    }

    /**
     * Get fechaFinEstimada
     *
     * @return \DateTime
     */
    public function getFechaFinEstimada()
    {
        return $this->fechaFinEstimada;
    }

    /**
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     *
     * @return Obra
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * Set fechaFin
     *
     * @param \DateTime $fechaFin
     *
     * @return Obra
     */
    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;

        return $this;
    }

    /**
     * Get fechaFin
     *
     * @return \DateTime
     */
    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    /**
     * Set tipoRed
     *
     * @param string $tipoRed
     *
     * @return Obra
     */
    public function setTipoRed($tipoRed)
    {
        $this->tipoRed = $tipoRed;

        return $this;
    }

    /**
     * Get tipoRed
     *
     * @return string
     */
    public function getTipoRed()
    {
        return $this->tipoRed;
    }

    /**
     * Add elemento
     *
     * @param \AppBundle\Entity\Red\Obra\Elemento $elemento
     *
     * @return Obra
     */
    public function addElemento(\AppBundle\Entity\Red\Obra\Elemento $elemento)
    {
        $elemento->setObra($this);
        $this->elementos[] = $elemento;

        return $this;
    }

    /**
     * Remove elemento
     *
     * @param \AppBundle\Entity\Red\Obra\Elemento $elemento
     */
    public function removeElemento(\AppBundle\Entity\Red\Obra\Elemento $elemento)
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
     * Add tarea
     *
     * @param \AppBundle\Entity\Red\Obra\Tarea $tarea
     *
     * @return Obra
     */
    public function addTarea(\AppBundle\Entity\Red\Obra\Tarea $tarea)
    {
        $this->tareas[] = $tarea;

        return $this;
    }

    /**
     * Remove tarea
     *
     * @param \AppBundle\Entity\Red\Obra\Tarea $tarea
     */
    public function removeTarea(\AppBundle\Entity\Red\Obra\Tarea $tarea)
    {
        $this->tareas->removeElement($tarea);
    }

    /**
     * Get tareas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTareas()
    {
        return $this->tareas;
    }
}
