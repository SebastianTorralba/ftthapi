<?php

namespace AppBundle\Entity\Red\Obra;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table("dbo.obras_red_elemento")
 * @ORM\Entity()
 */
class Elemento
{
    /**
     * @ORM\Column(name="id", type="decimal", precision=18, scale=0)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Red\Obra", inversedBy="elementos")
     * @ORM\JoinColumn(name="obra_id", referencedColumnName="id_obra")
     */
    private $obra;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Red\Elemento", inversedBy="obra")
     * @ORM\JoinColumn(name="elemento_id", referencedColumnName="id")
     */
    private $elemento;

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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Red\Obra\TareaElemento", orphanRemoval=true, mappedBy="elemento", cascade={"persist"})
     */
    private $tareas;

    public function getArray()
    {
      $obra = [];

      $obra["id"] = $this->getId();
      $obra["elemento"] = $this->getElemento()->getElementoArray();

      return $obra;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
     * @return Elemento
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
     * Set elemento
     *
     * @param \AppBundle\Entity\Red\Elemento $elemento
     *
     * @return Elemento
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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tareas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add tarea
     *
     * @param \AppBundle\Entity\Red\Obra\Tarea $tarea
     *
     * @return Elemento
     */
    public function addTarea(\AppBundle\Entity\Red\Obra\TareaElemento $tarea)
    {
        $elemento->setElemento($this);
        $this->tareas[] = $tarea;

        return $this;
    }

    /**
     * Remove tarea
     *
     * @param \AppBundle\Entity\Red\Obra\Tarea $tarea
     */
    public function removeTarea(\AppBundle\Entity\Red\Obra\TareaElemento $tarea)
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
