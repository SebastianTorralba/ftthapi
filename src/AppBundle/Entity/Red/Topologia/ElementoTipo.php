<?php

namespace AppBundle\Entity\Red\Topologia;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("dbo.red_elemento_tipo")
 * @ORM\Entity()
 */
class ElementoTipo
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
     * @ORM\Column(name="svg_path", type="string", length=255)
     */
    private $svgPath;

    /**
     * @ORM\Column(name="color_hexa", type="string", length=255)
     */
    private $colorHexa;

    /**
     * @ORM\Column(name="tipo_georeferencia", type="string", length=255)
     */
    private $tipoGeoreferencia;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Red\Topologia\ElementoTipoAtributo", mappedBy="elementoTipo")
     */
    private $atributos;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Red\Elemento", mappedBy="elementoTipo")
     */
    private $elemento;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->atributos = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return ElementoTipo
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
     * Set tipoGeoreferencia
     *
     * @param string $tipoGeoreferencia
     *
     * @return ElementoTipo
     */
    public function setTipoGeoreferencia($tipoGeoreferencia)
    {
        $this->tipoGeoreferencia = $tipoGeoreferencia;

        return $this;
    }

    /**
     * Get tipoGeoreferencia
     *
     * @return string
     */
    public function getTipoGeoreferencia()
    {
        return $this->tipoGeoreferencia;
    }

    /**
     * Add atributo
     *
     * @param \AppBundle\Entity\Red\Topologia\ElementoTipoAtributo $atributo
     *
     * @return ElementoTipo
     */
    public function addAtributo(\AppBundle\Entity\Red\Topologia\ElementoTipoAtributo $atributo)
    {
        $this->atributos[] = $atributo;

        return $this;
    }

    /**
     * Remove atributo
     *
     * @param \AppBundle\Entity\Red\Topologia\ElementoTipoAtributo $atributo
     */
    public function removeAtributo(\AppBundle\Entity\Red\Topologia\ElementoTipoAtributo $atributo)
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
     * Set svgPath
     *
     * @param string $svgPath
     *
     * @return ElementoTipo
     */
    public function setSvgPath($svgPath)
    {
        $this->svgPath = $svgPath;

        return $this;
    }

    /**
     * Get svgPath
     *
     * @return string
     */
    public function getSvgPath()
    {
        return utf8_encode($this->svgPath);
    }

    /**
     * Set colorHexa
     *
     * @param string $colorHexa
     *
     * @return ElementoTipo
     */
    public function setColorHexa($colorHexa)
    {
        $this->colorHexa = $colorHexa;

        return $this;
    }

    /**
     * Get colorHexa
     *
     * @return string
     */
    public function getColorHexa()
    {
        return $this->colorHexa;
    }

    /**
     * Add elemento
     *
     * @param \AppBundle\Entity\Red\Elemento $elemento
     *
     * @return ElementoTipo
     */
    public function addElemento(\AppBundle\Entity\Red\Elemento $elemento)
    {
        $this->elemento[] = $elemento;

        return $this;
    }

    /**
     * Remove elemento
     *
     * @param \AppBundle\Entity\Red\Elemento $elemento
     */
    public function removeElemento(\AppBundle\Entity\Red\Elemento $elemento)
    {
        $this->elemento->removeElement($elemento);
    }

    /**
     * Get elemento
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getElemento()
    {
        return $this->elemento;
    }
}
