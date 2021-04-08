<?php

namespace AppBundle\Entity\Red;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Entity as Entity;

/**
 * @ORM\Table("dbo.red_elemento")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Red\ElementoRepository")
 * @Gedmo\Tree(type="nested")
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(name="codigo", type="string", length=255)
     */
    private $codigo;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Red\Topologia\ElementoTipo", inversedBy="elemento")
     */
    private $elementoTipo;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Red\Topologia\ElementoGeoreferencia", orphanRemoval=true, mappedBy="elemento", cascade={"persist", "remove"})
     */
    private $georeferencias;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Red\Topologia\ElementoAtributo", orphanRemoval=true, mappedBy="elemento", cascade={"persist", "remove"})
     */
    private $atributos;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Red\Elemento\Estado",cascade={"persist"})
     * @ORM\JoinColumn(name="estado_actual_id", referencedColumnName="id")
     */
    private $estadoActual;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Red\Elemento\Estado", mappedBy="elemento")
     */
    private $estados;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Red\Obra\Elemento", mappedBy="elemento")
     */
    private $obra;


    /**
     * @var integer
     *
     * @Gedmo\TreeLeft
     *
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @var integer
     *
     * @Gedmo\TreeLevel
     *
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @var integer
     *
     * @Gedmo\TreeRight
     *
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeParent
     *
     * @ORM\ManyToOne(targetEntity="Elemento", inversedBy="children")
     * @ORM\JoinColumn(nullable=true)
     * @ORM\OrderBy({"nombre" = "ASC"})
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Elemento", mappedBy="parent" , cascade = {"persist"})
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="fecha_carga", type="datetime")
     */
    private $fechaCarga;

    public function setCodigo()
    {
      $codigoString = "";
      $codigoArray = $this->getCodigoArray();
      $i = 1;

      foreach($codigoArray as $co){
          $codigoString .= $co["id"];

          if ($i < count($codigoArray)) {
            $codigoString .= '.';
          }

          $i++;
      }

      $this->codigo = $codigoString;

      foreach ($this->getChildren() as $child) {
        $child->setCodigo();
      }

      return $this;
    }

    /**
     * @ORM\PreFlush
     */
    public function postFlush()
    {
      $this->setCodigo();
    }

    public function getCodigoArray()
    {
      // si es un elemento creado traigo el código
      //if ($this->getId()) {

        return $this->getParent() ?
            array_merge(
              $this->getParent()->getCodigoArray(),
              [[
                "id" => $this->getNombre(),
                "latitud"  => count($this->getGeoreferencias()) > 0 ? $this->getGeoreferencias()->last()->getLatitud() : "",
                "longitud"  => count($this->getGeoreferencias()) > 0 ? $this->getGeoreferencias()->last()->getLongitud() : "",
              ]]
            )
          :
            [[
              "id" => $this->getNombre(),
              "latitud"  => count($this->getGeoreferencias()) > 0 ? $this->getGeoreferencias()->last()->getLatitud() : "",
              "longitud"  => count($this->getGeoreferencias()) > 0 ? $this->getGeoreferencias()->last()->getLongitud() : "",
             ]]
        ;
      //}

      return [];
    }

    public function getShortArray()
    {
      $elemento["id"] = $this->getId();
      $elemento["arrayCodigo"] = $this->getCodigoArray();
      $elemento["codigo"] = $this->getCodigo();
      $elemento["nombre"] = $this->getNombre();
      $ea = $this->getEstadoActual();
      $elemento["estadoActual"] =   $ea ? $ea->getArray() : [];

    }

    public function getElementoArray()
    {

      $elemento = [];
      $elemento["id"] = $this->getId();
      $elemento["value"] = $this->getId();
      $elemento["nombre"] = $this->getNombre();

      $elemento["elementoTipo"] =  [
        "colorHexa" => $this->getElementoTipo()->getColorHexa(),
        "id"=> $this->getElementoTipo()->getId(),
        "nombre"=> $this->getElementoTipo()->getNombre(),
        "tipoGeoreferencia"=>"punto"
     ];

     $elemento["estadoActual"] = [
       "id"=>  $this->getEstadoActual()->getId(),
       "estado"=>  $this->getEstadoActual()->getEstado()
     ];

      $elemento["estadoActual"] =  $this->getEstadoActual() ? $this->getEstadoActual()->getArray() : [];

      $elemento["arrayCodigo"] = $this->getCodigoArray();
      $elemento["codigo"] = $this->getCodigo();
      $elemento["label"]  = $elemento["codigo"];

      $elemento["color"] = $this->getElementoTipo()->getColorHexa();

      $elemento["svgPath"] = $this->getElementoTipo()->getSvgPath();

      $elemento["parent"] = $this->getParent() ? $this->getParent()->getElementoArray() : null ;
      $elemento["lvl"] = $this->getLvl() ? $this->getLvl() : 0 ;
      $elemento["rgt"] = $this->getRgt() ? $this->getRgt()  : 0;
      $elemento["lft"] = $this->getLft() ? $this->getLft() : 0;

      $elemento["resumen"] = [
        "categoria" => "Red",
        "elementos" => [[
          "id"          => $this->getElementoTipo()->getId(),
          "descripcion" => $this->getElementoTipo()->getNombre(),
          "estado"      => $this->getEstadoActual()->getEstado(),
          "cantidad"    => 1,
          "unidad"      => ""
        ]]
      ];

      $elemento["tipoElemento"] = [
        "id"     => $this->getElementoTipo()->getId(),
        "value"  => $this->getElementoTipo()->getId(),
        "colorHexa"  => $this->getElementoTipo()->getColorHexa(),
        "nombre" => $this->getElementoTipo()->getNombre(),
        "label" => $this->getElementoTipo()->getNombre(),
        "tipoGeoreferencia" => $this->getElementoTipo()->getTipoGeoreferencia(),
      ];


      if ($this->getGeoreferencias()->last()) {
        $elemento["lastGeoreferencias"] = [
          "lat" => (float)$this->getGeoreferencias()->last()->getLatitud(),
          "lng" => (float)$this->getGeoreferencias()->last()->getLongitud()
        ];
      }

      $i = 0;
      foreach ($this->getGeoreferencias() as $georeferencia) {
        $elemento["georeferencias"][$i] =  [
          "lat" => (float)$georeferencia->getLatitud(),
          "lng" => (float)$georeferencia->getLongitud()
        ];

        $elemento["georeferenciasShort"][$i] =  [
          (float)$georeferencia->getLatitud(),
          (float)$georeferencia->getLongitud()
        ];

        $i++;
      }

      $i = 0;
      foreach ($this->getAtributos() as $atributo) {

        $elemento["atributos"][$i]["id"] = $atributo->getElementoTipoAtributo()->getId();
        $elemento["atributos"][$i]["nombre"] = $atributo->getElementoTipoAtributo()->getNombre();
        $elemento["atributos"][$i]["value"] = $atributo->getValor();

        $i++;
      }

      return $elemento;
    }


    public function __construct()
    {
        $this->georeferencias = new \Doctrine\Common\Collections\ArrayCollection();
        $this->atributos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->estados = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();

        if (!$this->getId()) {
          $this->addEstado(new Entity\Red\Elemento\Estado());
        }

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
     * @return Elemento
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
     * Set elementoTipo
     *
     * @param \AppBundle\Entity\Red\Topologia\ElementoTipo $elementoTipo
     *
     * @return Elemento
     */
    public function setElementoTipo(\AppBundle\Entity\Red\Topologia\ElementoTipo $elementoTipo = null)
    {
        $this->elementoTipo = $elementoTipo;

        return $this;
    }

    /**
     * Get elementoTipo
     *
     * @return \AppBundle\Entity\Red\Topologia\ElementoTipo
     */
    public function getElementoTipo()
    {
        return $this->elementoTipo;
    }

    /**
     * Add georeferencia
     *
     * @param \AppBundle\Entity\Red\Topologia\ElementoGeoreferencia $georeferencia
     *
     * @return Elemento
     */
    public function addGeoreferencia(\AppBundle\Entity\Red\Topologia\ElementoGeoreferencia $georeferencia)
    {
        $georeferencia->setElemento($this);
        $this->georeferencias[] = $georeferencia;

        return $this;
    }

    /**
     * Remove georeferencia
     *
     * @param \AppBundle\Entity\Red\Topologia\ElementoGeoreferencia $georeferencia
     */
    public function removeGeoreferencia(\AppBundle\Entity\Red\Topologia\ElementoGeoreferencia $georeferencia)
    {
        $this->georeferencias->removeElement($georeferencia);
    }

    /**
     * Get georeferencias
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGeoreferencias()
    {
        return $this->georeferencias;
    }

    /**
     * Add atributo
     *
     * @param \AppBundle\Entity\Red\Topologia\ElementoAtributo $atributo
     *
     * @return Elemento
     */
    public function addAtributo(\AppBundle\Entity\Red\Topologia\ElementoAtributo $atributo)
    {
        $atributo->setElemento($this);
        $this->atributos[] = $atributo;

        return $this;
    }

    /**
     * Remove atributo
     *
     * @param \AppBundle\Entity\Red\Topologia\ElementoAtributo $atributo
     */
    public function removeAtributo(\AppBundle\Entity\Red\Topologia\ElementoAtributo $atributo)
    {
        $this->atributos->removeElement($atributo);
    }

    /**
     * Get atributos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAtributos()
    {
        return $this->atributos;
    }

    /**
     * Set lft
     *
     * @param integer $lft
     *
     * @return Elemento
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lft
     *
     * @return integer
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set lvl
     *
     * @param integer $lvl
     *
     * @return Elemento
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * Get lvl
     *
     * @return integer
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Set rgt
     *
     * @param integer $rgt
     *
     * @return Elemento
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Get rgt
     *
     * @return integer
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set parent
     *
     * @param \AppBundle\Entity\Red\Topologia\Elemento $parent
     *
     * @return Elemento
     */
    public function setParent(\AppBundle\Entity\Red\Elemento $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\Red\Elemento
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add child
     *
     * @param \AppBundle\Entity\Red\Elemento $child
     *
     * @return Elemento
     */
    public function addChild(\AppBundle\Entity\Red\Elemento $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\Red\Elemento $child
     */
    public function removeChild(\AppBundle\Entity\Red\Elemento $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
      return $this->children;
    }

    public function getCodigo()
    {
      return $this->codigo;
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


    /**
     * Set estadoActual
     *
     * @param \AppBundle\Entity\Red\Elemento\Estado $estadoActual
     *
     * @return Elemento
     */
    public function setEstadoActual(\AppBundle\Entity\Red\Elemento\Estado $estadoActual = null)
    {
        $this->estadoActual = $estadoActual;

        return $this;
    }

    /**
     * Get estadoActual
     *
     * @return \AppBundle\Entity\Red\Elemento\Estado
     */
    public function getEstadoActual()
    {
        return $this->estadoActual;
    }

    /**
     * Add estado
     *
     * @param \AppBundle\Entity\Red\Elemento\Estado $estado
     *
     * @return Elemento
     */
    public function addEstado(\AppBundle\Entity\Red\Elemento\Estado $estado)
    {
        $estado->setElemento($this);
        $this->setEstadoActual($estado);
        $this->estados[] = $estado;

        return $this;
    }

    /**
     * Remove estado
     *
     * @param \AppBundle\Entity\Red\Elemento\Estado $estado
     */
    public function removeEstado(\AppBundle\Entity\Red\Elemento\Estado $estado)
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
     * Set obra
     *
     * @param \AppBundle\Entity\Red\Obra\Elemento $obra
     *
     * @return Elemento
     */
    public function setObra(\AppBundle\Entity\Red\Obra\Elemento $obra = null)
    {
        $this->obra = $obra;

        return $this;
    }

    /**
     * Get obra
     *
     * @return \AppBundle\Entity\Red\Obra\Elemento
     */
    public function getObra()
    {
        return $this->obra;
    }
}
