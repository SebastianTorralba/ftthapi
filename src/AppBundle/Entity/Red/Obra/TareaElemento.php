<?php

namespace AppBundle\Entity\Red\Obra;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table("dbo.obras_tarea_elemento")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Red\Obra\TareaElementoRepository")
 */
class TareaElemento
{
  const ESTADO_PENDIENTE = "PENDIENTE";
  const ESTADO_FINALIZADA = "FINALIZADA";

  public static $estados = [
    "PENDIENTE" => self::ESTADO_PENDIENTE,
    "FINALIZADA" => self::ESTADO_FINALIZADA,
  ];

  /**
   * @ORM\Column(name="id", type="decimal", precision=18, scale=0)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Red\Obra\Tarea", inversedBy="elementos", cascade={"persist"})
   */
  private $tarea;

  /**
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Red\Obra\Elemento", inversedBy="tareas", cascade={"persist"})
   */
  private $elemento;

  /**
   * @ORM\Column(name="estado", type="string", length=250)
   */
  private $estado;

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
   * @ORM\Column(name="fecha_finalizada", type="datetime", nullable=true)
   */
  private $fechaFinalizada;

  /**
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario")
   * @ORM\JoinColumn(name="id_usuario_finalizo", referencedColumnName="id_usuario")
   */
  private $finalizadaPor;

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

  public function setFechaFinalizada($fechaFinalizada)
  {
      $this->fechaFinalizada = $fechaFinalizada;

      return $this;
  }

  public function getFechaFinalizada()
  {
      return $this->fechaFinalizada;
  }

  public function setFinalizadaPor(\AppBundle\Entity\Usuario $finalizadaPor = null)
  {
      $this->finalizadaPor = $finalizadaPor;

      return $this;
  }

  public function getFinalizadaPor()
  {
      return $this->finalizadaPor;
  }

  public function __construct()
  {
    $this->estado = self::ESTADO_PENDIENTE;
  }

  public function isFinalizada() {
    return $this->getEstado() == self::ESTADO_FINALIZADA;
  }

  public function getEstadoNombre()
  {
    $estados = array_flip(self::$estados);
    return isset($estados[$this->estado]) ? $estados[$this->estado] : "s/n";
  }

  /**
   * Set tarea
   *
   * @param \AppBundle\Entity\Red\Obra\Tarea $tarea
   *
   * @return TareaElemento
   */
  public function setTarea(\AppBundle\Entity\Red\Obra\Tarea $tarea = null)
  {
      $this->tarea = $tarea;

      return $this;
  }

  /**
   * Get tarea
   *
   * @return \AppBundle\Entity\Red\Obra\Tarea
   */
  public function getTarea()
  {
      return $this->tarea;
  }

  /**
   * Set elemento
   *
   * @param \AppBundle\Entity\Red\Obra\Elemento $elemento
   *
   * @return TareaElemento
   */
  public function setElemento(\AppBundle\Entity\Red\Obra\Elemento $elemento = null)
  {
      $this->elemento = $elemento;

      return $this;
  }

  /**
   * Get elemento
   *
   * @return \AppBundle\Entity\Red\Obra\Elemento
   */
  public function getElemento()
  {
      return $this->elemento;
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
     * @return TareaElemento
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
}
