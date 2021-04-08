<?php

namespace AppBundle\Entity\Red\Topologia;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("dbo.red_elemento_tipo_atributo")
 * @ORM\Entity()
 */
class ElementoTipoAtributo
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Red\Topologia\ElementoTipo", inversedBy="atributos")
     * @ORM\JoinColumn(name="tipo_elemento_id", referencedColumnName="id")
     */
    private $elementoTipo;

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
     * @return ElementoTipoAtributo
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
     * @return ElementoTipoAtributo
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
}
